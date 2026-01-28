<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\OnlineTest $onlineTest */
/** @var \Authentication\IdentityInterface|null $identity */

$this->assign('title', 'Tes Online');

// safety: kalau controller lupa set identity, coba ambil dari request
$identity = $identity ?? $this->request->getAttribute('identity');

$nama  = $identity ? (string)$identity->get('nama_lengkap') : 'Peserta';
$email = $identity ? (string)$identity->get('email') : '';

$initials = 'PP';
if (!empty($nama)) {
  $parts = preg_split('/\s+/', trim($nama));
  $first = strtoupper(mb_substr($parts[0] ?? 'P', 0, 1));
  $second = strtoupper(mb_substr($parts[1] ?? $parts[0] ?? 'P', 0, 1));
  $initials = $first . $second;
}

$statusBadge = $identity ? (string)$identity->get('status') : 'Pendaftaran';
$statusText  = $statusBadge ?: 'Pendaftaran';

$badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-200';
if (strtolower($statusText) === 'pendaftaran') $badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-200';

$nav = [
  ['key' => 'dashboard',    'label' => 'Dashboard',        'url' => ['controller' => 'Dashboard', 'action' => 'index'], 'enabled' => true],
  ['key' => 'pendaftaran',  'label' => 'Form Pendaftaran', 'url' => ['controller' => 'Pendaftarans', 'action' => 'form'], 'enabled' => true],
  ['key' => 'tes',          'label' => 'Tes Online',       'url' => ['controller' => 'Tes Online', 'action' => 'index'], 'enabled' => true],
  ['key' => 'daftar-ulang', 'label' => 'Daftar Ulang',     'url' => ['controller' => 'DaftarUlang', 'action' => 'index'], 'enabled' => true],
  ['key' => 'onboarding',   'label' => 'Onboarding',       'url' => ['controller' => 'Onboarding', 'action' => 'index'], 'enabled' => true],
];

$statusMap = [
  'waiting' => ['label' => 'Menunggu Jadwal dan ID', 'badge' => 'bg-amber-50 text-amber-800 ring-amber-200', 'dot' => 'bg-amber-500'],
  'open'    => ['label' => 'Tes sudah dibuka',       'badge' => 'bg-emerald-50 text-emerald-800 ring-emerald-200', 'dot' => 'bg-emerald-500'],
  'closed'  => ['label' => 'Tes ditutup / selesai',  'badge' => 'bg-slate-50 text-slate-700 ring-slate-200', 'dot' => 'bg-slate-500'],
];

$st = $statusMap[$onlineTest->status] ?? $statusMap['waiting'];

$start = $onlineTest->schedule_start;
$end   = $onlineTest->schedule_end;

$jadwalText = 'Belum ada jadwal.';
if ($start && $end) {
  $jadwalText = $start->i18nFormat('EEEE, dd MMMM yyyy') . ' • ' .
               $start->i18nFormat('HH:mm') . ' – ' . $end->i18nFormat('HH:mm') . ' WIB';
} elseif ($start && !$end) {
  $jadwalText = $start->i18nFormat('EEEE, dd MMMM yyyy • HH:mm') . ' WIB (jam selesai belum diisi)';
}

$testUrl = $onlineTest->test_url ?: 'https://test.jecaid.com';
$testId  = $onlineTest->test_access_id ?: '-';
$canOpen = ($onlineTest->status === 'open');

$lokasiText = 'Belum ada lokasi.';

// contoh nama field (silakan sesuaikan dengan SQL kamu)
$type   = $onlineTest->test_location_type ?? null;
$detail = trim((string)($onlineTest->test_location_detail ?? ''));

if ($type) {
  if ($type === 'lpk_jeca') {
    $lokasiText = 'LPK JECA';
  } elseif ($type === 'onsite') {
    $lokasiText = 'Onsite';
  } elseif ($type === 'online') {
    $lokasiText = 'Online';
  } elseif ($type === 'custom') {
    $lokasiText = $detail !== '' ? $detail : 'Lokasi luar belum diisi.';
  } else {
    // fallback kalau ada value di DB yang “nyasar”
    $lokasiText = $detail !== '' ? $detail : 'Lokasi belum diisi.';
  }
}
?>

<div class="min-h-screen bg-slate-950 text-white relative overflow-hidden">
  <!-- Blobs background (SAMA PERSIS DENGAN form.php) -->
  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full bg-indigo-600/20 blur-3xl"></div>
    <div class="absolute top-32 -right-24 h-[520px] w-[520px] rounded-full bg-purple-600/20 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 h-[520px] w-[520px] rounded-full bg-cyan-500/10 blur-3xl"></div>
  </div>

