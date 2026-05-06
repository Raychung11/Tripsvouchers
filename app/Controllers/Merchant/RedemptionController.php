<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Merchant;
use App\Models\Redemption;

class RedemptionController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $rows = Redemption::listForMerchant((int) $merchant['id']);
        $this->render('merchant/redemptions', [
            'title'    => __('merchant.redemptions'),
            'merchant' => $merchant,
            'rows'     => $rows,
        ]);
    }
}
