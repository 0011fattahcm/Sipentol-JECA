<?php
declare(strict_types=1);

namespace App\Controller\Admin;

class OnboardingSettingsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('admin');
    }

    public function index()
    {
        $this->request->allowMethod(['get','post','put','patch']);

        $SiteSettings = $this->fetchTable('SiteSettings');

        $get = function(string $key, string $default = '') use ($SiteSettings): string {
            $row = $SiteSettings->find()->where(['key_name' => $key])->first();
            return $row ? (string)$row->value : $default;
        };

        $set = function(string $key, string $value) use ($SiteSettings): void {
            $row = $SiteSettings->find()->where(['key_name' => $key])->first();
            if (!$row) {
                $row = $SiteSettings->newEmptyEntity();
                $row->key_name = $key;
            }
            $row->value = $value;
            $SiteSettings->saveOrFail($row);
        };

        $data = [
            'group_url' => $get('onboarding_group_url', ''),
            'admin_wa_1' => $get('onboarding_admin_wa_1', ''),
            'admin_wa_2' => $get('onboarding_admin_wa_2', ''),
        ];

        if ($this->request->is(['post','put','patch'])) {
            $payload = $this->request->getData();

            $set('onboarding_group_url', trim((string)($payload['group_url'] ?? '')));
            $set('onboarding_admin_wa_1', trim((string)($payload['admin_wa_1'] ?? '')));
            $set('onboarding_admin_wa_2', trim((string)($payload['admin_wa_2'] ?? '')));

            $this->Flash->success('Onboarding settings berhasil disimpan.');
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('data'));
    }
}
