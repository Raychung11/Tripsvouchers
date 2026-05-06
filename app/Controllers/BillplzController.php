<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Billplz;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Models\Merchant;

class BillplzController extends Controller
{
    /**
     * Server-to-server callback. Billplz POSTs payment status here.
     * Idempotent — duplicate callbacks must not double-credit a wallet.
     */
    public function callback(array $params): void
    {
        $payload = $_POST;
        if (!Billplz::verifyCallback($payload)) {
            http_response_code(400);
            echo 'invalid signature';
            return;
        }

        $billId = (string) ($payload['id'] ?? '');
        $paid = ($payload['paid'] ?? '') === 'true' || ($payload['state'] ?? '') === 'paid';
        $paidAt = $payload['paid_at'] ?? null;

        if ($billId === '') {
            http_response_code(400);
            echo 'missing id';
            return;
        }

        $type = $payload['reference_2'] ?? null;

        try {
            if ($type === 'wallet_topup') {
                $this->handleTopup($billId, $paid, $paidAt);
            } elseif ($type === 'subscription') {
                $this->handleSubscription($billId, $paid, $paidAt);
            } else {
                // Try both
                $this->handleTopup($billId, $paid, $paidAt);
                $this->handleSubscription($billId, $paid, $paidAt);
            }
        } catch (\Throwable $e) {
            logger('Billplz callback error: ' . $e->getMessage(), $payload);
            http_response_code(500);
            echo 'error';
            return;
        }

        echo 'ok';
    }

    public function redirect(array $params): void
    {
        $payload = $_GET;
        Billplz::verifyRedirect($payload); // best-effort
        $billplz = $payload['billplz'] ?? $payload;
        $paid = ($billplz['paid'] ?? '') === 'true';
        flash($paid ? 'success' : 'info', $paid ? 'Payment successful.' : 'Payment pending or cancelled.');
        // Try to land back on a sensible page
        $topup = Database::fetch('SELECT merchant_id FROM wallet_topups WHERE billplz_bill_id = ?', [$billplz['id'] ?? '']);
        if ($topup) { redirect('/merchant/wallet'); }
        $sub = Database::fetch('SELECT merchant_id FROM merchant_subscriptions WHERE billplz_bill_id = ?', [$billplz['id'] ?? '']);
        if ($sub) { redirect('/merchant/subscription'); }
        redirect('/merchant');
    }

    private function handleTopup(string $billId, bool $paid, ?string $paidAt): void
    {
        $row = Database::fetch('SELECT * FROM wallet_topups WHERE billplz_bill_id = ?', [$billId]);
        if (!$row) {
            return;
        }
        if ($row['payment_status'] === 'paid') {
            return; // idempotent
        }
        $status = $paid ? 'paid' : 'failed';
        Database::run(
            'UPDATE wallet_topups SET payment_status = ?, paid_at = ? WHERE id = ?',
            [$status, $paid ? ($paidAt ?: date('Y-m-d H:i:s')) : null, $row['id']]
        );
        if ($paid) {
            Merchant::adjustWallet(
                (int) $row['merchant_id'],
                (float) $row['amount'],
                'topup',
                'Wallet top-up · Billplz ' . $billId,
                'wallet_topup',
                (int) $row['id']
            );
        }
    }

    private function handleSubscription(string $billId, bool $paid, ?string $paidAt): void
    {
        $row = Database::fetch('SELECT * FROM merchant_subscriptions WHERE billplz_bill_id = ?', [$billId]);
        if (!$row) {
            return;
        }
        if ($row['payment_status'] === 'paid') {
            return; // idempotent
        }
        if (!$paid) {
            Database::run('UPDATE merchant_subscriptions SET payment_status = "failed" WHERE id = ?', [$row['id']]);
            return;
        }

        $start = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime('+1 year'));

        Database::transaction(function () use ($row, $billId, $start, $expiry) {
            Database::run(
                'UPDATE merchant_subscriptions
                 SET payment_status = "paid", status = "active", start_date = ?, expiry_date = ?
                 WHERE id = ?',
                [$start, $expiry, $row['id']]
            );
            Database::run(
                'UPDATE merchants SET subscription_status = "active", status = "active" WHERE id = ?',
                [$row['merchant_id']]
            );
        });
    }

    /**
     * Local-only fake Billplz payment screen so the flow can be tested
     * without real Billplz credentials. Disabled in production.
     */
    public function devPay(array $params): void
    {
        if (env('APP_ENV') === 'production') { http_response_code(404); echo 'Not found'; return; }
        $bill = (string) ($_GET['bill'] ?? '');
        $ref  = (string) ($_GET['ref']  ?? '');
        $this->render('public/billplz_dev', [
            'title' => 'Billplz (dev mode)',
            'bill'  => $bill,
            'ref'   => $ref,
        ]);
    }

    public function devPayConfirm(array $params): void
    {
        if (env('APP_ENV') === 'production') { http_response_code(404); echo 'Not found'; return; }
        $bill = (string) $this->input('bill', '');
        $action = (string) $this->input('action', 'pay');
        $paid = $action === 'pay';

        // Simulate the callback synchronously for dev convenience.
        $payload = [
            'id'           => $bill,
            'paid'         => $paid ? 'true' : 'false',
            'state'        => $paid ? 'paid' : 'due',
            'paid_at'      => date('c'),
            'reference_2'  => '',
            'x_signature'  => '',
        ];
        $_POST = $payload;
        $this->callback($params);

        flash($paid ? 'success' : 'info', $paid ? 'Dev payment successful.' : 'Dev payment cancelled.');
        $topup = Database::fetch('SELECT merchant_id FROM wallet_topups WHERE billplz_bill_id = ?', [$bill]);
        if ($topup) { redirect('/merchant/wallet'); }
        $sub = Database::fetch('SELECT merchant_id FROM merchant_subscriptions WHERE billplz_bill_id = ?', [$bill]);
        if ($sub) { redirect('/merchant/subscription'); }
        redirect('/merchant');
    }
}
