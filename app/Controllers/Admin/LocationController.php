<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Upload;
use App\Models\Campaign;
use App\Models\Location;

class LocationController extends Controller
{
    public function index(array $params): void
    {
        $this->render('admin/locations/index', [
            'title' => __('admin.locations'),
            'rows'  => Location::all(),
        ]);
    }

    public function create(array $params): void
    {
        $this->render('admin/locations/form', [
            'title'    => __('admin.create') . ' · ' . __('admin.locations'),
            'location' => null,
        ]);
    }

    public function store(array $params): void
    {
        $data = $this->collect();
        $errors = $this->validate(['state' => 'required', 'area_name' => 'required'], $data);
        if ($errors) {
            flash('errors', $errors);
            flash_input($data);
            redirect('/admin/locations/create');
        }
        try {
            $uploaded = Upload::imageOrNull($_FILES['banner_file'] ?? null, 'locations');
            if ($uploaded) {
                $data['banner_image'] = $uploaded;
            }
        } catch (\RuntimeException $e) {
            flash('error', $e->getMessage());
            flash_input($data);
            redirect('/admin/locations/create');
        }
        $slug = Campaign::slugify($data['area_name']);
        Database::insert(
            'INSERT INTO locations (state, city, area_name, slug, description, banner_image, map_link, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$data['state'], $data['city'] ?: null, $data['area_name'], $slug,
             $data['description'] ?: null, $data['banner_image'] ?: null, $data['map_link'] ?: null,
             $data['status'] ?: 'active']
        );
        flash('success', __('admin.save'));
        redirect('/admin/locations');
    }

    public function edit(array $params): void
    {
        $location = Location::find((int) $params['id']);
        if (!$location) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $this->render('admin/locations/form', [
            'title'    => __('admin.edit') . ' · ' . $location['area_name'],
            'location' => $location,
        ]);
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $existing = Location::find($id);
        $data = $this->collect();
        try {
            $uploaded = Upload::imageOrNull($_FILES['banner_file'] ?? null, 'locations');
            if ($uploaded) {
                if (!empty($existing['banner_image'])) {
                    Upload::delete($existing['banner_image']);
                }
                $data['banner_image'] = $uploaded;
            } elseif ($this->input('banner_remove') === '1') {
                if (!empty($existing['banner_image'])) {
                    Upload::delete($existing['banner_image']);
                }
                $data['banner_image'] = '';
            } elseif ($data['banner_image'] === '' && !empty($existing['banner_image'])) {
                $data['banner_image'] = (string) $existing['banner_image'];
            }
        } catch (\RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/admin/locations/' . $id . '/edit');
        }
        Database::run(
            'UPDATE locations SET state=?, city=?, area_name=?, description=?, banner_image=?, map_link=?, status=? WHERE id = ?',
            [$data['state'], $data['city'] ?: null, $data['area_name'], $data['description'] ?: null,
             $data['banner_image'] ?: null, $data['map_link'] ?: null, $data['status'] ?: 'active', $id]
        );
        flash('success', __('admin.save'));
        redirect('/admin/locations');
    }

    private function collect(): array
    {
        return [
            'state'        => trim((string) $this->input('state', '')),
            'city'         => trim((string) $this->input('city', '')),
            'area_name'    => trim((string) $this->input('area_name', '')),
            'description'  => trim((string) $this->input('description', '')),
            'banner_image' => trim((string) $this->input('banner_image', '')),
            'map_link'     => trim((string) $this->input('map_link', '')),
            'status'       => (string) $this->input('status', 'active'),
        ];
    }
}
