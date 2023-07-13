<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

class Pocket extends BaseController
{
    protected $db;
    protected $validation;
    protected $email;
    protected $session;
    protected $uuid;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        $this->email = \Config\Services::email();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data['totalAset'] = $this->db->table('pockets')
            ->select('sum(balance) as totalAset')
            ->where('owner', $this->session->userSession)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray()['totalAset'];


        return view('pockets/pocket', $data);
    }

    public function addPocket()
    {
        $data['name'] = trim($_POST['name']);
        $data['type'] = $_POST['type'];
        $data['icon'] = $_POST['icon'];

        $this->validation->setRules(
            [
                'name' => 'required'
            ],
            [
                'name' => [
                    'required' => 'Nama kantong wajib diisi!'
                ]
            ]
        );

        if (!$this->validation->run($data)) {
            return json_encode($this->validation->getErrors());
        }

        $totalKantong = $this->db->table('pockets')
            ->select('count(uuid) as a')
            ->where('owner', $this->session->userSession)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray()['a'];

        if ($totalKantong == 10) {
            return json_encode(['response' => 'LIMIT']);
        }

        $data['owner'] = $this->session->userSession;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $this->db->transBegin();

        $this->uuid = Uuid::uuid4();
        $data['uuid'] = $this->uuid->toString();
        $this->db->table('pockets')
            ->set($data)
            ->insert();
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return json_encode(['response' => 'FAILED']);
        } else {
            $this->db->transCommit();
            return json_encode(['response' => 'SUCCESS']);
        }
    }

    public function listPocket()
    {
        $pockets = $this->db->table('pockets')
            ->select('*')
            ->where('owner', $this->session->userSession)
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $html = '';

        foreach ($pockets as $key => $pocket) {
            $type = $pocket['type'] == 0 ? "Tunai" : "Non Tunai";
            $html .= '<div class="card mb-2 rounded-3" onclick="showPocketDetail(\'' . $pocket['uuid'] . '\',\'' . $pocket['icon'] . '\',\'' . $pocket['name'] . '\',\'Rp ' . number_format($pocket['balance'], 0, ',', '.') . '\',\'' . $type . '\')">
                    <div class="card-body py-1 d-flex justify-content-between align-items-center">
                         <span class="text-primary me-4 py-2">
                            <img src="' . base_url('public/assets/img/emojis/' . $pocket['icon'] . '') . '" width="40px">
                        </span>
                        <span class="text-end">
                            <span class="small">
                                ' . $pocket['name'] . '
                            </span>
                            <br>
                            <span class="fw-bolder">
                                Rp ' . number_format($pocket['balance'], 0, ',', '.') . '
                            </span>
                        </span>
                    </div>
                </div>
                ';
        }

        return $html;
    }

    public function detailPocket($uuid = NULL)
    {
        $data['pockets'] = $this->db->table('pockets')
            ->select('*')
            ->where('deleted_at IS NULL')
            ->where('owner', $this->session->userSession)
            ->where('uuid !=', $uuid)
            ->get()
            ->getResultArray();

        if ($uuid != null) {
            $data['pocketDetail'] = $this->db->table('pockets')
                ->select('*')
                ->where('uuid', $uuid)
                ->where('owner', $this->session->userSession)
                ->where('deleted_at IS NULL')
                ->get()
                ->getRowArray();

            return view('pockets/pocket-detail', $data);
        }
    }

    public function addBalance()
    {
        $data['pocket_id'] = $_POST['pocketUuid'];
        $data['amount'] = $_POST['balance'];
        $data['note'] = $_POST['note'];

        $data['uuid'] = Uuid::uuid4();
        $data['type'] = 0;
        $data['source_type'] = 1;
        $data['from'] = 'TOP-UP';
        $data['initiator'] = $this->session->userSession;
        $data['created_at'] = time();
        $data['updated_at'] = time();

        $this->db->transBegin();

        $this->db->table('transactions')
            ->set($data)
            ->insert();

        $currentBalance = $this->db->table('pockets')
            ->select('balance')
            ->where('uuid', $data['pocket_id'])
            ->get()
            ->getRowArray()['balance'];
        $newBalance = $currentBalance + $data['amount'];
        $this->db->table('pockets')
            ->set('balance', $newBalance)
            ->where('uuid', $data['pocket_id'])
            ->update();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return json_encode(['response' => 'FAILED']);
        } else {
            $this->db->transCommit();
            return json_encode(['response' => 'SUCCESS', 'balance' => "Rp " . number_format($newBalance, 0, ',', '.')]);
        }
    }

    public function listTransaction($pocketId)
    {
        $transactions = $this->db->table('transactions')
            ->select('*')
            ->where('pocket_id', $pocketId)
            ->where('deleted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        $html = '';

        foreach ($transactions as $key => $trx) {

            /* Transaksi Masuk */
            if ($trx['type'] == '0') {
                $trxType = 'Uang Masuk';
                $amount = "text-success\" > +Rp " . number_format($trx['amount'], 0, ',', '.');
                /* Transaksi Masuk Dari External */
                if ($trx['source_type'] == '1') {
                    $initials = explode(' ', $trx['from']);
                    $initials = array_slice($initials, 0, 2);
                    $acronym = '';
                    foreach ($initials as $initial) {
                        $firstLetter = substr($initial, 0, 1);
                        $acronym .= strtoupper($firstLetter);
                    }
                    $emoji = '<div class="rounded-circle bg-primary text-light d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;">
                            ' . $acronym . '
                        </div>';

                    $title = $trx['from'];
                }
            }
            /* Transaksi Ke Atau Dari Pocket */
            if ($trx['source_type'] == '0' || $trx['destination_type'] == '0') {
                if ($trx['from'] !== NULL) {
                    $relatedPocket = $trx['from'];
                } else {
                    $relatedPocket = $trx['to'];
                }

                $pocket = $this->db->table('pockets')
                    ->select('name, icon')
                    ->where('uuid', $relatedPocket)
                    ->get()
                    ->getRowArray();

                $emoji = '<img src=" ' . base_url('public/assets/img/emojis/' . $pocket['icon']) . '" style="width: 50px;height: 50px;" class="me-3">';
                $title = $pocket['name'];
            }

            /* Transaksi keluar */
            if ($trx['type'] == '1') {
                $trxType = 'Uang Keluar';
                $amount = "text-danger\">-Rp " . number_format($trx['amount'], 0, ',', '.');
            }

            $html .= '<div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        ' . $emoji . '
                        <span>
                            <p class="fs-6 mb-0">' . $title . '</p>
                            <p class="small mb-0 text-secondary">' . date('j M Y', $trx['created_at']) . '</p>
                        </span>
                    </div>
                    <span>
                        <p class="fs-6 fw-bold mb-0 text-end ' . $amount . '</p>
                        <p class="small mb-0 text-end text-secondary">' . $trxType . '</p>
                    </span>
                </div>';
        }
        echo $html;
    }

    public function transferBalance()
    {
        $destination = $_POST['pocketId'];
        $amount = $_POST['amount'];
        $note = $_POST['note'];
        $source = $_POST['sourcePocketId'];

        $trxOut = [
            'uuid' => Uuid::uuid4()->toString(),
            'type' => '1',
            'amount' => $amount,
            'pocket_id' => $source,
            'destination_type' => '0',
            'to' => $destination,
            'initiator' => $this->session->userSession,
            'note' => $note,
            'created_at' => time(),
            'updated_at' => time()
        ];
        $trxIn = [
            'uuid' => Uuid::uuid4()->toString(),
            'type' => '0',
            'amount' => $amount,
            'pocket_id' => $destination,
            'source_type' => '0',
            'from' => $source,
            'initiator' => $this->session->userSession,
            'note' => $note,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $this->db->transBegin();

        $newBalanceOnSource = $this->db->table('pockets')
            ->select('balance')
            ->where('uuid', $source)
            ->get()->getRowArray()['balance'] - $amount;

        $newBalanceOnDestination = $this->db->table('pockets')
            ->select('balance')
            ->where('uuid', $destination)
            ->get()->getRowArray()['balance'] + $amount;

        $this->db->table('transactions')
            ->set($trxOut)
            ->insert();
        $this->db->table('transactions')
            ->set($trxIn)
            ->insert();

        $this->db->table('pockets')
            ->set('balance', $newBalanceOnSource)
            ->where('uuid', $source)
            ->update();
        $this->db->table('pockets')
            ->set('balance', $newBalanceOnDestination)
            ->where('uuid', $destination)
            ->update();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return json_encode(['response' => 'FAILED']);
        } else {
            $this->db->transCommit();
            return json_encode(['response' => 'SUCCESS']);
        }
    }
}
