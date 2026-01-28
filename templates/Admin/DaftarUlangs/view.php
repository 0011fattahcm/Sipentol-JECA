<?php
/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\EntityInterface $item */
/** @var array $missing */
/** @var bool $isComplete */

$this->assign('title', 'Detail Daftar Ulang');

$backQuery = $this->request->getQueryParams();

$makeUrl = function (?string $path) {
  $path = trim((string)$path);
  if ($path === '') return null;

  // kalau sudah URL
  if (preg_match('#^https?://#i', $path)) return $path;

  // path relatif dari webroot
  return $this->Url->build('/' . ltrim($path, '/'));
};


$formulir = $makeUrl($item->formulir_pendaftaran_pdf ?? null);
$perjanjian = $makeUrl($item->surat_perjanjian_pdf ?? null);
$ortu = $makeUrl($item->surat_persetujuan_orangtua_pdf ?? null);
$bukti = $makeUrl($item->bukti_pembayaran_img ?? null);
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Detail Daftar Ulang</h1>
    <p class="text-slate-600 mt-1 text-sm">
      User: <span class="font-semibold"><?= h($item->user->nama_lengkap ?? '-') ?></span>
      • <span class="font-semibold"><?= h($item->user->email ?? '-') ?></span>
    </p>
  </div>

  <a
    href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'index','?' => $backQuery]) ?>"
    class="px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
  >
    Kembali
  </a>
  
</div>

<div class="mt-5 glass-soft p-4 space-y-4">

  <?php if (!$isComplete): ?>
    <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4">
      <div class="font-extrabold text-amber-900">Berkas belum lengkap</div>
      <ul class="mt-2 text-sm text-amber-900 list-disc pl-5">
        <?php foreach ($missing as $m): ?>
          <li><?= h($m) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else: ?>
    <div class="rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4">
      <div class="font-extrabold text-emerald-900">Berkas lengkap</div>
      <div class="text-sm text-emerald-900 mt-1">Anda bisa verifikasi untuk mengaktifkan user.</div>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">File Upload</div>
      <div class="mt-4 flex flex-wrap gap-2">
        <?php foreach ([
          ['label'=>'Formulir (PDF)', 'url'=>$formulir],
          ['label'=>'Surat Perjanjian (PDF)', 'url'=>$perjanjian],
          ['label'=>'Persetujuan Ortu (PDF)', 'url'=>$ortu],
          ['label'=>'Bukti Pembayaran (IMG)', 'url'=>$bukti],
        ] as $f): ?>
          <?php if (!empty($f['url'])): ?>
            <a target="_blank" href="<?= h($f['url']) ?>"
              class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
              <?= h($f['label']) ?>
            </a>
          <?php else: ?>
            <span class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white/60 text-slate-400 font-semibold">
              <?= h($f['label']) ?>: -
            </span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="mt-4 text-xs text-slate-500">
        Last update: <?= h($item->modified ?? '-') ?>
      </div>
    </div>

    <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
      <div class="font-extrabold text-slate-900">Aksi Admin</div>
      <p class="text-xs text-slate-500 mt-1">
        Verify = berkas valid & user jadi AKTIF. Need Fix = user diminta perbaiki upload.
      </p>

      <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:justify-end">
  <?= $this->Form->create(null, [
    'url' => ['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'openAccess', $item->id, '?' => $backQuery],
    'type' => 'post'
  ]) ?>
    <button class="px-5 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
      Buka Akses Daftar Ulang
    </button>
  <?= $this->Form->end() ?>

  <?= $this->Form->create(null, [
    'url' => ['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'needFix', $item->id, '?' => $backQuery],
    'type' => 'post'
  ]) ?>
    <button class="px-5 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
      Need Fix
    </button>
  <?= $this->Form->end() ?>

  <?= $this->Form->create(null, [
    'url' => ['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'verify', $item->id, '?' => $backQuery],
    'type' => 'post'
  ]) ?>
    <button
      class="px-5 py-2 rounded-2xl bg-slate-900 text-white font-semibold <?= $isComplete ? '' : 'opacity-50 cursor-not-allowed' ?>"
      <?= $isComplete ? '' : 'disabled' ?>
    >
      Verify
    </button>
    
  <?= $this->Form->end() ?>
  
</div>

<div class="mt-3">
  <?= $this->Form->create(null, [
    'url' => ['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'needFix', $item->id, '?' => $backQuery],
    'type' => 'post'
  ]) ?>

  <label class="text-xs text-slate-500 font-semibold">Catatan untuk user (opsional)</label>
  <textarea
    name="admin_note"
    class="mt-1 w-full rounded-xl ring-1 ring-slate-200 px-3 py-2 text-sm"
    placeholder="Contoh: file KTP blur, mohon upload ulang yang jelas."
  ><?= h($item->admin_note ?? '') ?></textarea>

  <div class="mt-3 flex justify-end gap-2">
    <button class="px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
      Need Fix
    </button>
  </div>

  <?= $this->Form->end() ?>
</div>

    </div>
  </div>
</div>
