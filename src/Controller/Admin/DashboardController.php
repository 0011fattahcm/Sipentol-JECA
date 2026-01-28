<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Datasource\FactoryLocator;

class DashboardController extends AppController
{
    public function index()
    {
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setLayout('admin');

        $tableLocator = FactoryLocator::get('Table');

        $Users        = $tableLocator->get('Users');
        $OnlineTests  = $tableLocator->get('OnlineTests');
        $Pengumuman   = $tableLocator->get('Pengumuman');
        $ActivityLogs = $tableLocator->get('ActivityLogs'); // table logs
        // optional tables (kalau ada di project)
        $Pendaftarans = null;
        $DaftarUlangs = null;

        // Safe-get table jika memang ada (tidak bikin error)
        try { $Pendaftarans = $tableLocator->get('Pendaftarans'); } catch (\Throwable $e) {}
        try { $DaftarUlangs = $tableLocator->get('DaftarUlangs'); } catch (\Throwable $e) {}

        // ===================== KPI =====================
        $totalUsers       = (int)$Users->find()->count();
        $totalOnlineTests = (int)$OnlineTests->find()->count();

        $activePengumuman = (int)$Pengumuman->find()
            ->where(['Pengumuman.is_active' => 1])
            ->count();

        // ===================== PENDING PROSES (defensive) =====================
        $pendingPendaftaran = 0;
        if ($Pendaftarans) {
            $cols = $Pendaftarans->getSchema()->columns();
            if (in_array('status', $cols, true)) {
                $pendingPendaftaran = (int)$Pendaftarans->find()->where(['status' => 'pending'])->count();
            } elseif (in_array('is_verified', $cols, true)) {
                $pendingPendaftaran = (int)$Pendaftarans->find()->where(['is_verified' => 0])->count();
            } else {
                // kalau belum jelas definisi "pending", aman: 0
                $pendingPendaftaran = 0;
            }
        }

        $pendingDaftarUlang = 0;
        if ($DaftarUlangs) {
            $cols = $DaftarUlangs->getSchema()->columns();
            if (in_array('status', $cols, true)) {
                $pendingDaftarUlang = (int)$DaftarUlangs->find()->where(['status' => 'pending'])->count();
            } elseif (in_array('is_verified', $cols, true)) {
                $pendingDaftarUlang = (int)$DaftarUlangs->find()->where(['is_verified' => 0])->count();
            } else {
                $pendingDaftarUlang = 0;
            }
        }

        // ===================== TES ONLINE OPEN / WAITING (defensive) =====================
        $tesOpenCount    = 0;
        $tesWaitingCount = 0;

        $otCols = $OnlineTests->getSchema()->columns();

        if (in_array('status', $otCols, true)) {
            $tesOpenCount    = (int)$OnlineTests->find()->where(['status' => 'open'])->count();
            $tesWaitingCount = (int)$OnlineTests->find()->where(['status' => 'waiting'])->count();
        } elseif (in_array('is_open', $otCols, true)) {
            $tesOpenCount    = (int)$OnlineTests->find()->where(['is_open' => 1])->count();
            $tesWaitingCount = (int)$OnlineTests->find()->where(['is_open' => 0])->count();
        } else {
            // fallback: tidak bisa menentukan open/waiting
            $tesOpenCount = 0;
            $tesWaitingCount = 0;
        }

        // ===================== LIST: USER TERBARU =====================
        $latestUsers = $Users->find()
            ->select(['id', 'nama_lengkap', 'email', 'created'])
            ->orderDesc('Users.id')
            ->limit(5)
            ->all();

        // ===================== LIST: PENGUMUMAN TERBARU =====================
        // NOTE: view kamu memakai $p->isi, jadi wajib disertakan.
        $latestPengumuman = $Pengumuman->find()
            ->select(['id', 'judul', 'isi', 'target', 'is_active', 'created_at'])
            ->orderDesc('Pengumuman.id')
            ->limit(5)
            ->all();

        // ===================== LIST: AKTIVITAS TERBARU =====================
        // logs table: logs(user_id, aktivitas, created) -> contain Users (email, nama_lengkap)
        $latestActivity = $ActivityLogs->find()
            ->contain([
                'Users' => function ($q) {
                    return $q->select(['Users.id', 'Users.email', 'Users.nama_lengkap']);
                }
            ])
            ->select(['ActivityLogs.id', 'ActivityLogs.user_id', 'ActivityLogs.aktivitas', 'ActivityLogs.created'])
            ->orderDesc('ActivityLogs.id')
            ->limit(6)
            ->all();

        // ===================== GRAFIK PENDAFTAR PER BULAN (12 bulan) =====================
$pendaftarLabels = [];
$pendaftarCounts = [];

if ($Pendaftarans) {
    // range 12 bulan terakhir (format YYYY-MM)
    $start = (new \DateTime('first day of -11 months'))->setTime(0, 0, 0);
    $end   = (new \DateTime('last day of this month'))->setTime(23, 59, 59);

    // list key bulan untuk di-fill 0 kalau tidak ada data
    $months = [];
    $cursor = clone $start;
    while ($cursor <= $end) {
        $key = $cursor->format('Y-m');
        $months[$key] = 0;
        $cursor->modify('+1 month');
    }

    // NOTE: group by month pada kolom created.
    // Ini pakai DATE_FORMAT created. (MySQL/MariaDB)
    $rows = $Pendaftarans->find()
        ->select([
            'ym' => $Pendaftarans->find()->func()->date_format([
                'Pendaftarans.created' => 'identifier',
                "'%Y-%m'" => 'literal',
            ]),
            'total' => $Pendaftarans->find()->func()->count('*')
        ])
        ->where([
            'Pendaftarans.created >=' => $start->format('Y-m-d H:i:s'),
            'Pendaftarans.created <=' => $end->format('Y-m-d H:i:s'),
        ])
        ->group('ym')
        ->orderAsc('ym')
        ->enableHydration(false)
        ->all();

    foreach ($rows as $r) {
        $ym = $r['ym'] ?? null;
        if ($ym && array_key_exists($ym, $months)) {
            $months[$ym] = (int)($r['total'] ?? 0);
        }
    }

    // label tampilan: "Jan 2026" dll
    foreach ($months as $ym => $count) {
        $dt = \DateTime::createFromFormat('Y-m', $ym);
        $pendaftarLabels[] = $dt ? $dt->format('M Y') : $ym;
        $pendaftarCounts[] = (int)$count;
    }
}

// nanti di set()
    

        $this->set(compact(
            'totalUsers',
            'totalOnlineTests',
            'activePengumuman',
            'pendingPendaftaran',
            'pendingDaftarUlang',
            'tesOpenCount',
            'tesWaitingCount',
            'latestUsers',
            'latestPengumuman',
            'latestActivity',
            'pendaftarLabels',
            'pendaftarCounts',
        ));
    }
}
