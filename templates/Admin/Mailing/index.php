<?php
/** @var \App\View\AppView $this */
/** @var \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface $mailings */
/** @var string $q */
/** @var string $from */
/** @var string $to */

$this->assign('title', 'Mailing');

$q = (string)($q ?? $this->request->getQuery('q', ''));
$from = (string)($from ?? $this->request->getQuery('from', ''));
$to = (string)($to ?? $this->request->getQuery('to', ''));
?>

<div class="flex items-start justify-between gap-4 mb-5">
  <div>
    <h1 class="text-2xl font-extrabold tracking-tight">Mailing</h1>
    <p class="text-slate-500 mt-1">Kirim email ke user tertentu atau semua user. Semua riwayat akan tercatat.</p>
  </div>

  <a href="<?= $this->Url->build(['action'=>'add']) ?>"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold shadow hover:opacity-95">
    + Kirim Email
  </a>
</div>

<form method="get" class="glass-soft p-4 rounded-2xl mb-4">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <input name="q" value="<?= h($q) ?>" placeholder="Cari subject/body..."
      class="w-full h-11 px-4 rounded-xl ring-1 ring-slate-200 bg-white/70 outline-none"/>

    <input type="date" name="from" value="<?= h($from) ?>"
      class="w-full h-11 px-4 rounded-xl ring-1 ring-slate-200 bg-white/70 outline-none"/>

    <input type="date" name="to" value="<?= h($to) ?>"
      class="w-full h-11 px-4 rounded-xl ring-1 ring-slate-200 bg-white/70 outline-none"/>

    <button class="h-11 rounded-xl bg-slate-900 text-white font-semibold shadow">Search</button>
  </div>
</form>

<div class="ring-1 ring-slate-200 rounded-2xl overflow-hidden bg-white/70">
  <table class="w-full text-sm">
    <thead class="bg-slate-50">
      <tr class="text-left text-slate-600">
        <th class="px-4 py-3 w-16">ID</th>
        <th class="px-4 py-3">Subject</th>
        <th class="px-4 py-3 w-28">Target</th>
        <th class="px-4 py-3 w-40">Sukses</th>
        <th class="px-4 py-3 w-44">Terkirim</th>
        <th class="px-4 py-3 w-28 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($mailings as $m): ?>
        <tr class="border-t border-slate-200/70">
          <td class="px-4 py-3"><?= (int)$m->id ?></td>
          <td class="px-4 py-3">
            <div class="font-semibold text-slate-900"><?= h($m->subject) ?></div>
            <div class="text-slate-500 text-xs mt-1 line-clamp-1">
              <?= h(strip_tags((string)$m->body_html)) ?>
            </div>
          </td>
          <td class="px-4 py-3">
            <?php if ($m->target === 'semua'): ?>
              <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100">semua</span>
            <?php else: ?>
              <span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 ring-1 ring-slate-200">tertentu</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <span class="font-semibold text-emerald-700"><?= (int)$m->sent_success ?></span>
            <span class="text-slate-400">/ <?= (int)$m->sent_total ?></span>
            <?php if ((int)$m->sent_failed > 0): ?>
              <span class="ml-2 text-rose-600 text-xs">(gagal <?= (int)$m->sent_failed ?>)</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-slate-600">
            <?= h((string)$m->created_at ?: '-') ?>
          </td>
          <td class="px-4 py-3 text-right">
            <a class="px-3 py-2 rounded-xl ring-1 ring-slate-200 hover:bg-white font-semibold"
               href="<?= $this->Url->build(['action'=>'view', $m->id]) ?>">
              Detail
            </a>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php if (count($mailings) === 0): ?>
        <tr>
          <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada riwayat email.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="mt-4 flex items-center justify-between text-sm text-slate-600">
  <div><?= $this->Paginator->counter('Menampilkan {{start}}–{{end}} dari {{count}} data') ?></div>
  <div class="flex gap-2">
    <?= $this->Paginator->prev('Prev', ['class'=>'px-3 py-2 rounded-xl ring-1 ring-slate-200 hover:bg-white']) ?>
    <?= $this->Paginator->next('Next', ['class'=>'px-3 py-2 rounded-xl ring-1 ring-slate-200 hover:bg-white']) ?>
  </div>
</div>
