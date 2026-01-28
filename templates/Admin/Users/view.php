<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\User $user */
$this->assign('title', 'Detail User');
$status = strtolower((string)($user->status ?? 'aktif'));
$isActive = ($status !== 'nonaktif');

?>

<div class="space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold">Detail User</h1>
      <p class="text-sm text-slate-600 mt-1">ID: <?= (int)$user->id ?></p>
    </div>

    <div class="flex gap-2">
      <a href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'index']) ?>"
         class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold">
        Kembali
      </a>

      <?= $this->Form->postLink(
        $isActive ? 'Nonaktifkan' : 'Aktifkan',
        ['prefix'=>'Admin','controller'=>'Users','action'=>'toggle', $user->id],
        [
          'class' => 'rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white',
          'confirm' => $isActive ? 'Nonaktifkan user ini?' : 'Aktifkan user ini?'
        ]
      ) ?>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-slate-200 bg-white/60 p-5">
      <div class="text-sm text-slate-500">Nama</div>
      <div class="text-lg font-bold mt-1"><?= h($user->nama_lengkap ?? '-') ?></div>

      <div class="mt-4 text-sm text-slate-500">Email</div>
      <div class="text-base font-semibold mt-1"><?= h($user->email ?? '-') ?></div>

      <div class="mt-4 text-sm text-slate-500">Status</div>
      <div class="mt-1">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' ?>">
          <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
        </span>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white/60 p-5">
      <div class="text-sm text-slate-500">Info Lain</div>
      <div class="mt-3 grid grid-cols-1 gap-3 text-sm">
        <div class="flex justify-between gap-4">
          <span class="text-slate-500">Created</span>
          <span class="font-semibold text-slate-800"><?= h($user->created ?? '-') ?></span>
        </div>
        <div class="flex justify-between gap-4">
          <span class="text-slate-500">Modified</span>
          <span class="font-semibold text-slate-800"><?= h($user->modified ?? '-') ?></span>
        </div>
      </div>

      <div class="mt-6">
        <?= $this->Form->postLink(
          'Hapus User',
          ['prefix'=>'Admin','controller'=>'Users','action'=>'delete', $user->id],
          [
            'class' => 'rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700',
            'confirm' => 'Hapus user ini?'
          ]
        ) ?>
      </div>
    </div>
  </div>
</div>
