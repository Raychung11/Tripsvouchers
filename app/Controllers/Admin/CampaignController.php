<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Campaign;
use App\Models\Location;
use App\Models\Merchant;

class CampaignController extends Controller
{
    public function index(array $params): void
    {
        $this->render('admin/campaigns/index', [
            'title' => __('admin.campaigns'),
            'rows'  => Campaign::all(),
        ]);
    }

    public function create(array $params): void
    {
        $this->render('admin/campaigns/form', [
            'title'     => __('admin.create') . ' · ' . __('admin.campaigns'),
            'campaign'  => null,
            'locations' => Location::active(),
        ]);
    }

    public function store(array $params): void
    {
        $data = $this->collect();
        $errors = $this->validate(['campaign_name' => 'required', 'voucher_value' => 'required|numeric'], $data);
        if ($errors) {
            flash('errors', $errors);
            flash_input($data);
            redirect('/admin/campaigns/create');
        }
        $slug = Campaign::slugify($data['campaign_name']);
        $id = Database::insert(
            'INSERT INTO campaigns (location_id, campaign_name, slug, description, voucher_type, voucher_value,
                start_date, end_date, banner_image, claim_limit, terms, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$data['location_id'] ?: null, $data['campaign_name'], $slug, $data['description'] ?: null,
             $data['voucher_type'], $data['voucher_value'], $data['start_date'] ?: null, $data['end_date'] ?: null,
             $data['banner_image'] ?: null, (int) $data['claim_limit'], $data['terms'] ?: null, $data['status']]
        );
        flash('success', __('admin.save'));
        redirect('/admin/campaigns/' . $id);
    }

    public function show(array $params): void
    {
        $campaign = Campaign::find((int) $params['id']);
        if (!$campaign) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $merchants = Campaign::merchants((int) $campaign['id']);
        $available = Database::all(
            'SELECT m.id, m.business_name FROM merchants m
             WHERE m.status = "active" AND m.id NOT IN (
                SELECT merchant_id FROM campaign_merchants WHERE campaign_id = ?
             ) ORDER BY m.business_name',
            [$campaign['id']]
        );
        $stats = [
            'claimed'  => (int) Database::value('SELECT COUNT(*) FROM vouchers WHERE campaign_id = ?', [$campaign['id']]),
            'redeemed' => (int) Database::value('SELECT COUNT(*) FROM redemptions WHERE campaign_id = ?', [$campaign['id']]),
        ];
        $this->render('admin/campaigns/show', [
            'title'     => $campaign['campaign_name'],
            'campaign'  => $campaign,
            'merchants' => $merchants,
            'available' => $available,
            'stats'     => $stats,
        ]);
    }

    public function edit(array $params): void
    {
        $campaign = Campaign::find((int) $params['id']);
        if (!$campaign) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $this->render('admin/campaigns/form', [
            'title'     => __('admin.edit') . ' · ' . $campaign['campaign_name'],
            'campaign'  => $campaign,
            'locations' => Location::active(),
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = $this->collect();
        Database::run(
            'UPDATE campaigns SET location_id=?, campaign_name=?, description=?, voucher_type=?, voucher_value=?,
              start_date=?, end_date=?, banner_image=?, claim_limit=?, terms=?, status=? WHERE id = ?',
            [$data['location_id'] ?: null, $data['campaign_name'], $data['description'] ?: null,
             $data['voucher_type'], $data['voucher_value'], $data['start_date'] ?: null, $data['end_date'] ?: null,
             $data['banner_image'] ?: null, (int) $data['claim_limit'], $data['terms'] ?: null, $data['status'], $id]
        );
        flash('success', __('admin.save'));
        redirect('/admin/campaigns/' . $id);
    }

    public function attachMerchant(array $params): void
    {
        $campaignId = (int) $params['id'];
        $merchantId = (int) $this->input('merchant_id', 0);
        if ($merchantId > 0) {
            Database::run(
                'INSERT INTO campaign_merchants (campaign_id, merchant_id, status)
                 VALUES (?, ?, "active")
                 ON DUPLICATE KEY UPDATE status = "active"',
                [$campaignId, $merchantId]
            );
        }
        flash('success', __('admin.save'));
        redirect('/admin/campaigns/' . $campaignId);
    }

    private function collect(): array
    {
        return [
            'location_id'   => (int) $this->input('location_id', 0),
            'campaign_name' => trim((string) $this->input('campaign_name', '')),
            'description'   => trim((string) $this->input('description', '')),
            'voucher_type'  => (string) $this->input('voucher_type', 'cash'),
            'voucher_value' => (float) $this->input('voucher_value', 0),
            'start_date'    => trim((string) $this->input('start_date', '')),
            'end_date'      => trim((string) $this->input('end_date', '')),
            'banner_image'  => trim((string) $this->input('banner_image', '')),
            'claim_limit'   => (int) $this->input('claim_limit', 0),
            'terms'         => trim((string) $this->input('terms', '')),
            'status'        => (string) $this->input('status', 'draft'),
        ];
    }
}
