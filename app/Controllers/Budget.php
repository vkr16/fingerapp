<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Ramsey\Uuid\Uuid;

class Budget extends BaseController
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
        $data['totalAset'] = $this->db->table('budgets')
            ->select('sum(budget) as totalAset')
            ->where('owner', $this->session->userSession)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray()['totalAset'];


        return view('budgets/budget', $data);
    }

    public function addBudget()
    {
        $data['name'] = trim($_POST['name']);
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


        $data['owner'] = $this->session->userSession;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $this->db->transBegin();

        $this->uuid = Uuid::uuid4();
        $data['uuid'] = $this->uuid->toString();
        $this->db->table('budgets')
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

    public function listBudget()
    {
        $budgets = $this->db->table('budgets')
            ->select('*')
            ->where('owner', $this->session->userSession)
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $html = '';

        foreach ($budgets as $key => $budget) {
            $html .= '<div class="card mb-2 rounded-3" onclick="showBudgetDetail(\'' . $budget['uuid'] . '\',\'' . $budget['icon'] . '\',\'' . $budget['name'] . '\',\'Rp ' . number_format($budget['budget'], 0, ',', '.') . '\')">
                    <div class="card-body py-1 d-flex justify-content-between align-items-center">
                         <span class="text-primary me-4 py-2">
                            <img src="' . base_url('public/assets/img/emojis/' . $budget['icon'] . '') . '" width="40px">
                        </span>
                        <span class="text-end">
                            <span class="small">
                                ' . $budget['name'] . '
                            </span>
                            <br>
                            <span class="fw-bolder">
                                Rp ' . number_format($budget['budget'], 0, ',', '.') . '
                            </span>
                        </span>
                    </div>
                </div>
                ';
        }

        return $html;
    }

    public function detailBudget($uuid = NULL)
    {
        $data['budgets'] = $this->db->table('budgets')
            ->select('*')
            ->where('deleted_at IS NULL')
            ->where('owner', $this->session->userSession)
            ->where('uuid !=', $uuid)
            ->get()
            ->getResultArray();

        if ($uuid != null) {
            $data['budgetDetail'] = $this->db->table('budgets')
                ->select('*')
                ->where('uuid', $uuid)
                ->where('owner', $this->session->userSession)
                ->where('deleted_at IS NULL')
                ->get()
                ->getRowArray();

            return view('budgets/budget-detail', $data);
        }
    }

    public function addBalance()
    {
        $data['transaction_container_id'] = $_POST['budgetUuid'];
        $data['amount'] = $_POST['balance'];
        $data['note'] = $_POST['note'];

        $data['uuid'] = Uuid::uuid4();

        $data['source_type'] = 1;
        $data['from'] = 'TOP UP';
        $data['initiator'] = $this->session->userSession;
        $data['created_at'] = time();
        $data['updated_at'] = time();

        $this->db->transBegin();

        $this->db->table('transactions')
            ->set($data)
            ->insert();

        $currentBalance = $this->db->table('budgets')
            ->select('budget')
            ->where('uuid', $data['transaction_container_id'])
            ->get()
            ->getRowArray()['budget'];
        $newBalance = $currentBalance + $data['amount'];
        $this->db->table('budgets')
            ->set('budget', $newBalance)
            ->where('uuid', $data['transaction_container_id'])
            ->update();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return json_encode(['response' => 'FAILED']);
        } else {
            $this->db->transCommit();
            return json_encode(['response' => 'SUCCESS', 'balance' => "Rp " . number_format($newBalance, 0, ',', '.')]);
        }
    }

    public function listTransaction($budgetId)
    {
        $transactions = $this->db->table('transactions')
            ->select('*')
            ->where('transaction_container_id', $budgetId)
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
            /* Transaksi Ke Atau Dari Budget */
            if ($trx['source_type'] == '0' || $trx['destination_type'] == '0') {
                if ($trx['from'] !== NULL) {
                    $relatedBudget = $trx['from'];
                } else {
                    $relatedBudget = $trx['to'];
                }

                $budget = $this->db->table('budgets')
                    ->select('name, icon')
                    ->where('uuid', $relatedBudget)
                    ->get()
                    ->getRowArray();

                $emoji = '<img src=" ' . base_url('public/assets/img/emojis/' . $budget['icon']) . '" style="width: 50px;height: 50px;" class="me-3">';
                $title = $budget['name'];
            }

            /* Transaksi keluar */
            if ($trx['type'] == '1') {
                $trxType = 'Uang Keluar';
                $amount = "text-danger\">-Rp " . number_format($trx['amount'], 0, ',', '.');


                /* Trx keluar dengan budget */
                if ($trx['destination_type'] == '1') {
                    if ($trx['budget_id'] != NULL) {
                        $budget = $this->db->table('budgets')
                            ->select('*')
                            ->where('uuid', $trx['budget_id'])
                            ->get()
                            ->getRowArray();

                        $emoji = '<img src=" ' . base_url('public/assets/img/emojis/' . $budget['icon']) . '" style="width: 50px;height: 50px;" class="me-3">';
                        $title = $budget['name'];
                    } else {
                        $initials = explode(' ', $trx['to']);
                        $initials = array_slice($initials, 0, 2);
                        $acronym = '';
                        foreach ($initials as $initial) {
                            $firstLetter = substr($initial, 0, 1);
                            $acronym .= strtoupper($firstLetter);
                        }
                        $emoji = '<div class="rounded-circle bg-primary text-light d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;">
                            ' . $acronym . '
                        </div>';

                        $title = $trx['to'];
                    }
                }
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
        $destination = $_POST['budgetId'];
        $amount = $_POST['amount'];
        $note = $_POST['note'];
        $source = $_POST['sourceBudgetId'];

        $trxOut = [
            'uuid' => Uuid::uuid4()->toString(),
            'type' => '1',
            'amount' => $amount,
            'transaction_container_id' => $source,
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
            'transaction_container_id' => $destination,
            'source_type' => '0',
            'from' => $source,
            'initiator' => $this->session->userSession,
            'note' => $note,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $this->db->transBegin();

        $newBalanceOnSource = $this->db->table('budgets')
            ->select('budget')
            ->where('uuid', $source)
            ->get()->getRowArray()['budget'] - $amount;

        $newBalanceOnDestination = $this->db->table('budgets')
            ->select('budget')
            ->where('uuid', $destination)
            ->get()->getRowArray()['budget'] + $amount;


        $this->db->table('transactions')
            ->set($trxOut)
            ->insert();
        $this->db->table('transactions')
            ->set($trxIn)
            ->insert();

        $this->db->table('budgets')
            ->set('budget', $newBalanceOnSource)
            ->where('uuid', $source)
            ->update();
        $this->db->table('budgets')
            ->set('budget', $newBalanceOnDestination)
            ->where('uuid', $destination)
            ->update();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return json_encode(['response' => 'FAILED']);
        } else {

            if ($newBalanceOnSource < 0) {
                return json_encode(['response' => 'BALANCE_LIMIT']);
            } else {
                $this->db->transCommit();
                return json_encode(['response' => 'SUCCESS', 'balance' => "Rp " . number_format($newBalanceOnSource, 0, ',', '.')]);
            }
        }
    }
}
