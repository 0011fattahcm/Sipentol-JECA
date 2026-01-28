<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;

class DashboardController extends AppController
{
    public function index()
    {
        $this->request->allowMethod(['get']);

        $identity = $this->Authentication->getIdentity();
        $userId = $identity ? (int)$identity->get('id') : 0;

        $pengumuman = [];
        $announcements = [];

        if ($userId <= 0) {
            $this->set(compact('identity', 'pengumuman', 'announcements'));
            return;
        }

        $Users = FactoryLocator::get('Table')->get('Users');
        $user  = $Users->get($userId);

        // ====== TAHAP 1: default ======
        $currentStatus = 'pendaftaran';

        // ====== ambil data pendukung ======
        $now = FrozenTime::now();

        // 1) Pendaftaran (cek apakah user sudah submit form pendaftaran)
        $hasPendaftaran = false;
        try {
            $Pendaftarans = FactoryLocator::get('Table')->get('Pendaftarans');
            $hasPendaftaran = $Pendaftarans->find()
                ->where(['user_id' => $userId])
                ->count() > 0;
        } catch (\Throwable $e) {
            $hasPendaftaran = false;
        }

        // 2) Online Test
        $ot = null;
        try {
            $OnlineTests = FactoryLocator::get('Table')->get('OnlineTests');
            $ot = $OnlineTests->find()
                ->where(['user_id' => $userId])
                ->first();
        } catch (\Throwable $e) {
            $ot = null;
        }

        // 3) Daftar Ulang
        $du = null;
        try {
            $DaftarUlangs = FactoryLocator::get('Table')->get('DaftarUlangs');
            $du = $DaftarUlangs->find()
                ->where(['user_id' => $userId])
                ->orderDesc('id')
                ->first();
        } catch (\Throwable $e) {
            $du = null;
        }

        // ====== NORMALISASI STATUS LAMA (compat) ======
        $legacyStatus = strtolower(trim((string)($user->status ?? '')));

        // ====== RULE PRIORITY ======
        // 6. Onboarding: daftar ulang sudah diverifikasi admin
        if ($du && (string)$du->status === 'verified') {
            $currentStatus = 'onboarding';
        } elseif (in_array($legacyStatus, ['onboarding', 'aktif'], true)) {
            $currentStatus = 'onboarding';

        // 5. Daftar Ulang: form daftar ulang sudah dibuka admin
        } elseif ($du || in_array($legacyStatus, ['daftar_ulang', 'need_fix', 'lulus_tes'], true)) {
            $currentStatus = 'daftar_ulang';

        } else {
            // 4. Menunggu Hasil
            if ($ot) {
                $hasSchedule = !empty($ot->schedule_start) && !empty($ot->schedule_end);
                $hasAccessId = !empty($ot->test_access_id);

                $end = $ot->schedule_end ? FrozenTime::parse($ot->schedule_end) : null;
                $otStatus = strtolower(trim((string)($ot->status ?? '')));

                if ($otStatus === 'closed') {
                    $currentStatus = 'menunggu_hasil';
                } elseif ($end && $now->gt($end)) {
                    $currentStatus = 'menunggu_hasil';
                } elseif ($hasSchedule && $hasAccessId) {
                    // 3. Tes
                    $currentStatus = 'tes';
                }
            }

            // 2. Menunggu Tes
            if ($currentStatus === 'pendaftaran' && $hasPendaftaran) {
                $currentStatus = 'menunggu_tes';
            }
        }

        // ====== simpan ke users.status supaya konsisten di seluruh UI ======
        if ($currentStatus !== strtolower(trim((string)($user->status ?? '')))) {
            $user->status = $currentStatus;
            $Users->save($user);
        }

        // Untuk konsistensi antar halaman:
        // - expose entity terbaru sebagai `authUser`
        // - coba refresh Authentication identity untuk request ini
        $this->set('authUser', $user);
        try {
            $this->Authentication->setIdentity($user);
            $identity = $this->Authentication->getIdentity();
        } catch (\Throwable $e) {
            // fallback: view akan baca dari authUser
        }

        // ====== Pengumuman ======
        try {
            $Pengumuman = FactoryLocator::get('Table')->get('Pengumuman');

            $rows = $Pengumuman->find()
                ->where(['Pengumuman.is_active' => 1])
                ->orderDesc('Pengumuman.id')
                ->limit(50)
                ->enableHydration(false)
                ->all()
                ->toArray();

            foreach ($rows as $r) {
                $target = strtolower(trim((string)($r['target'] ?? 'semua')));

                if ($target === 'semua') {
                    $announcements[] = $r;
                } elseif ($target === 'tertentu') {
                    $raw = $r['target_user_ids'] ?? '';

                    $ids = [];
                    if (is_array($raw)) {
                        $ids = $raw;
                    } else {
                        $rawStr = trim((string)$raw);
                        if ($rawStr !== '') {
                            $json = json_decode($rawStr, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                                $ids = $json;
                            } else {
                                $ids = array_filter(array_map('trim', explode(',', $rawStr)));
                            }
                        }
                    }

                    $idsInt = array_map('intval', $ids);

                    if (in_array($userId, $idsInt, true)) {
                        $announcements[] = $r;
                    }
                }

                if (count($announcements) >= 5) break;
            }

            $pengumuman = array_slice($announcements, 0, 5);
        } catch (\Throwable $e) {
            $pengumuman = [];
            $announcements = [];
        }

        $this->set(compact('identity', 'pengumuman', 'announcements'));
    }
}
