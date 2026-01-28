<?php
/**
 * Sidebar peserta (desktop)
 *
 * Root cause bug:
 * - Dashboard kadang memakai entity Users terbaru (status sudah "onboarding")
 * - Halaman lain masih memakai Authentication identity dari session (status lama)
 *
 * Solusi: prioritaskan `authUser` (fresh dari DB, diset di AppController::beforeFilter).
 */

/** @var \App\View\AppView $this */
/** @var \Authentication\IdentityInterface|\Cake\Datasource\EntityInterface|null $identity */
/** @var \App\Model\Entity\User|\Cake\Datasource\EntityInterface|null $authUser */
/** @var string|null $active */

$active = $active ?? 'dashboard';

// getter aman untuk IdentityInterface maupun Entity
$get = function ($obj, string $field, $default = '') {
    if (!$obj) return $default;
    try {
        if (is_object($obj) && method_exists($obj, 'get')) {
            $v = $obj->get($field);
            return ($v === null || $v === '') ? $default : $v;
        }
        if (is_object($obj) && isset($obj->{$field})) {
            $v = $obj->{$field};
            return ($v === null || $v === '') ? $default : $v;
        }
    } catch (\Throwable $e) {
        return $default;
    }
    return $default;
};

// user paling update untuk UI
$u = $authUser ?? $identity;

$nama  = (string)$get($u, 'nama_lengkap', 'Peserta');
$email = (string)$get($u, 'email', '');

$rawStatus = (string)$get($u, 'status', 'pendaftaran');
$status = strtolower(trim($rawStatus));

// normalisasi status lama (compat)
$legacyMap = [
    'aktif'       => 'onboarding',
    'lulus_tes'   => 'daftar_ulang',
    'need_fix'    => 'daftar_ulang',
    'tes_aktif'   => 'tes',
];
if (isset($legacyMap[$status])) {
    $status = $legacyMap[$status];
}

$statusLabel = [
    'pendaftaran'      => 'Pendaftaran',
    'menunggu_tes'     => 'Menunggu Tes',
    'tes'              => 'Tes Online',
    'menunggu_hasil'   => 'Menunggu Hasil',
    'daftar_ulang'     => 'Daftar Ulang',
    'onboarding'       => 'Onboarding',

    // fallback lama
    'verifikasi_ulang' => 'Verifikasi Daftar Ulang',
    'tidak_lulus'      => 'Tidak Lulus',
];
$statusText = $statusLabel[$status] ?? ($rawStatus ?: 'Pendaftaran');

$badge = [
    'pendaftaran'      => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'menunggu_tes'     => 'bg-amber-50 text-amber-700 ring-amber-200',
    'tes'              => 'bg-sky-50 text-sky-700 ring-sky-200',
    'menunggu_hasil'   => 'bg-slate-50 text-slate-700 ring-slate-200',
    'daftar_ulang'     => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'onboarding'       => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'verifikasi_ulang' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'tidak_lulus'      => 'bg-rose-50 text-rose-700 ring-rose-200',
];
$badgeClass = $badge[$status] ?? 'bg-slate-50 text-slate-700 ring-slate-200';

// initials avatar
$initials = 'U';
if ($nama) {
    $parts = preg_split('/\s+/', trim($nama));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = mb_substr($parts[count($parts)-1] ?? '', 0, 1);
    $initials = mb_strtoupper($first . ($last ?: ''));
}

// Ambil pas foto dari tabel pendaftarans (kalau ada)
$fotoUrl = null;
$userId = null;
try {
    if ($identity && is_object($identity) && method_exists($identity, 'getIdentifier')) {
        $userId = (int)$identity->getIdentifier();
    } else {
        $userId = (int)$get($u, 'id', 0);
    }
} catch (\Throwable $e) {
    $userId = (int)$get($u, 'id', 0);
}

if (!empty($userId)) {
    try {
        $Pendaftarans = \Cake\Datasource\FactoryLocator::get('Table')->get('Pendaftarans');
        $row = $Pendaftarans->find()
            ->select(['pas_foto_path'])
            ->where(['user_id' => $userId])
            ->disableHydration()
            ->first();

        $fotoPath = $row['pas_foto_path'] ?? null;
        if (!empty($fotoPath)) {
            $fotoUrl = $this->Url->build('/' . ltrim((string)$fotoPath, '/'));
        }
    } catch (\Throwable $e) {
        $fotoUrl = null;
    }
}

$nav = [
    ['key' => 'dashboard',   'label' => 'Dashboard',        'href' => $this->Url->build('/dashboard'),     'enabled' => true],
    ['key' => 'pendaftaran', 'label' => 'Form Pendaftaran', 'href' => $this->Url->build('/pendaftaran'),  'enabled' => true],
    ['key' => 'tes',         'label' => 'Tes Online',       'href' => $this->Url->build('/tes-online'),   'enabled' => true],
    ['key' => 'daftarulang', 'label' => 'Daftar Ulang',     'href' => $this->Url->build('/daftar-ulang'), 'enabled' => true],
    ['key' => 'onboarding',  'label' => 'Onboarding',       'href' => $this->Url->build('/onboarding'),   'enabled' => true],
];
?>

<aside class="hidden lg:block lg:col-span-3">
    <div class="sticky top-6 rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl overflow-hidden bg-white/10 ring-1 ring-white/10 shadow-lg shadow-indigo-500/20">
                    <?php if (!empty($fotoUrl)): ?>
                        <img
                            src="<?= h($fotoUrl) ?>"
                            alt="Foto Profil"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-sky-500 text-white flex items-center justify-center font-bold">
                            <?= h($initials) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="min-w-0">
                    <div class="text-white font-semibold truncate"><?= h($nama) ?></div>
                    <div class="text-slate-300 text-sm truncate"><?= h($email) ?></div>
                </div>
            </div>

            <div class="mt-4 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 <?= h($badgeClass) ?>">
                <span class="h-2 w-2 rounded-full <?= $status === 'tidak_lulus' ? 'bg-rose-500' : ($status === 'onboarding' ? 'bg-emerald-500' : 'bg-indigo-500') ?>"></span>
                <?= h($statusText) ?>
            </div>
        </div>

        <nav class="p-3 space-y-1">
            <?php foreach ($nav as $item): ?>
                <?php
                    $isActive = ($item['key'] === $active);

                    $href = (string)($item['href'] ?? '#');
                    $isPlaceholder = ($href === '#');

                    $base = 'group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition ring-1 ring-transparent focus:outline-none focus:ring-white/20';
                    $cls = $isActive
                        ? 'bg-white text-slate-900 shadow-sm hover:bg-slate-50 ring-white/10'
                        : 'text-slate-200 hover:bg-white/10 hover:text-white';

                    $extraAttrs = $isPlaceholder ? 'onclick="return false;" title="Segera hadir"' : '';
                ?>
                <a href="<?= h($href) ?>"
                   class="<?= $base . ' ' . $cls ?>"
                   <?= $extraAttrs ?>
                >
                    <span><?= h($item['label']) ?></span>
                    <span class="<?= $isActive ? 'text-slate-400' : 'text-slate-500 group-hover:text-slate-300' ?>">›</span>
                </a>
            <?php endforeach; ?>

            <div class="pt-3 mt-3 border-t border-white/10">
                <button type="button"
                    class="js-logout-btn w-full inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-3 text-sm font-semibold ring-1 ring-white/10 transition">
                    Logout
                </button>
            </div>
        </nav>
    </div>
</aside>
