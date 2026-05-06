<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Billplz;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Merchant;

class SubscriptionController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $sub = Merchant::activeSubscription((int) $merchant['id']);
        $this->render('merchant/subscription', [
            'title'    => __('merchant.subscription'),
            'merchant' => $merchant,
            'sub'      => $sub,
            'fee'      => (float) config('config.pricing.subscription_fee', 150),
        ]);
    }

    public function pay(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $fee = (float) config('config.pricing.subscription_fee', 150);

        $subId = Database::insert(
            'INSERT INTO merchant_subscriptions (merchant_id, annual_fee, payment_status, status) VALUES (?, ?, "pending", "pending")',
            [$merchant['id'], $fee]
        );

        try {
            $bill = Billplz::createBill([
                'name'         => $merchant['business_name'],
                'email'        => $merchant['email'],
                'mobile'       => $merchant['phone'],
                'amount'       => $fee,
                'description'  => 'SLV Annual Subscription · ' . $merchant['business_name'],
                'callback_url' => url('/billplz/callback'),
                'redirect_url' => url('/billplz/redirect'),
                'reference_1_label' => 'SubscriptionId',
                'reference_1'  => (string) $subId,
                'reference_2_label' => 'Type',
                'reference_2'  => 'subscription',
            ]);
        } catch (\Throwable $e) {
            logger('Billplz subscription failed: ' . $e->getMessage());
            flash('error', 'Payment provider error. Please try again.');
            redirect('/merchant/subscription');
        }

        Database::run('UPDATE merchant_subscriptions SET billplz_bill_id = ? WHERE id = ?', [$bill['id'], $subId]);

        redirect($bill['url']);
    }
}
