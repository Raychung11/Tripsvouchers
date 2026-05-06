<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Token;
use App\Models\Campaign;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Voucher;

class ScannerController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        [$canRedeem, $reason] = Merchant::canRedeem($merchant);
        $this->render('merchant/scanner', [
            'title'      => __('merchant.scanner'),
            'merchant'   => $merchant,
            'can_redeem' => $canRedeem,
            'reason'     => $reason,
        ]);
    }

    public function redeem(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $voucher = $this->resolveVoucher((string) $this->input('code', ''), (string) $this->input('token', ''));

        $isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if (!$voucher) {
            if ($isAjax) $this->json(['ok' => false, 'message' => __('redemption.invalid')], 422);
            flash('error', __('redemption.invalid'));
            redirect('/merchant/scanner');
        }

        try {
            $result = Redemption::redeem($voucher, $merchant, (int) Auth::id());
        } catch (\Throwable $e) {
            if ($isAjax) $this->json(['ok' => false, 'message' => $e->getMessage()], 422);
            flash('error', $e->getMessage());
            redirect('/merchant/scanner');
        }

        $msg = __('merchant.redeem_success', ['fee' => number_format($result['fee'], 2)]);
        if ($isAjax) {
            $this->json([
                'ok'             => true,
                'message'        => $msg,
                'voucher_code'   => $voucher['voucher_code'],
                'balance_after'  => $result['balance_after'],
            ]);
        }
        flash('success', $msg);
        redirect('/merchant/redemptions');
    }

    private function resolveVoucher(string $code, string $token): ?array
    {
        $code = trim($code);
        if ($code !== '') {
            return Voucher::findByCode($code);
        }
        if ($token !== '') {
            // Token may be the raw value or a full URL
            if (str_contains($token, 'v=')) {
                parse_str(parse_url($token, PHP_URL_QUERY) ?? '', $q);
                $token = $q['v'] ?? $token;
            }
            $payload = Token::decrypt($token);
            if ($payload && !empty($payload['c'])) {
                return Voucher::findByCode((string) $payload['c']);
            }
        }
        return null;
    }
}
