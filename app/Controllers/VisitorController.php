<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

/**
 * Visitor "find my vouchers" — a phone-based lookup so tourists can come
 * back later and see all the vouchers they've claimed across campaigns.
 *
 * No password / OTP yet (vouchers are low-value claim tokens, the QR code
 * is already the access credential). Phone normalisation strips non-digits
 * and matches on the last 9 digits so "+60123456789" / "0123456789" /
 * "12 3456 7890" all resolve to the same person.
 *
 * Phase 2 idea: bolt on Twilio/MessageBird OTP for stronger identity.
 */
class VisitorController extends Controller
{
    /** GET /my — phone input form (or redirect if already signed in). */
    public function index(array $params): void
    {
        if (!empty($_SESSION['visitor_phone_last9'])) {
            redirect('/my/vouchers');
        }
        $this->render('public/my/login', [
            'title' => __('visitor.login_title'),
        ]);
    }

    /** POST /my — accept phone, set session, redirect to dashboard. */
    public function login(array $params): void
    {
        $phone = trim((string) $this->input('phone', ''));
        flash_input(['phone' => $phone]);

        $errors = $this->validate(['phone' => 'required|min:7'], ['phone' => $phone]);
        if ($errors) {
            flash('errors', $errors);
            redirect('/my');
        }

        $last9 = self::normalisePhone($phone);
        if (strlen($last9) < 7) {
            flash('error', __('visitor.invalid_phone'));
            redirect('/my');
        }

        $count = (int) Database::value(
            "SELECT COUNT(*) FROM vouchers
             WHERE RIGHT(REGEXP_REPLACE(customer_phone, '[^0-9]+', ''), 9) = ?",
            [$last9]
        );

        if ($count === 0) {
            flash('error', __('visitor.no_vouchers_found'));
            redirect('/my');
        }

        session_regenerate_id(true);
        $_SESSION['visitor_phone'] = $phone;
        $_SESSION['visitor_phone_last9'] = $last9;
        redirect('/my/vouchers');
    }

    /** GET /my/vouchers — list active / redeemed / expired vouchers. */
    public function dashboard(array $params): void
    {
        $last9 = $_SESSION['visitor_phone_last9'] ?? null;
        if (!$last9) {
            redirect('/my');
        }

        $rows = Database::all(
            "SELECT v.*, c.campaign_name, c.voucher_value, c.voucher_type,
                    l.area_name AS location_name, l.state AS location_state
             FROM vouchers v
             JOIN campaigns c ON c.id = v.campaign_id
             LEFT JOIN locations l ON l.id = c.location_id
             WHERE RIGHT(REGEXP_REPLACE(v.customer_phone, '[^0-9]+', ''), 9) = ?
             ORDER BY
               CASE v.status
                 WHEN 'claimed'  THEN 1
                 WHEN 'redeemed' THEN 2
                 ELSE 3
               END,
               v.claimed_at DESC",
            [$last9]
        );

        $name = $rows ? (string) $rows[0]['customer_name'] : '';
        $active   = array_values(array_filter($rows, fn ($v) => $v['status'] === 'claimed'));
        $redeemed = array_values(array_filter($rows, fn ($v) => $v['status'] === 'redeemed'));
        $expired  = array_values(array_filter($rows, fn ($v) => in_array($v['status'], ['expired', 'void'], true)));

        $this->render('public/my/dashboard', [
            'title'    => __('visitor.your_vouchers'),
            'name'     => $name,
            'phone'    => $_SESSION['visitor_phone'] ?? '',
            'active'   => $active,
            'redeemed' => $redeemed,
            'expired'  => $expired,
        ]);
    }

    /** POST /my/logout — clear visitor session. */
    public function logout(array $params): void
    {
        unset($_SESSION['visitor_phone'], $_SESSION['visitor_phone_last9']);
        session_regenerate_id(true);
        flash('info', __('visitor.signed_out'));
        redirect('/');
    }

    /** Strip non-digits and return the last 9 digits (MY mobile w/o country code). */
    public static function normalisePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        return substr($digits, -9);
    }
}
