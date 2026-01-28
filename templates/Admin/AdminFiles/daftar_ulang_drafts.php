<?php
/** @var \App\View\AppView $this */
/** @var array $keys */
/** @var array $existing */

$this->assign('title', 'Draft Daftar Ulang');
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Draft Daftar Ulang</h1>
    <p class="text-slate-600 mt-1 text-sm">Upload draft PDF yang akan didownload oleh user pada halaman Daftar Ulang.</p>
  </div>
</div>

<div class="mt-5 glass-soft p-4">
  <?= $this->Form->create(null, ['type' => 'file']) ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <?php foreach ($keys as $key => $label): ?>
      <?php $row = $existing[$key] ?? null; ?>
      <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
        <div class="font-extrabold text-slate-900"><?= h($label) ?></div>
        <div class="text-xs text-slate-500 mt-1">
          Format: PDF • Max 10MB
        </div>

        <div class="mt-4">
          <?= $this->Form->control($key, [
            'type' => 'file',
            'label' => false,
            'required' => false,
            'class' => 'block w-full text-sm'
          ]) ?>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
          <div class="text-xs text-slate-500">
            <?php if ($row && !empty($row->file_path)): ?>
              Tersimpan: <span class="font-semibold"><?= h($row->file_name ?? '-') ?></span>
            <?php else: ?>
              Belum ada file.
            <?php endif; ?>
          </div>

          <?php if ($row && !empty($row->file_path)): ?>
            <a class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
               href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'AdminFiles','action'=>'download', $key]) ?>">
              Download
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-5 flex justify-end">
    <button class="px-5 py-2 rounded-2xl bg-slate-900 text-white font-semibold">
      Simpan Draft
    </button>
  </div>

  <?= $this->Form->end() ?>
</div>
