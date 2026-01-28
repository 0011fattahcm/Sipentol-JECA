<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

class SettingsController extends AppController
{
    public function onboarding()
    {
        $this->request->allowMethod(['get', 'post']);

        /** @var \App\Model\Table\SiteSettingsTable $SiteSettings */
        $SiteSettings = $this->fetchTable('SiteSettings');

        // === Standarisasi KEY ===
        $keys = [
            'onboarding_group_url',
            'onboarding_admin_wa_1',
            'onboarding_admin_wa_2',
        ];

        // Ambil nilai existing untuk ditampilkan di form
        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = (string)($SiteSettings->getValue($k) ?? '');
        }

        if ($this->request->is('post')) {
            $data = (array)$this->request->getData();

            // Bersihin input
            $groupUrl = trim((string)($data['onboarding_group_url'] ?? ''));
            $wa1 = preg_replace('/\D+/', '', (string)($data['onboarding_admin_wa_1'] ?? ''));
            $wa2 = preg_replace('/\D+/', '', (string)($data['onboarding_admin_wa_2'] ?? ''));

            // Simpan (upsert)
            $SiteSettings->setValue('onboarding_group_url', $groupUrl);
            $SiteSettings->setValue('onboarding_admin_wa_1', $wa1);
            $SiteSettings->setValue('onboarding_admin_wa_2', $wa2);

            $this->Flash->success('Onboarding settings berhasil disimpan.');

            return $this->redirect(['action' => 'onboarding']);
        }

        $this->set(compact('settings'));
        $this->viewBuilder()->setLayout('admin');
    }
}
