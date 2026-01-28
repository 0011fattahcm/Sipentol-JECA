<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\User $user */

$this->assign('title', 'Edit User');

$backQuery = $this->request->getQueryParams();
?>

<div class="space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold">Edit User</h1>
      <p class="text-sm text-slate-600 mt-1">ID: <?= (int)$user->id ?></p>
    </div>

    <div class="flex gap-2">
      <a
        href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'index', '?' => $backQuery]) ?>"
        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold"
      >
        Kembali
      </a>

      <a
        href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'view', $user->id]) ?>"
        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold"
      >
        Detail
      </a>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white/60 p-5">
    <?= $this->Form->create($user) ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
        <?= $this->Form->control('nama_lengkap', [
          'label' => false,
          'class' => 'mt-2 w-full rounded-2xl ring-1 ring-slate-200 bg-white px-4 py-2 outline-none',
          'required' => true,
        ]) ?>
      </div>

      <div>
        <label class="text-sm font-semibold text-slate-700">Email</label>
        <?= $this->Form->control('email', [
          'label' => false,
          'type' => 'email',
          'class' => 'mt-2 w-full rounded-2xl ring-1 ring-slate-200 bg-white px-4 py-2 outline-none',
          'required' => true,
        ]) ?>
      </div>

      <div>
        <label class="text-sm font-semibold text-slate-700">Status</label>
        <?= $this->Form->control('status', [
          'label' => false,
          'type' => 'select',
          'options' => [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
          ],
          'class' => 'mt-2 w-full rounded-2xl ring-1 ring-slate-200 bg-white px-4 py-2 outline-none',
        ]) ?>
        <div class="text-xs text-slate-500 mt-2">Status ini dipakai untuk gating akses user.</div>
      </div>
    </div>

    <div class="mt-6 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-end">
      <a
        href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'index', '?' => $backQuery]) ?>"
        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold"
      >
        Batal
      </a>

      <button
        class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white"
        type="submit"
      >
        Simpan Perubahan
      </button>
    </div>

    <?= $this->Form->end() ?>
  </div>
</div>
