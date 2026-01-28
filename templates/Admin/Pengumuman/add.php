<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Pengumuman $pengumuman */
$this->assign('title', 'Tambah Pengumuman');
?>

<div class="flex items-center justify-between mb-5">
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">Tambah Pengumuman</h1>
    <p class="text-sm text-slate-600 mt-1">Buat pengumuman untuk semua user atau user tertentu.</p>
  </div>

  <a href="<?= $this->Url->build(['action'=>'index']) ?>"
     class="px-4 py-2 rounded-xl ring-1 ring-slate-200 bg-white/70 hover:bg-white text-sm font-semibold">
    Kembali
  </a>
</div>

<?= $this->Form->create($pengumuman, ['class' => 'space-y-6']) ?>

  <?= $this->element('Pengumuman/form', ['pengumuman' => $pengumuman]) ?>

  <div class="flex items-center justify-end gap-3 pt-2">
    <a href="<?= $this->Url->build(['action'=>'index']) ?>"
       class="px-4 py-2 rounded-xl ring-1 ring-slate-200 bg-white/70 hover:bg-white text-sm font-semibold">
      Batal
    </a>

    <button type="submit"
      class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800">
      Simpan
    </button>
  </div>

<?= $this->Form->end() ?>
