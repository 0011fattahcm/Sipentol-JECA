<?php
/** @var \App\View\AppView $this */

$this->assign('title', 'Dashboard Admin');

// Fallback biar aman kalau controller belum set semua variabel
$totalUsers         = $totalUsers ?? 0;
$totalOnlineTests   = $totalOnlineTests ?? 0;
$activePengumuman   = $activePengumuman ?? 0;

$pendingPendaftaran = $pendingPendaftaran ?? 0; // jumlah user yang sudah punya pendaftaran (atau status tertentu)
$pendingDaftarUlang = $pendingDaftarUlang ?? 0; // jumlah yang belum lengkap / belum diverifikasi
$tesOpenCount       = $tesOpenCount ?? 0;       // jumlah tes status open
$tesWaitingCount    = $tesWaitingCount ?? 0;    // status waiting

$latestPengumuman   = $latestPengumuman ?? [];
$latestActivity     = $latestActivity ?? [];    // array entity log (optional)
$latestUsers        = $latestUsers ?? [];       // array entity user (optional)

// helper
function badge($text, $type = 'gray') {
  $map = [
    'green' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
    'amber' => 'bg-amber-100 text-amber-800 ring-amber-200',
    'red'   => 'bg-rose-100 text-rose-800 ring-rose-200',
    'blue'  => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
    'gray'  => 'bg-slate-100 text-slate-700 ring-slate-200',
  ];
  $cls = $map[$type] ?? $map['gray'];
  return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 '.$cls.'">'.h($text).'</span>';
}
?>

<div class="space-y-6">

  <!-- Header + Quick Actions -->
  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Dashboard Admin</h1>
      <p class="text-slate-600 mt-1">Ringkasan sistem, status proses, dan aksi cepat untuk admin.</p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Pengumuman','action'=>'add']) ?>"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-900 text-white font-semibold hover:opacity-90 transition">
        Buat Pengumuman
      </a>

      <a href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'index']) ?>"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/70 ring-1 ring-slate-200 text-slate-800 font-semibold hover:bg-white transition">
        Kelola Tes Online
      </a>

      <a href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Settings','action'=>'onboarding']) ?>"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/70 ring-1 ring-slate-200 text-slate-800 font-semibold hover:bg-white transition">
        Link Onboarding
      </a>
    </div>
  </div>

  <!-- KPI cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="text-sm text-slate-600">Total User</div>
      <div class="text-3xl font-extrabold text-slate-900 mt-1"><?= (int)$totalUsers ?></div>
      <div class="text-xs text-slate-500 mt-2">Jumlah akun terdaftar di sistem.</div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="text-sm text-slate-600">Total Tes Online</div>
      <div class="text-3xl font-extrabold text-slate-900 mt-1"><?= (int)$totalOnlineTests ?></div>
      <div class="text-xs text-slate-500 mt-2">
        <?= $tesOpenCount > 0 ? 'Ada tes yang sedang dibuka.' : 'Tidak ada tes open saat ini.' ?>
      </div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="text-sm text-slate-600">Pengumuman Aktif</div>
      <div class="text-3xl font-extrabold text-slate-900 mt-1"><?= (int)$activePengumuman ?></div>
      <div class="text-xs text-slate-500 mt-2">Pengumuman yang tampil di dashboard user.</div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="text-sm text-slate-600">Antrian Proses</div>
      <div class="text-3xl font-extrabold text-slate-900 mt-1"><?= (int)$pendingDaftarUlang + (int)$pendingPendaftaran ?></div>
      <div class="text-xs text-slate-500 mt-2">Butuh review admin (pendaftaran/daftar ulang).</div>
    </div>
  </div>

   <div class="mt-6 rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
  <div class="flex items-center justify-between gap-3">
    <div>
      <div class="text-base font-extrabold text-slate-900">Grafik Pendaftar per Bulan</div>
      <div class="text-sm text-slate-600 mt-0.5">12 bulan terakhir</div>
    </div>
  </div>

  <div class="mt-4">
    <canvas id="pendaftarChart" height="110"></canvas>
  </div>
