<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Billplz;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Merchant;

class WalletController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        if (!$merchant) { redirect('/merchant/login'); }

        $topups = Database::all(
            'SELECT * FROM wallet_topups WHERE merchant_id = ? ORDER BY created_at DESC LIMIT 25',
            [$merchant['id']]
        );
        $tx = Database::all(
            'SELECT * FROM merchant_wallet_transactions WHERE merchant_id = ? ORDER BY created_at DESC LIMIT 25',
            [$merchant['id']]
        );

        $this->render('merchant/wallet', [
            'title'         => __('merchant.wallet'),
            'merchant'      => $merchant,
            'topups'        => $topups,
            'transactions'  => $tx,
            'wallet_status' => Merchant::walletStatus((float) $merchant['wallet_balance']),
        ]);
    }

    public function topupForm(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $this->render('merchant/wallet_topup', [
            'title'    => __('merchant.topup'),
            'merchant' => $merchant,
        ]);
    }

    public function topup(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $amount = (float) $this->input('amount', 0);
        $min = (float) config('config.pricing.wallet_min_reload', 100);
        flash_input(['amount' => $amount]);

        if ($amount < $min) {
            flash('error', __('merchant.topup_min', ['amount' => rm($min)]));
            redirect('/merchant/wallet/topup');
        }

        $topupId = Database::insert(
            'INSERT INTO wallet_topups (merchant_id, amount, payment_status) VALUES (?, ?, "pending")',
            [$merchant['id'], $amount]
        );

        try {
            $bill = Billplz::createBill([
                'name'         => $merchant['business_name'],
                'email'        => $merchant['email'],
                'mobile'       => $merchant['phone'],
                'amount'       => $amount,
                'description'  => 'SLV Wallet Top-up · ' . $merchant['business_name'],
                'callback_url' => url('/billplz/callback'),
                'redirect_url' => url('/billplz/redirect'),
                'reference_1_label' => 'TopupId',
                'reference_1'  => (string) $topupId,
                'reference_2_label' => 'Type',
                'reference_2'  => 'wallet_topup',
            ]);
        } catch (\Throwable $e) {
            logger('Billplz createBill failed: ' . $e->getMessage());
            flash('error', 'Payment provider error. Please try again.');
            redirect('/merchant/wallet/topup');
        }

        Database::run('UPDATE wallet_topups SET billplz_bill_id = ? WHERE id = ?', [$bill['id'], $topupId]);

        redirect($bill['url']);
    }
}
