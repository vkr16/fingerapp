<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Budget extends BaseController
{
    protected $db;
    protected $validation;
    protected $email;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        $this->email = \Config\Services::email();
        $this->session = \Config\Services::session();;
    }

    public function index()
    {
        return view('budgets/budget');
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
                    'required' => 'Nama anggaran wajib diisi!'
                ]
            ]
        );

        if (!$this->validation->run($data)) {
            return json_encode($this->validation->getErrors());
        }

        $userEmail = $this->session->get('user_email_session');

        $data['owner'] = $this->db->table('users')
            ->select('id')
            ->where('email', $userEmail)
            ->get()
            ->getRowArray()['id'];

        $data['created_at'] = time();
        $data['updated_at'] = time();
        $this->db->transBegin();

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
        $userEmail = $this->session->get('user_email_session');

        $userId = $this->db->table('users')
            ->select('id')
            ->where('email', $userEmail)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray()['id'];

        $budgets = $this->db->table('budgets')
            ->select('*')
            ->where('owner', $userId)
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $html = '';

        foreach ($budgets as $key => $budget) {
            $html .= '<div class="card mb-2 rounded-3">
                    <div class="card-body py-1 d-flex justify-content-between align-items-center">
                         <span class="text-primary me-4 py-2">
                            <img src="' . base_url('public/assets/img/emojis/' . $budget['icon'] . '') . '" width="40px">
                        </span>
                        <span class="text-end">
                            <span class="small">
                                ' . $budget['name'] . '</sub>
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
}
