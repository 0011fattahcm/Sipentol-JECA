<?php
declare(strict_types=1);

namespace App\Controller;

class OnboardingController extends AppController
{
    public function index()
    {
        $this->request->allowMethod(['get']);

        $identity = $this->Authentication->getIdentity();
        $userId = (int)$identity->get('id');

        // 1) cek status daftar ulang user
        $DaftarUlangs = $this->fetchTable('DaftarUlangs');
        $daftarUlang = $DaftarUlangs->find()
            ->where(['user_id' => $userId])
            ->first();

        $reviewStatus = $daftarUlang
            ? (string)($daftarUlang->review_status ?? $daftarUlang->status ?? 'draft')
            : 'draft';

        $isVerified = ($reviewStatus === 'verified');

        // 2) ambil settings dari site_settings (admin input)
        /** @var \App\Model\Table\SiteSettingsTable $SiteSettings */
        $SiteSettings = $this->fetchTable('SiteSettings');

        $groupUrl = (string)($SiteSettings->getValue('onboarding_group_url') ?? '');
        $adminWa1 = (string)($SiteSettings->getValue('onboarding_admin_wa_1') ?? '');
        $adminWa2 = (string)($SiteSettings->getValue('onboarding_admin_wa_2') ?? '');

        $this->set(compact(
            'identity',
            'daftarUlang',
            'reviewStatus',
            'isVerified',
            'groupUrl',
            'adminWa1',
            'adminWa2'
        ));
    }
}
