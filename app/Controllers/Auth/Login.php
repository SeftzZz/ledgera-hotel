<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\PermissionService;
use App\Libraries\JwtService;

class Login extends BaseController
{
    protected UserModel $userModel;
    protected $jwt;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jwt   = new JwtService();
    }

    /**
     * GET /login
     * Show login page
     */
    public function index()
    {
        // Kalau sudah login, langsung ke dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login', [
            'title' => 'Login'
        ]);
    }

    /**
     * POST /login
     * Proses login web (session-based)
     */
    public function auth()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (! $email || ! $password) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email dan password wajib diisi');
        }

        $user = $this->userModel
            ->select('
                users.*,
                companies.company_name,
                branches.*,
                categories.id,
                categories.name as category_name,
            ')
            ->join('companies', 'companies.id = users.company_id', 'left')
            ->join('branches', 'branches.id = users.branch_id', 'left')
            ->join('categories', 'categories.id = users.category_id', 'left')
            ->where('users.email', $email)
            ->where('users.is_active', 1)
            ->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah');
        }

        // Ambil permission & cache ke session
        $permissions = service('permission')->cache($user['id']);

        /*
        =========================
        GENERATE JWT TOKEN
        =========================
        */

        $token = $this->jwt->generateAccessToken((object)$user);
        $refreshToken = $this->jwt->generateRefreshToken();

        // Set session login
        session()->set([
            'user_id'         => $user['id'],
            'user_name'       => $user['name'],
            'user_email'      => $user['email'],
            'user_role'       => $user['role'],
            'company_id'      => $user['company_id'],
            'company_name'    => $user['company_name'],
            'branch_id'       => $user['branch_id'],
            'branch_name'     => $user['branch_name'],
            'branch_address'  => $user['branch_address'],
            'branch_logo'     => $user['branch_logo'],
            'category_id'     => $user['category_id'],
            'category_name'   => $user['category_name'],
            'permissions'     => $permissions,
            'jwt_token'       => $token,
            'refresh_token'   => $refreshToken,
            'is_logged_in'    => true,
            'logged_at'       => date('Y-m-d H:i:s'),
        ]);
        
        // Update last login
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('dashboard'));
    }

    /**
     * GET /logout
     * Destroy session
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('login'))
            ->with('success', 'Anda berhasil logout');
    }
}
