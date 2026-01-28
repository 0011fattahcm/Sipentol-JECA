<?php
/** @var \App\View\AppView $this */
/** @var \Cake\ORM\ResultSet $pengumuman */
/** @var string $q */
/** @var string $target */
/** @var string $isActive */

$this->assign('title', 'Pengumuman');
?>

<div class="p-6">
  <div class="flex items-center justify-between mb-5">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Pengumuman</h1>
      <p class="text-slate-500 mt-1">Broadcast ke semua user atau user tertentu.</p>
    </div>
    <a href="<?= $this->Url->build(['action' => 'add']) ?>"
       class="inline-flex items-center rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800">
      + Tambah
    </a>
  </div>

  <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-4 mb-5">
    <form class="grid grid-cols-1 md:grid-cols-12 gap-3" method="get">
      <div class="md:col-span-6">
        <input name="q" value="<?= h($q ?? '') ?>" placeholder="Cari judul/isi..."
          class="w-full h-10 rounded-xl border border-slate-200 px-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
      </div>
      <div class="md:col-span-3">
        <select name="target"
          class="w-full h-10 rounded-xl border border-slate-200 px-3 text-sm focus:outline-none">
          <option value="">Semua target</option>
          <option value="semua" <?= ($target ?? '')==='semua'?'selected':'' ?>>semua</option>
          <option value="tertentu" <?= ($target ?? '')==='tertentu'?'selected':'' ?>>tertentu</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <select name="is_active"
          class="w-full h-10 rounded-xl border border-slate-200 px-3 text-sm focus:outline-none">
          <option value="">Semua status</option>
          <option value="1" <?= (string)($isActive ?? '')==='1'?'selected':'' ?>>aktif</option>
          <option value="0" <?= (string)($isActive ?? '')==='0'?'selected':'' ?>>nonaktif</option>
        </select>
      </div>
      <div class="md:col-span-1">
        <button class="w-full h-10 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
          Search
        </button>
      </div>
    </form>
  </div>

  <div class="rounded-2xl bg-white ring-1 ring-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-4 py-3">ID</th>
          <th class="text-left px-4 py-3">Judul</th>
          <th class="text-left px-4 py-3">Target</th>
          <th class="text-left px-4 py-3">Aktif</th>
          <th class="text-right px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($pengumuman as $p): ?>
          <tr class="text-slate-800">
            <td class="px-4 py-3"><?= (int)$p->id ?></td>
            <td class="px-4 py-3">
              <div class="font-semibold"><?= h($p->judul) ?></div>
              <div class="text-slate-500 line-clamp-1"><?= h($p->isi) ?></div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                <?= $p->target==='semua' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : 'bg-amber-50 text-amber-800 ring-1 ring-amber-200' ?>">
                <?= h($p->target) ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                <?= (int)$p->is_active===1 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' ?>">
                <?= (int)$p->is_active===1 ? 'aktif' : 'nonaktif' ?>
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <a class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold ring-1 ring-slate-200 hover:bg-slate-50"
                 href="<?= $this->Url->build(['action' => 'edit', $p->id]) ?>">
                Edit
              </a>
              <?= $this->Form->postLink(
                'Hapus',
                ['action' => 'delete', $p->id],
                [
                  'confirm' => 'Hapus pengumuman ini?',
                  'class' => 'ml-2 inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold bg-rose-600 text-white hover:bg-rose-500'
                ]
              ) ?>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (count($pengumuman) === 0): ?>
          <tr>
            <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada data.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
      <div class="text-xs text-slate-500">
        <?= $this->Paginator->counter('Menampilkan {{start}}–{{end}} dari {{count}} data') ?>
      </div>
      <div class="flex items-center gap-2">
        <?= $this->Paginator->prev('Prev', ['class' => 'px-3 py-2 rounded-xl ring-1 ring-slate-200 text-sm hover:bg-slate-50']) ?>
        <?= $this->Paginator->next('Next', ['class' => 'px-3 py-2 rounded-xl ring-1 ring-slate-200 text-sm hover:bg-slate-50']) ?>
      </div>
    </div>
  </div>
</div>
