<?php
/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\ResultSetInterface $logs */
/** @var string $q */
/** @var int $limit */

use Cake\I18n\FrozenTime;

$this->assign('title', 'Activity Log User');

$q = (string)($q ?? $this->request->getQuery('q', ''));
$limit = (int)($limit ?? $this->request->getQuery('limit', 20));
if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 20;

$this->Paginator->setTemplates([
  'number' => '<a class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/70 hover:bg-white font-semibold text-slate-700" href="{{url}}">{{text}}</a>',
  'current' => '<span class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl bg-slate-900 text-white font-extrabold">{{text}}</span>',
  'prevActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/70 hover:bg-white font-semibold" rel="prev" href="{{url}}">Prev</a>',
  'nextActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/70 hover:bg-white font-semibold" rel="next" href="{{url}}">Next</a>',
  'prevDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/40 text-slate-400 font-semibold">Prev</span>',
  'nextDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/40 text-slate-400 font-semibold">Next</span>',
]);

function formatWib($dt): string {
  if (!$dt) return '-';

  // Kalau $dt string datetime, ubah ke FrozenTime
  if (is_string($dt)) {
    try { $dt = new FrozenTime($dt); } catch (\Throwable $e) { return h($dt); }
  }

  try {
    return $dt->setTimezone('Asia/Jakarta')->format('d/m/y, H:i') . ' WIB';
  } catch (\Throwable $e) {
    return h((string)$dt);
  }
}
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Activity Log User</h1>
    <p class="text-slate-600 mt-1 text-sm">Riwayat aktivitas user (login, logout, submit form, dll).</p>
  </div>

  <form method="get" class="flex flex-col sm:flex-row gap-2">
    <input
      type="text"
      name="q"
      value="<?= h($q) ?>"
      placeholder="Cari aktivitas / nama / email..."
      class="h-11 w-full sm:w-72 px-4 rounded-xl ring-1 ring-slate-200 bg-white/70 outline-none"
    />

    <select
      name="limit"
      class="h-11 px-3 rounded-xl ring-1 ring-slate-200 bg-white/70 outline-none"
    >
      <?php foreach ([10,20,50,100] as $n): ?>
        <option value="<?= $n ?>" <?= $limit===$n ? 'selected' : '' ?>><?= $n ?>/hal</option>
      <?php endforeach; ?>
    </select>

    <button class="h-11 px-5 rounded-xl bg-slate-900 text-white font-semibold hover:opacity-90">
      Filter
    </button>
  </form>
</div>

<div class="mt-5 rounded-2xl bg-white/70 ring-1 ring-slate-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50/80">
        <tr class="text-left">
          <th class="px-4 py-3 font-extrabold text-slate-700">Waktu</th>
          <th class="px-4 py-3 font-extrabold text-slate-700">User ID</th>
          <th class="px-4 py-3 font-extrabold text-slate-700">Nama Lengkap</th>
          <th class="px-4 py-3 font-extrabold text-slate-700">Email</th>
          <th class="px-4 py-3 font-extrabold text-slate-700">Aktivitas</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($logs->count() === 0): ?>
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
              Tidak ada data.
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($logs as $log): ?>
          <tr class="border-t border-slate-200/70">
            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
              <?= h(formatWib($log->created ?? null)) ?>
            </td>

            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
              <?= h($log->user_id ?? '-') ?>
            </td>

            <td class="px-4 py-3 font-semibold text-slate-900">
              <?= h($log->user->nama_lengkap ?? '-') ?>
            </td>

            <td class="px-4 py-3 text-slate-700">
              <?= h($log->user->email ?? '-') ?>
            </td>

            <td class="px-4 py-3">
              <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-semibold">
                <?= h($log->aktivitas ?? '-') ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
  <div class="text-sm text-slate-600">
    <?= $this->Paginator->counter('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total') ?>
  </div>

  <div class="flex flex-wrap gap-2">
    <?= $this->Paginator->prev('Prev') ?>
    <?= $this->Paginator->numbers() ?>
    <?= $this->Paginator->next('Next') ?>
  </div>
</div>
