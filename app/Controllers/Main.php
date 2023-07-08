<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Main extends BaseController
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
        $this->login();
    }

    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function recovery()
    {
        return view('recovery');
    }

    public function registerProcess()
    {
        $data['name'] = trim(ucwords(strtolower($_POST['name'])));
        $data['email'] = trim(strtolower($_POST['email']));
        $data['password'] = $_POST['password'];

        $this->validation->setRules(
            [
                'name' => 'required|alpha_space',
                'email' => 'required|valid_email',
                'password' => 'required'
            ],
            [
                'name' => [
                    'required' => 'Nama tidak boleh kosong!',
                    'alpha_space' => 'Nama hanya boleh berupa huruf dan spasi'
                ],
                'email' => [
                    'required' => 'Email tidak boleh kosong!',
                    'valid_email' => 'Harap isikan email yang valid!'
                ],
                'password' => [
                    'required' => 'Password tidak boleh kosong'
                ]
            ]
        );

        if (!$this->validation->run($data)) {
            return json_encode($this->validation->getErrors());
        }

        $countUserConflict = $this->db->table('users')
            ->select('id')
            ->where('email', $data['email'])
            ->countAllResults();

        if ($countUserConflict > 0) {
            return json_encode(['email' => 'Email sudah digunakan!']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $rawToken = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 64)), 0, 64);
            $data['token'] = password_hash($rawToken, PASSWORD_DEFAULT);

            $this->db->transBegin();
            $this->db->table('users')
                ->set($data)
                ->insert();
            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();
                return json_encode(['response' => 'FAIL']);
            } else {
                $domain = explode('@', $data['email'])[1];
                if (checkdnsrr($domain, 'MX')) {
                    $this->email->setFrom('webservice@akuonline.my.id', 'AkuOnline WebService');
                    // $this->email->setTo($data['email']);
                    $this->email->setTo('spam@akuonline.my.id');

                    $this->email->setSubject('Registrasi Berhasil');
                    $this->email->setMessage('<h3>Hai, ' . $data['name'] . '</h3>
                    <br><br>
                    Akun anda telah berhasil dibuat, silahkan klik link dibawah ini untuk memverifikasi akun anda.
                    <br><br>
                    <a href="' . base_url('verify/') . $rawToken . '/' . $data['email'] . '"><button>Verifikasi</button></a>
                    <br><br>
                    Atau salin dan buka link berikut dengan browser anda 
                    <br>
                    <i>' . base_url('verify/') . $rawToken . '/' . $data['email'] . '</i>
                    <br><br>
                    <small>*Link valid hingga ' . date('d-m-Y') . ' 23:59</small>
                    ');
                    if ($this->email->send()) {
                        $this->db->transCommit();
                        return json_encode(['response' => 'SUCCESS']);
                    } else {
                        $this->db->transRollback();
                        return json_encode(['response' => 'FAIL']);
                    }
                } else {
                    $this->db->transRollback();
                    return json_encode(['domain' => 'Harap gunakan email dengan domain lain!']);
                }
            }
        }
    }

    public function verify($token = null, $email = null)
    {
        if (!empty($token) && !empty($email)) {
            $queryGetUser = $this->db->table('users')
                ->select('*')
                ->where('email', $email)
                ->get();
            if ($user = $queryGetUser->getRowArray()) {
                if (password_verify($token, $user['token'])) {
                    $this->db->transBegin();
                    $this->db->table('users')
                        ->set(
                            [
                                'token' => NULL,
                                'is_email_verified' => '1',
                                'status' => '1',
                                'updated_at' => time()
                            ]
                        )
                        ->where('email', $email)
                        ->update();
                    if ($this->db->transStatus() === FALSE) {
                        $this->db->transRollback();
                        $this->session->setFlashdata('verification-fail', 'Verifikasi email gagal, silahkan ulangi pendaftaran');
                        $this->db->table('users')
                            ->where('email', $email)
                            ->delete();
                        return redirect()->to(base_url());
                    } else {
                        $this->db->transCommit();
                        $this->session->setFlashdata('verification-success', 'Verifikasi Berhasil');
                        return redirect()->to(base_url());
                    }
                } else {
                    $this->db->transRollback();
                    $this->session->setFlashdata('verification-fail', 'Link tidak valid');
                    return redirect()->to(base_url());
                }
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
    }

    public function loginProcess()
    {
        $data['email'] = trim(strtolower($_POST['email']));
        $data['password'] = $_POST['password'];

        $this->validation->setRules(
            [
                'email' => 'required|valid_email',
                'password' => 'required'
            ],
            [
                'email' => [
                    'required' => 'Email tidak boleh kosong!',
                    'valid_email' => 'Harap isikan email yang valid!'
                ],
                'password' => [
                    'required' => 'Password tidak boleh kosong'
                ]
            ]
        );

        if (!$this->validation->run($data)) {
            return json_encode($this->validation->getErrors());
        }

        $query = $this->db->table('users')
            ->select('*')
            ->where('email', $data['email'])
            ->get();
        if ($result = $query->getRowArray()) {
            if ($result['status'] == 0) {
                if ($result['is_email_verified'] == 0) {
                    return json_encode(['email' => 'Email anda belum diverifikasi!']);
                } else {
                    return json_encode(['response' => 'ACCOUNT_DISABLED']);
                }
            } else {
                if (!password_verify($data['password'], $result['password'])) {
                    return json_encode(['password' => 'Password tidak sesuai!']);
                } else {
                    $this->session->set('user_email_session', $result['email']);
                    return json_encode(['response' => 'LOGIN_VALID']);
                }
            }
        } else {
            return json_encode(['email' => 'Email tidak terdaftar!']);
        }
    }
}
