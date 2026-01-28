<?php
/** @var \App\View\AppView $this */

$u = $authUser ?? $identity;

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

$nama  = (string)$get($u, 'nama_lengkap', 'Peserta');
$email = (string)$get($u, 'email', '');

$status = strtolower(trim((string)$get($u, 'status', 'pendaftaran')));
// normalisasi kalau ada sisa status lama
$legacyMap = [
  'aktif' => 'onboarding',
  'lulus_tes' => 'daftar_ulang',
  'need_fix' => 'daftar_ulang',
];
if (isset($legacyMap[$status])) $status = $legacyMap[$status];

// label UI
$statusLabelMap = [
  'pendaftaran'     => 'Pendaftaran',
  'menunggu_tes'    => 'Menunggu Tes',
  'tes'             => 'Tes Online',
  'menunggu_hasil'  => 'Menunggu Hasil',
  'daftar_ulang'    => 'Daftar Ulang',
  'onboarding'      => 'Onboarding',
];

// urutan step untuk pills
$steps = [
  ['key'=>'pendaftaran',    'label'=>'Pendaftaran'],
  ['key'=>'menunggu_tes',   'label'=>'Menunggu Tes'],
  ['key'=>'tes',            'label'=>'Tes Online'],
  ['key'=>'menunggu_hasil', 'label'=>'Hasil'],
  ['key'=>'daftar_ulang',   'label'=>'Daftar Ulang'],
  ['key'=>'onboarding',     'label'=>'Onboarding'],
];

// index step aktif (biar selalu valid)
$stepIndexMap = [];
foreach ($steps as $i => $s) $stepIndexMap[$s['key']] = $i;
$currentIndex = $stepIndexMap[$status] ?? 0;

// text status (INI YANG SEBELUMNYA MISSING)
$statusText = $statusLabelMap[$status] ?? 'Pendaftaran';

// persentase progress (0..100)
$progressPercent = (count($steps) > 1)
  ? (int) round(($currentIndex / (count($steps)-1)) * 100)
  : 0;

