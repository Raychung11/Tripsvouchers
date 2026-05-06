<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Token;
use App\Models\Campaign;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function claimForm(array $params): void
    {
        $campaign = $this->resolveCampaign((string) $params['campaign']);
        if (!$campaign) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }

        $this->render('public/claim_form', [
            'title'     => __('claim.title'),
            'campaign'  => $campaign,
            'claimable' => Campaign::claimable($campaign),
        ]);
    }

    public function claim(array $params): void
    {
        $campaign = $this->resolveCampaign((string) $params['campaign']);
        if (!$campaign) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }

        $data = [
            'name'    => trim((string) $this->input('name', '')),
            'phone'   => trim((string) $this->input('phone', '')),
            'consent' => $this->input('consent') ? '1' : '',
        ];
        flash_input($data);

        $errors = $this->validate([
            'name'    => 'required|min:2',
            'phone'   => 'required|min:7',
            'consent' => 'required',
        ], $data);
        if ($errors) {
            flash('errors', $errors);
            redirect('/claim/' . urlencode((string) $params['campaign']));
        }

        if (!Campaign::claimable($campaign)) {
            flash('error', __('claim.closed'));
            redirect('/campaigns/' . $campaign['id']);
        }

        // Check campaign limit again right before insert
        if ((int) $campaign['claim_limit'] > 0
            && Voucher::countForCampaign((int) $campaign['id']) >= (int) $campaign['claim_limit']) {
            flash('error', __('claim.limit_reached'));
            redirect('/campaigns/' . $campaign['id']);
        }

        // Prevent duplicate claim per phone for the same campaign
        $existing = Voucher::findByPhone((int) $campaign['id'], $data['phone']);
        if ($existing) {
            flash('info', __('claim.duplicate'));
            redirect('/voucher/' . $existing['voucher_code']);
        }

        $voucher = Voucher::create(
            $campaign,
            $data['name'],
            $data['phone'],
            $campaign['location_slug'] ?? 'GEN'
        );

        flash('success', __('claim.success'));
        redirect('/voucher/' . $voucher['voucher_code']);
    }

    public function show(array $params): void
    {
        $code = (string) $params['code'];
        $voucher = Voucher::findByCode($code);
        if (!$voucher) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $campaign = Campaign::find((int) $voucher['campaign_id']);
        $this->render('public/voucher', [
            'title'    => __('voucher.title'),
            'voucher'  => $voucher,
            'campaign' => $campaign,
            'qr_url'   => Voucher::qrUrl($voucher),
        ]);
    }

    /**
     * /redeem?v=<encrypted_token> — preview that visitors might land on,
     * but redemption itself requires a logged-in merchant via /merchant/scanner/redeem.
     */
    public function redeemPreview(array $params): void
    {
        $token = (string) ($_GET['v'] ?? '');
        $payload = Token::decrypt($token);
        if (!$payload || empty($payload['c'])) {
            $this->render('public/redeem_invalid', ['title' => __('redemption.invalid')]);
            return;
        }
        $voucher = Voucher::findByCode((string) $payload['c']);
        if (!$voucher) {
            $this->render('public/redeem_invalid', ['title' => __('redemption.invalid')]);
            return;
        }
        $this->render('public/redeem_preview', [
            'title'   => __('voucher.title'),
            'voucher' => $voucher,
            'campaign'=> Campaign::find((int) $voucher['campaign_id']),
        ]);
    }

    private function resolveCampaign(string $key): ?array
    {
        if (ctype_digit($key)) {
            return Campaign::find((int) $key);
        }
        return Campaign::findBySlug($key);
    }
}
