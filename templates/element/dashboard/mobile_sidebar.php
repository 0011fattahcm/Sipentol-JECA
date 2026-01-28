<?php
/**
 * Mobile Sidebar (Single Source of Truth)
 *
 * Problem:
 * - Banyak halaman kirim $nav yang formatnya beda-beda (href vs url vs hardcode '#')
 * - Akibatnya beberapa menu (Tes Online, Onboarding) jadi placeholder dan tidak bisa diklik di mobile.
 *
 * Fix:
 * - Mobile sidebar generate nav sendiri berdasarkan status user terbaru.
 * - Tetap menerima $active untuk highlight.
 */

/** @var \App\View\AppView $this */
/** @var \Authentication\IdentityInterface|\Cake\Datasource\EntityInterface|null $identity */
/** @var \Cake\Datasource\EntityInterface|null $authUser */
/** @var string|null $active */

$active = $active ?? 'dashboard';

// getter aman
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

// normalisasi status legacy
$legacyMap = [
    'aktif'     => 'onboarding',
    'lulus_tes' => 'daftar_ulang',
    'need_fix'  => 'daftar_ulang',
    'tes_aktif' => 'tes',
];
if (isset($legacyMap[$status])) $status = $legacyMap[$status];

// label status
$statusLabel = [
    'pendaftaran'    => 'Pendaftaran',
    'menunggu_tes'   => 'Menunggu Tes',
    'tes'            => 'Tes Online',
    'menunggu_hasil' => 'Menunggu Hasil',
    'daftar_ulang'   => 'Daftar Ulang',
    'onboarding'     => 'Onboarding',
];
$statusText = $statusLabel[$status] ?? ($rawStatus ?: 'Pendaftaran');

// badge
$badge = [
    'pendaftaran'    => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'menunggu_tes'   => 'bg-amber-50 text-amber-700 ring-amber-200',
    'tes'            => 'bg-sky-50 text-sky-700 ring-sky-200',
    'menunggu_hasil' => 'bg-slate-50 text-slate-700 ring-slate-200',
    'daftar_ulang'   => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'onboarding'     => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
];
$badgeClass = $badge[$status] ?? 'bg-slate-50 text-slate-700 ring-slate-200';

// initials
$initials = 'U';
if ($nama) {
    $parts = preg_split('/\s+/', trim($nama));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = mb_substr($parts[count($parts)-1] ?? '', 0, 1);
    $initials = mb_strtoupper($first . ($last ?: ''));
}

// foto dari pendaftarans (optional)
$fotoUrl = $fotoUrl ?? null;
$userId = 0;
try {
    if ($identity && is_object($identity) && method_exists($identity, 'getIdentifier')) {
        $userId = (int)$identity->getIdentifier();
    } else {
        $userId = (int)$get($u, 'id', 0);
    }
} catch (\Throwable $e) {
    $userId = (int)$get($u, 'id', 0);
}

if ($fotoUrl === null && $userId > 0) {
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

/**
 * Enable menu berdasarkan stage.
 * Aturan sederhana: user boleh akses menu sampai stage current.
 */
$stageOrder = [
    'pendaftaran'    => 1,
    'menunggu_tes'   => 2,
    'tes'            => 3,
    'menunggu_hasil' => 4,
    'daftar_ulang'   => 5,
    'onboarding'     => 6,
];
$currStage = $stageOrder[$status] ?? 1;

$nav = [
    ['key' => 'dashboard',   'label' => 'Dashboard',        'href' => $this->Url->build('/dashboard'),     'minStage' => 1],
    ['key' => 'pendaftaran', 'label' => 'Form Pendaftaran', 'href' => $this->Url->build('/pendaftaran'),  'minStage' => 1],
    ['key' => 'tes',         'label' => 'Tes Online',       'href' => $this->Url->build('/tes-online'),   'minStage' => 3],
    ['key' => 'daftarulang', 'label' => 'Daftar Ulang',     'href' => $this->Url->build('/daftar-ulang'), 'minStage' => 5],
    ['key' => 'onboarding',  'label' => 'Onboarding',       'href' => $this->Url->build('/onboarding'),   'minStage' => 6],
];
?>

<div id="mobileSidebar" class="hidden lg:hidden fixed inset-0 z-50">
    <!-- overlay -->
    <div class="absolute inset-0 bg-black/60 z-40"
         onclick="document.getElementById('mobileSidebar').classList.add('hidden')"></div>

    <!-- panel -->
    <div class="absolute left-0 top-0 bottom-0 z-50 w-[86%] max-w-sm bg-slate-950 text-white ring-1 ring-white/10">
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div class="font-semibold">Menu</div>
            <button type="button"
                class="rounded-xl bg-white/10 hover:bg-white/15 px-3 py-2 ring-1 ring-white/10"
                onclick="document.getElementById('mobileSidebar').classList.add('hidden')">
                Tutup
            </button>
        </div>

        <div class="p-5">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl overflow-hidden bg-white/10 ring-1 ring-white/10">
                    <?php if (!empty($fotoUrl)): ?>
                        <img src="<?= h($fotoUrl) ?>" alt="Foto Profil" class="h-full w-full object-cover" loading="lazy">
                    <?php else: ?>
                        <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-sky-500 flex items-center justify-center font-bold">
                            <?= h($initials) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="min-w-0">
                    <div class="font-semibold truncate"><?= h($nama) ?></div>
                    <div class="text-slate-300 text-sm truncate"><?= h($email) ?></div>
                </div>
            </div>

            <div class="mt-4 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 <?= h($badgeClass) ?>">
                <span class="h-2 w-2 rounded-full <?= $status === 'onboarding' ? 'bg-emerald-500' : 'bg-indigo-500' ?>"></span>
                <?= h($statusText) ?>
            </div>

            <div class="mt-5 space-y-1">
                <?php foreach ($nav as $item): ?>
                    <?php
                        $isActive = ((string)($item['key'] ?? '') === (string)$active);
                        $minStage = (int)($item['minStage'] ?? 1);
                        $enabled  = ($currStage >= $minStage);

                        $href = $enabled ? (string)$item['href'] : '#';

                        $base = 'group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition ring-1 ring-transparent focus:outline-none focus:ring-white/20';
                        if ($isActive) {
                            $cls = 'bg-white text-slate-900 shadow-sm hover:bg-slate-50 ring-white/10';
                        } else {
                            $cls = $enabled
                                ? 'text-slate-200 hover:bg-white/10 hover:text-white'
                                : 'text-slate-500 bg-white/5 cursor-not-allowed';
                        }

                        // kalau enabled, klik juga menutup sidebar (UX)
                        $extraAttrs = $enabled
                            ? 'onclick="document.getElementById(\'mobileSidebar\').classList.add(\'hidden\')"'
                            : 'onclick="return false;" title="Menu akan terbuka sesuai status Anda"';
                    ?>
                    <a href="<?= h($href) ?>" class="<?= $base . ' ' . $cls ?>" <?= $extraAttrs ?>>
                        <span><?= h((string)$item['label']) ?></span>
                        <span class="<?= $isActive ? 'text-slate-400' : 'text-slate-500 group-hover:text-slate-300' ?>">›</span>
                    </a>
                <?php endforeach; ?>

                <div class="pt-3 mt-3 border-t border-white/10">
                    <button type="button"
                        class="js-logout-btn w-full inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-3 text-sm font-semibold ring-1 ring-white/10 transition">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