// badge status buat sidebar chip
$badge = [
  'pendaftaran'    => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
  'menunggu_tes'   => 'bg-amber-50 text-amber-700 ring-amber-200',
  'tes'            => 'bg-sky-50 text-sky-700 ring-sky-200',
  'menunggu_hasil' => 'bg-slate-50 text-slate-700 ring-slate-200',
  'daftar_ulang'   => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
  'onboarding'     => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
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

// Akses menu
$canFormPendaftaran = in_array($status, ['pendaftaran'], true);
$canTes            = in_array($status, ['menunggu_tes','tes','menunggu_hasil'], true);
$canDaftarUlang    = in_array($status, ['daftar_ulang','onboarding'], true);
$canOnboarding     = in_array($status, ['onboarding'], true);

// Next action card
$nextTitle = 'Lengkapi Pendaftaran';
$nextDesc  = 'Lengkapi data awal agar Anda bisa diproses ke tahap berikutnya.';
$nextBtn   = [
  'text' => 'Buka Form Pendaftaran',
  'enabled' => $canFormPendaftaran,
  'href' => $this->Url->build('/pendaftaran')
];

if ($status === 'menunggu_tes') {
  $nextTitle = 'Menunggu Tes Online';
  $nextDesc  = 'Tes akan dibuka oleh admin setelah ID & jadwal tersedia. Pantau informasi di menu Tes Online.';
  $nextBtn   = ['text' => 'Lihat Tes Online', 'enabled' => $canTes, 'href' => $this->Url->build('/tes-online')];
}

if ($status === 'tes') {
  $nextTitle = 'Tes Online Aktif';
  $nextDesc  = 'ID & jadwal sudah tersedia. Silakan masuk tes sesuai instruksi. Pastikan koneksi stabil.';
  $nextBtn   = ['text' => 'Buka Tes Online', 'enabled' => true, 'href' => $this->Url->build('/tes-online')];
}

if ($status === 'menunggu_hasil') {
  $nextTitle = 'Menunggu Hasil';
  $nextDesc  = 'Jadwal tes sudah berakhir. Silakan tunggu pengumuman hasil dari admin.';
  $nextBtn   = ['text' => 'Lihat Pengumuman', 'enabled' => true, 'href' => $this->Url->build('/dashboard') . '#pengumuman'];
}

if ($status === 'daftar_ulang') {
  $nextTitle = 'Daftar Ulang Dibuka';
  $nextDesc  = 'Silakan lengkapi proses daftar ulang sesuai instruksi admin.';
  $nextBtn   = ['text' => 'Buka Daftar Ulang', 'enabled' => true, 'href' => $this->Url->build('/daftar-ulang')];
}

if ($status === 'onboarding') {
  $nextTitle = 'Onboarding Aktif';
  $nextDesc  = 'Daftar ulang Anda sudah diverifikasi. Silakan akses materi & informasi kelas.';
  $nextBtn   = ['text' => 'Buka Onboarding', 'enabled' => true, 'href' => $this->Url->build('/onboarding')];
}

// sidebar nav config
$nav = [
  ['label' => 'Dashboard',        'href' => $this->Url->build('/dashboard'),   'active' => true,  'enabled' => true],
  ['label' => 'Form Pendaftaran', 'href' => $this->Url->build('/pendaftaran'), 'active' => false, 'enabled' => $canFormPendaftaran],
  ['label' => 'Tes Online',       'href' => $this->Url->build('/tes-online'),  'active' => false, 'enabled' => $canTes],
  ['label' => 'Daftar Ulang',     'href' => $this->Url->build('/daftar-ulang'),'active' => false, 'enabled' => $canDaftarUlang],
  ['label' => 'Onboarding',       'href' => $this->Url->build('/onboarding'),  'active' => false, 'enabled' => $canOnboarding],
];
?>

<div class="min-h-screen bg-slate-950">
  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
    <div class="absolute top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="lg:hidden flex items-center justify-between mb-6">
      <div>
        <div class="text-white text-xl font-semibold">SiPentol</div>
        <div class="text-slate-300 text-sm">Dashboard Peserta</div>
      </div>
      <button
        type="button"
        class="inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-2 ring-1 ring-white/10"
        onclick="document.getElementById('mobileSidebar').classList.remove('hidden')"
      >
        Menu
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

      <?= $this->element('dashboard/sidebar', compact('nama','email','initials','badgeClass','statusText','nav')) ?>

      <section class="lg:col-span-9 space-y-6">
        <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <div class="text-slate-300 text-sm">Selamat datang,</div>
              <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                Dashboard Peserta
              </h1>
              <p class="text-slate-300 mt-2">
                Pantau tahapan Anda dan lanjutkan langkah berikutnya.
              </p>
            </div>

            <div class="rounded-2xl bg-white/10 ring-1 ring-white/10 px-5 py-4 text-center min-w-[140px]">
              <div class="text-xs text-slate-300">Progress</div>
              <div class="text-white text-2xl font-bold mt-1"><?= (int)$progressPercent ?>%</div>
            </div>
          </div>
        </div>

        <!-- Progres Tahapan -->
        <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h2 class="text-lg font-semibold text-slate-900">Progres Tahapan</h2>
              <p class="text-sm text-slate-600 mt-1">Menu akan terbuka otomatis sesuai status Anda.</p>
            </div>

            <div class="min-w-[260px]">
              <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2.5 bg-gradient-to-r from-indigo-600 to-sky-500" style="width: <?= (int)$progressPercent ?>%"></div>
              </div>
              <div class="mt-2 text-xs text-slate-500">
                Saat ini: <span class="font-semibold text-slate-800"><?= h($statusText) ?></span>
              </div>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-2 md:grid-cols-6 gap-3">
            <?php foreach ($steps as $i => $s): ?>
              <?php
                $done = $i < $currentIndex;
                $now  = $i === $currentIndex;

                $dot = $done ? 'bg-indigo-600' : ($now ? 'bg-slate-900' : 'bg-slate-300');
                $txt = $done ? 'text-slate-700' : ($now ? 'text-slate-900 font-semibold' : 'text-slate-500');
                $card = $now ? 'bg-white ring-slate-300' : 'bg-slate-50 ring-slate-200';
              ?>
              <div class="rounded-2xl <?= $card ?> ring-1 px-4 py-3">
                <div class="flex items-center gap-2">
                  <span class="h-2.5 w-2.5 rounded-full <?= $dot ?>"></span>
                  <span class="text-xs <?= $txt ?>"><?= h($s['label']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Pengumuman -->
        <div id="pengumuman" class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
          <div class="text-lg font-bold text-slate-900">Pengumuman</div>
          <div class="text-sm text-slate-500 mt-1">Informasi terbaru dari admin.</div>

          <div class="mt-4 space-y-3">
            <?php if (!empty($pengumuman)): ?>
              <?php foreach ($pengumuman as $p): ?>
                <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50 via-blue-50 to-sky-50 border border-indigo-100 shadow-sm">
                  <div class="font-bold text-slate-900"><?= h($p['judul'] ?? '-') ?></div>
                  <div class="text-slate-600 mt-1"><?= nl2br(h($p['isi'] ?? '')) ?></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-slate-500">Belum ada pengumuman.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Next action -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-500 text-white shadow-xl shadow-indigo-500/20 overflow-hidden">
            <div class="p-6 md:p-7">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h2 class="text-lg md:text-xl font-bold"><?= h($nextTitle) ?></h2>
                  <p class="text-white/85 mt-2 text-sm md:text-base"><?= h($nextDesc) ?></p>
                </div>

                <div class="hidden md:flex h-12 w-12 rounded-2xl bg-white/15 ring-1 ring-white/20 items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>

              <div class="mt-5 flex flex-col sm:flex-row sm:items-center gap-3">
                <a href="<?= h($nextBtn['href']) ?>"
                  class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold transition
                  <?= $nextBtn['enabled'] ? 'bg-white text-slate-900 hover:bg-white/90' : 'bg-white/30 text-white/60 cursor-not-allowed' ?>"
                  <?= $nextBtn['enabled'] ? '' : 'aria-disabled="true" onclick="return false;"' ?>
                >
                  <?= h($nextBtn['text']) ?>
                </a>

                <div class="text-xs text-white/80">
                  Notifikasi utama akan dikirim via email.
                </div>
              </div>

              <div class="mt-6 rounded-2xl bg-white/15 ring-1 ring-white/20 p-4 text-sm text-white/90">
                <div class="font-semibold">Catatan</div>
                <div class="mt-1">
                  Tahapan akan berubah otomatis setelah admin memproses.
                </div>
              </div>
            </div>
          </div>

          <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
            <div class="text-lg font-bold text-slate-900">Informasi Akun</div>
            <div class="mt-4 space-y-3">
              <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="text-xs text-slate-500">Nama</div>
                <div class="font-semibold text-slate-900 mt-1"><?= h($nama) ?></div>
              </div>
              <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="text-xs text-slate-500">Email</div>
                <div class="font-semibold text-slate-900 mt-1 break-words"><?= h($email ?: '-') ?></div>
              </div>
              <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="text-xs text-slate-500">Status</div>
                <div class="font-semibold text-slate-900 mt-1"><?= h($statusText) ?></div>
              </div>
            </div>
          </div>
        </div>

      </section>
      <?= $this->element('dashboard/mobile_sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'dashboard']) ?>

    </div>
  </div>
</div>
