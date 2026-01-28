<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\OnlineTest $item */

$this->assign('title', 'Edit Tes Online');

$backQuery = $this->request->getQueryParams();

$vStart = '';
$vEnd   = '';
try {
  if (!empty($item->schedule_start)) $vStart = $item->schedule_start->format('Y-m-d\TH:i');
  if (!empty($item->schedule_end))   $vEnd   = $item->schedule_end->format('Y-m-d\TH:i');
} catch (\Throwable $e) {}

$statuses = [
  'waiting' => 'waiting (belum dibuka)',
  'open'    => 'open (dibuka)',
  'closed'  => 'closed (ditutup)',
];

$locations = [
  'online'   => 'online',
  'lpk_jeca' => 'lpk_jeca',
  'onsite'   => 'onsite',
  'custom'   => 'custom',
];
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Edit Tes Online</h1>
    <p class="text-slate-600 mt-1 text-sm">
      User: <span class="font-semibold"><?= h($item->user->nama_lengkap ?? '-') ?></span>
      • <span class="font-semibold"><?= h($item->user->email ?? '-') ?></span>
    </p>
  </div>

  <a
    href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'index', '?' => $backQuery]) ?>"
    class="px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
  >
    Kembali
  </a>
</div>

<div class="mt-5 glass-soft p-4">
  <?= $this->Form->create($item) ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">Jadwal</div>
      <p class="text-xs text-slate-500 mt-1">Atur waktu tes. User akan melihat jadwal ini di halaman Tes Online.</p>

      <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-semibold text-slate-700">Mulai</label>
          <input
            type="datetime-local"
            name="schedule_start"
            value="<?= h($vStart) ?>"
            class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
          />
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700">Selesai</label>
          <input
            type="datetime-local"
            name="schedule_end"
            value="<?= h($vEnd) ?>"
            class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
          />
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">Akses Tes</div>
      <p class="text-xs text-slate-500 mt-1">Set URL tes dan ID tes yang diberikan admin ke user.</p>

      <div class="mt-4">
        <label class="text-sm font-semibold text-slate-700">Test URL</label>
        <input
          name="test_url"
          value="<?= h($item->test_url ?: 'https://test.jecaid.com') ?>"
          class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
        />
      </div>

      <div class="mt-3">
        <label class="text-sm font-semibold text-slate-700">ID Tes</label>
        <input
          name="test_access_id"
          value="<?= h($item->test_access_id ?? '') ?>"
          placeholder="Contoh: JECA-0123"
          class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
        />
        <p class="text-xs text-slate-500 mt-1">Wajib diisi jika status = open.</p>
      </div>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">Status</div>
      <p class="text-xs text-slate-500 mt-1">Status menentukan apakah user bisa melihat tombol akses & ID.</p>

      <div class="mt-4">
        <label class="text-sm font-semibold text-slate-700">Status Tes</label>
        <select
          name="status"
          class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
        >
          <?php foreach ($statuses as $val => $label): ?>
            <option value="<?= h($val) ?>" <?= ($item->status ?? 'waiting') === $val ? 'selected' : '' ?>>
              <?= h($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">Lokasi</div>
      <p class="text-xs text-slate-500 mt-1">Untuk tampilan informasi lokasi di halaman user.</p>

      <div class="mt-4">
        <label class="text-sm font-semibold text-slate-700">Tipe Lokasi</label>
        <select
          name="test_location_type"
          class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
          onchange="toggleCustomLocation(this.value)"
        >
          <?php foreach ($locations as $val => $label): ?>
            <option value="<?= h($val) ?>" <?= ($item->test_location_type ?? 'online') === $val ? 'selected' : '' ?>>
              <?= h($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mt-3" id="customLocationWrap">
        <label class="text-sm font-semibold text-slate-700">Detail Lokasi (jika custom)</label>
        <input
          name="test_location_detail"
          value="<?= h($item->test_location_detail ?? '') ?>"
          placeholder="Contoh: Zoom / Google Meet / Alamat luar"
          class="mt-1 w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
        />
      </div>
    </div>
  </div>

  <div class="mt-5 flex flex-col sm:flex-row gap-2 sm:justify-end">
    <button class="px-5 py-2 rounded-2xl bg-slate-900 text-white font-semibold">Simpan</button>
    <a
      href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'index', '?' => $backQuery]) ?>"
      class="px-5 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold text-center"
    >
      Batal
    </a>
  </div>

  <?= $this->Form->end() ?>
</div>

<script>
  function toggleCustomLocation(val) {
    const wrap = document.getElementById('customLocationWrap');
    if (!wrap) return;
    wrap.style.display = (val === 'custom') ? 'block' : 'none';
  }
  toggleCustomLocation('<?= h($item->test_location_type ?? 'online') ?>');
</script>
