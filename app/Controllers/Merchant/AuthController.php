<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Merchant;

class AuthController extends Controller
{
    public function showLogin(array $params): void
    {
        $this->render('merchant/login', ['title' => __('merchant.login_title')], 'layouts/public');
    }

    public function login(array $params): void
    {
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        flash_input(['email' => $email]);

        if (!Auth::attempt($email, $password, 'merchant')) {
            flash('error', __('auth.invalid'));
            redirect('/merchant/login');
        }
        redirect('/merchant/dashboard');
    }

    public function showRegister(array $params): void
    {
        $this->render('merchant/register', ['title' => __('merchant.register_title')], 'layouts/public');
    }

    public function register(array $params): void
    {
        $data = [
            'business_name'         => trim((string) $this->input('business_name', '')),
            'owner_name'            => trim((string) $this->input('owner_name', '')),
            'email'                 => trim((string) $this->input('email', '')),
            'phone'                 => trim((string) $this->input('phone', '')),
            'whatsapp'              => trim((string) $this->input('whatsapp', '')),
            'category'              => (string) $this->input('category', 'fnb'),
            'address'               => trim((string) $this->input('address', '')),
            'password'              => (string) $this->input('password', ''),
            'password_confirmation' => (string) $this->input('password_confirmation', ''),
            'terms'                 => $this->input('terms') ? '1' : '',
        ];
        flash_input($data);

        $errors = $this->validate([
            'business_name' => 'required|min:2',
            'owner_name'    => 'required|min:2',
            'email'         => 'required|email',
            'phone'         => 'required|min:7',
            'password'      => 'required|min:8',
        ], $data);

        if ($data['password'] !== '' && $data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'][] = __('merchant.register_password_mismatch');
        }
        if ($data['terms'] !== '1') {
            $errors['terms'][] = __('merchant.register_terms_required');
        }

        if (!$errors) {
            $exists = Database::value('SELECT id FROM users WHERE email = ?', [$data['email']]);
            if ($exists) {
                $errors['email'][] = 'Email already in use.';
            }
        }
        if ($errors) {
            flash('errors', $errors);
            redirect('/merchant/register');
        }

        Database::transaction(function () use ($data) {
            $userId = Database::insert(
                'INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, "merchant", "active")',
                [
                    $data['owner_name'],
                    $data['email'],
                    $data['phone'],
                    password_hash($data['password'], PASSWORD_BCRYPT),
                ]
            );
            Database::insert(
                'INSERT INTO merchants
                  (user_id, business_name, owner_name, phone, whatsapp, email, category, address,
                   wallet_balance, subscription_status, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, "pending", "pending")',
                [
                    $userId,
                    $data['business_name'],
                    $data['owner_name'],
                    $data['phone'],
                    $data['whatsapp'] ?: null,
                    $data['email'],
                    in_array($data['category'], ['fnb','hotel','retail','souvenir','attraction','transport','experience','others'], true) ? $data['category'] : 'fnb',
                    $data['address'] ?: null,
                ]
            );
            $user = Database::fetch('SELECT * FROM users WHERE id = ?', [$userId]);
            Auth::login($user);
        });

        flash('success', __('merchant.sub_pay'));
        redirect('/merchant/subscription');
    }

    public function logout(array $params): void
    {
        Auth::logout();
        redirect('/');
    }
}
