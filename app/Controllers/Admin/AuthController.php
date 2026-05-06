<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLogin(array $params): void
    {
        $this->render('admin/login', ['title' => __('admin.login_title')], 'layouts/public');
    }

    public function login(array $params): void
    {
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        flash_input(['email' => $email]);

        if (!Auth::attempt($email, $password, 'admin')) {
            flash('error', __('auth.invalid'));
            redirect('/admin/login');
        }
        redirect('/admin/dashboard');
    }

    public function logout(array $params): void
    {
        Auth::logout();
        redirect('/');
    }
}