<!-- Container (SAMAIN WIDTH + PADDING) -->
<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  <!-- Mobile Topbar (WAJIB, samain seperti form.php) -->
  <div class="lg:hidden flex items-center justify-between mb-6 relative z-10">
    <div>
      <div class="text-white text-xl font-semibold">SiPentol</div>
      <div class="text-slate-300 text-sm">Tes Online</div>
    </div>

    <button
      type="button"
      class="inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-5 py-3 min-h-[44px] ring-1 ring-white/10"
      style="touch-action: manipulation;"
      onclick="document.getElementById('mobileSidebar').classList.remove('hidden')"
    >
      Menu
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

      <aside class="hidden lg:block lg:col-span-3">
        <?= $this->element('dashboard/sidebar', ['identity' => $identity, 'active' => 'tes']) ?>
      </aside>

      <!-- Content -->
      <main class="lg:col-span-9 space-y-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Tes Online</h1>
            <p class="text-slate-300 mt-1">Silakan akses tes sesuai jadwal dan gunakan ID yang diberikan admin.</p>
          </div>

          <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1 <?= h($st['badge']) ?>">
            <span class="h-2 w-2 rounded-full <?= h($st['dot']) ?>"></span>
            <?= h($st['label']) ?>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
          <!-- Link Tes -->
          <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-5">
            <h2 class="text-white font-semibold">Link Akses Tes</h2>
            <p class="text-slate-300 text-sm mt-1">Gunakan tombol di bawah untuk membuka halaman tes.</p>

            <div class="mt-4">
              <a
                href="<?= h($testUrl) ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold transition
                  <?= $canOpen ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-600/20' : 'bg-white/10 text-slate-300 pointer-events-none opacity-60' ?>"
                aria-disabled="<?= $canOpen ? 'false' : 'true' ?>"
              >
                Buka Tes (test.jecaid.com)
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10v11h11"/>
                </svg>
              </a>

              <?php if (!$canOpen): ?>
                <p class="text-xs text-slate-400 mt-2">
                  Tombol akan aktif setelah admin membuka tes dan memberikan jadwal serta ID.
                </p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Jadwal -->
          <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-5">
            <h2 class="text-white font-semibold">Jadwal Tes</h2>
            <p class="text-slate-300 text-sm mt-1">Jadwal akan diberikan oleh admin (hari, tanggal, dan rentang waktu).</p>

        
            
            <div class="mt-4 rounded-xl bg-white/5 ring-1 ring-white/10 p-4">
  <div class="text-xs text-slate-300">Jadwal</div>
  <div class="text-white font-semibold mt-1"><?= h($jadwalText) ?></div>

  <div class="mt-3 pt-3 border-t border-white/10">
    <div class="text-xs text-slate-300">Lokasi</div>
    <div class="text-white font-semibold mt-1"><?= h($lokasiText) ?></div>
  </div>
</div>


            <?php if (!empty($onlineTest->admin_note)): ?>
              <div class="mt-3 text-sm text-slate-300">
                <span class="text-slate-400">Catatan admin:</span> <?= h($onlineTest->admin_note) ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- ID Tes -->
          <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-5">
            <h2 class="text-white font-semibold">ID Tes</h2>
            <p class="text-slate-300 text-sm mt-1">ID ini diberikan oleh admin untuk login di halaman tes.</p>

            <div class="mt-4 flex items-center gap-2">
              <div class="flex-1 rounded-xl bg-white/5 ring-1 ring-white/10 px-4 py-3">
                <div class="text-xs text-slate-400">ID Anda</div>
                <div id="testIdText" class="text-white font-semibold mt-1 tracking-wide">
                  <?= h($testId) ?>
                </div>
              </div>

              <?php $btnDisabled = ($testId === '-' || trim((string)$testId) === ''); ?>
              <button
                type="button"
                class="rounded-xl px-4 py-3 text-sm font-semibold ring-1 ring-white/10 transition
                  <?= $btnDisabled ? 'bg-white/10 text-slate-400 cursor-not-allowed opacity-60' : 'bg-white/10 hover:bg-white/15 text-white' ?>"
                onclick="copyTestId()"
                <?= $btnDisabled ? 'disabled' : '' ?>
              >
                Copy
              </button>
            </div>

            <?php if ($btnDisabled): ?>
              <p class="text-xs text-slate-400 mt-2">ID belum diberikan admin.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Kontak Admin -->
        <div class="mt-6 rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-5">
          <h2 class="text-white font-semibold">Kontak Admin</h2>
          <p class="text-slate-300 text-sm mt-1">Jika ada kendala jadwal/ID atau akses tes, hubungi admin:</p>

          <div class="mt-3 flex flex-wrap gap-3">
            <a class="inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/15 text-white px-4 py-2 text-sm font-semibold ring-1 ring-white/10 transition"
               href="https://wa.me/6281320001323" target="_blank" rel="noopener noreferrer">
              WhatsApp 081320001323
            </a>

            <a class="inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/15 text-white px-4 py-2 text-sm font-semibold ring-1 ring-white/10 transition"
               href="https://wa.me/6289681928200" target="_blank" rel="noopener noreferrer">
              WhatsApp 089681928200
            </a>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Sidebar mobile (samain kayak form.php) -->
  <?= $this->element('dashboard/mobile_sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'tes']) ?>
</div>

<script>
function copyTestId() {
  const el = document.getElementById('testIdText');
  if (!el) return;
  const text = (el.textContent || '').trim();
  if (!text || text === '-') return;

  navigator.clipboard?.writeText(text).then(() => {
    alert('ID Tes berhasil disalin: ' + text);
  }).catch(() => {
    alert('Gagal menyalin. Silakan blok dan copy manual.');
  });
}
</script>