</div>


  <!-- Status Overview -->
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <!-- Proses -->
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between">
        <div class="font-extrabold text-slate-900">Status Proses</div>
        <?= badge('Monitoring', 'blue') ?>
      </div>

      <div class="mt-4 space-y-3">
        <div class="flex items-center justify-between">
          <div class="text-slate-700">Pendaftaran</div>
          <div class="flex items-center gap-2">
            <?= badge($pendingPendaftaran.' pending', $pendingPendaftaran > 0 ? 'amber' : 'gray') ?>
            <a class="text-sm font-semibold text-slate-900 hover:underline"
               href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Pendaftarans','action'=>'index']) ?>">
              Kelola
            </a>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-slate-700">Daftar Ulang</div>
          <div class="flex items-center gap-2">
            <?= badge($pendingDaftarUlang.' pending', $pendingDaftarUlang > 0 ? 'amber' : 'gray') ?>
            <a class="text-sm font-semibold text-slate-900 hover:underline"
               href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'index']) ?>">
              Kelola
            </a>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-slate-700">Onboarding</div>
          <div class="flex items-center gap-2">
            <?= badge('Link', 'gray') ?>
            <a class="text-sm font-semibold text-slate-900 hover:underline"
               href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Settings','action'=>'onboarding']) ?>">
              Atur
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Tes Online -->
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between">
        <div class="font-extrabold text-slate-900">Tes Online</div>
        <?= badge(($tesOpenCount > 0 ? 'Open' : 'Waiting'), $tesOpenCount > 0 ? 'green' : 'amber') ?>
      </div>

      <div class="mt-4 space-y-3 text-sm text-slate-700">
        <div class="flex items-center justify-between">
          <span>Open</span>
          <span class="font-bold text-slate-900"><?= (int)$tesOpenCount ?></span>
        </div>
        <div class="flex items-center justify-between">
          <span>Menunggu Jadwal/ID</span>
          <span class="font-bold text-slate-900"><?= (int)$tesWaitingCount ?></span>
        </div>
        <div class="pt-2">
          <a href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'index']) ?>"
             class="inline-flex w-full justify-center px-4 py-2 rounded-2xl bg-slate-900 text-white font-semibold hover:opacity-90 transition">
            Set Jadwal / Lokasi / ID
          </a>
        </div>
      </div>
    </div>

    <!-- User Overview -->
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between">
        <div class="font-extrabold text-slate-900">User Terbaru</div>
        <a class="text-sm font-semibold text-slate-900 hover:underline"
           href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'index']) ?>">
          Lihat Semua
        </a>
      </div>

      <div class="mt-4 space-y-3">
        <?php if (count($latestUsers) === 0): ?>
          <div class="text-sm text-slate-600">Belum ada data user terbaru.</div>
        <?php else: ?>
          <?php foreach ($latestUsers as $u): ?>
            <div class="flex items-center justify-between rounded-2xl bg-white/60 ring-1 ring-slate-200 px-4 py-3">
              <div class="min-w-0">
                <div class="font-bold text-slate-900 truncate"><?= h($u->nama_lengkap ?? $u->name ?? 'User') ?></div>
                <div class="text-xs text-slate-600 truncate"><?= h($u->email ?? '-') ?></div>
              </div>
              <a class="text-sm font-semibold text-slate-900 hover:underline"
                 href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'edit', $u->id]) ?>">
                Detail
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

 

  <!-- Pengumuman + Activity Log -->
  <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
    <!-- Pengumuman -->
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between">
        <div class="font-extrabold text-slate-900">Pengumuman Terbaru</div>
        <a class="text-sm font-semibold text-slate-900 hover:underline"
           href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Pengumuman','action'=>'index']) ?>">
          Kelola
        </a>
      </div>

      <div class="mt-4 space-y-3">
        <?php if (count($latestPengumuman) === 0): ?>
          <div class="text-sm text-slate-600">Belum ada pengumuman.</div>
        <?php else: ?>
          <?php foreach ($latestPengumuman as $p): ?>
            <div class="rounded-2xl bg-white/60 ring-1 ring-slate-200 p-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-bold text-slate-900 truncate"><?= h($p->judul) ?></div>
                  <div class="text-sm text-slate-700 mt-1">
                    <?= h(mb_substr((string)$p->isi, 0, 140)) ?><?= mb_strlen((string)$p->isi) > 140 ? '...' : '' ?>
                  </div>
                </div>
                <a class="text-sm font-semibold text-slate-900 hover:underline"
                   href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Pengumuman','action'=>'edit', $p->id]) ?>">
                  Edit
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Activity log -->
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between">
        <div class="font-extrabold text-slate-900">Aktivitas Terbaru</div>
        <a class="text-sm font-semibold text-slate-900 hover:underline"
           href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'ActivityLogs','action'=>'index']) ?>">
          Lihat Semua
        </a>
      </div>

      <div class="mt-4 space-y-3">
        <?php if (count($latestActivity) === 0): ?>
          <div class="text-sm text-slate-600">Belum ada aktivitas tercatat.</div>
        <?php else: ?>
         <?php foreach ($latestActivity as $log): ?>
  <div class="rounded-2xl bg-white/60 ring-1 ring-slate-200 px-4 py-3">
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="font-bold text-slate-900 truncate">
          <?= h($log->aktivitas ?? '-') ?>
        </div>

        <div class="text-xs text-slate-600 mt-0.5 truncate">
          <?= h($log->user->email ?? '-') ?>
          <?php if (!empty($log->created)): ?>
            • <?= h($log->created->setTimezone('Asia/Jakarta')->format('d/m/Y, H:i')) ?> WIB
          <?php endif; ?>
        </div>
      </div>

      <div class="shrink-0">
        <?= badge('Log', 'gray') ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>

        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php
$labels = json_encode($pendaftarLabels ?? [], JSON_UNESCAPED_UNICODE);
$counts = json_encode($pendaftarCounts ?? [], JSON_UNESCAPED_UNICODE);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
  const el = document.getElementById('pendaftarChart');
  if (!el) return;

  const labels = <?= $labels ?>;
  const data   = <?= $counts ?>;

  new Chart(el, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Pendaftar',
        data,
        tension: 0.35,
        fill: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
})();
</script>

