<?php
/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\ResultSetInterface|\Cake\ORM\ResultSet $items */
/** @var string $q */
/** @var int $limit */

$this->assign('title', 'Tes Online');

$q = (string)($q ?? $this->request->getQuery('q', ''));
$limit = (int)($limit ?? $this->request->getQuery('limit', 10));
if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 10;

$this->Paginator->setTemplates([
  'number' => '<a class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" href="{{url}}">{{text}}</a>',
  'current' => '<span class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl bg-slate-900 text-white font-semibold">{{text}}</span>',
  'ellipsis' => '<span class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center text-slate-400">…</span>',
  'prevActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" rel="prev" href="{{url}}">Prev</a>',
  'prevDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/60 text-slate-400 font-semibold cursor-not-allowed">Prev</span>',
  'nextActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" rel="next" href="{{url}}">Next</a>',
  'nextDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/60 text-slate-400 font-semibold cursor-not-allowed">Next</span>',
]);

$badge = function (string $status): array {
  $status = $status ?: 'waiting';
  if ($status === 'open')   return ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'Open'];
  if ($status === 'closed') return ['bg-slate-100 text-slate-700 ring-slate-200', 'Closed'];
  return ['bg-amber-50 text-amber-700 ring-amber-200', 'Waiting'];
};

$fmt = function ($dt): string {
  if (empty($dt)) return '-';
  try {
    return is_object($dt) ? $dt->format('Y-m-d H:i') : (string)$dt;
  } catch (\Throwable $e) {
    return (string)$dt;
  }
};
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Tes Online</h1>
    <p class="text-slate-600 mt-1 text-sm">
      Set <b>status</b>, <b>jadwal</b>, dan <b>ID Tes</b> untuk user. User akan melihatnya di halaman Tes Online.
    </p>
  </div>

  <form class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2" method="get">
    <input
      name="q"
      value="<?= h($q) ?>"
      placeholder="Cari nama / NIK / email / WA / ID tes..."
      class="w-full sm:w-96 max-w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
    />

    <select
      name="limit"
      class="px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
      onchange="this.form.submit()"
      title="Jumlah data per halaman"
    >
      <?php foreach ([10,20,50,100] as $n): ?>
        <option value="<?= $n ?>" <?= $limit === $n ? 'selected' : '' ?>><?= $n ?>/page</option>
      <?php endforeach; ?>
    </select>

    <button class="px-4 py-2 rounded-2xl bg-slate-900 text-white font-semibold">Search</button>
  </form>
</div>

<div class="mt-5 glass-soft p-4">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-3 px-3">ID</th>
          <th class="py-3 px-3">User</th>
          <th class="py-3 px-3">NIK / WA</th>
          <th class="py-3 px-3">Jadwal</th>
          <th class="py-3 px-3">ID Tes</th>
          <th class="py-3 px-3">Status</th>
          <th class="py-3 px-3 text-right">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($items as $it): ?>
          <?php
            // hasOne('Pendaftarans') => property biasanya "pendaftaran"
            $p = $it->user->pendaftaran ?? ($it->user->pendaftarans ?? null);
            $nik = $p->nik ?? '-';
            $wa  = $p->whatsapp ?? '-';

            [$cls, $label] = $badge((string)($it->status ?? 'waiting'));

            $start = $fmt($it->schedule_start ?? null);
            $end   = $fmt($it->schedule_end ?? null);
            $idTes = (string)($it->test_access_id ?? '');
          ?>

          <tr class="border-t border-slate-200/70 align-top">
            <td class="py-3 px-3 font-semibold"><?= (int)$it->id ?></td>

            <td class="py-3 px-3">
              <div class="font-semibold text-slate-900"><?= h($it->user->nama_lengkap ?? '-') ?></div>
              <div class="text-xs text-slate-500"><?= h($it->user->email ?? '-') ?></div>
              <div class="text-xs text-slate-500 mt-1">User ID: <?= h($it->user_id ?? '-') ?></div>
            </td>

            <td class="py-3 px-3 text-slate-700">
              <div><span class="text-xs text-slate-500">NIK:</span> <span class="font-semibold"><?= h($nik) ?></span></div>
              <div class="mt-1"><span class="text-xs text-slate-500">WA:</span> <span class="font-semibold"><?= h($wa) ?></span></div>
            </td>

            <td class="py-3 px-3 text-slate-700">
              <div class="text-xs text-slate-500">Start</div>
              <div class="font-semibold"><?= h($start) ?></div>
              <div class="mt-2 text-xs text-slate-500">End</div>
              <div class="font-semibold"><?= h($end) ?></div>
            </td>

            <td class="py-3 px-3">
              <?php if ($idTes !== ''): ?>
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white font-semibold">
                    <?= h($idTes) ?>
                  </span>
                  <button
                    type="button"
                    class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
                    data-copy="<?= h($idTes) ?>"
                    onclick="copyIdTes(this)"
                    title="Copy ID Tes"
                  >
                    Copy
                  </button>
                </div>
              <?php else: ?>
                <span class="text-slate-500">-</span>
              <?php endif; ?>
            </td>

            <td class="py-3 px-3">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ring-1 <?= h($cls) ?>">
                <?= h($label) ?>
              </span>
            </td>

            <td class="py-3 px-3">
              <div class="flex justify-end gap-2">
                <a
                  href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'edit', $it->id, '?' => $this->request->getQueryParams()]) ?>"
                  class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
                >
                  Edit
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if ((int)$items->count() === 0): ?>
          <tr class="border-t border-slate-200/70">
            <td colspan="7" class="py-6 px-3 text-slate-600">Tidak ada data tes online.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div class="text-sm text-slate-600">
      <?= $this->Paginator->counter('Menampilkan {{start}}–{{end}} dari {{count}} data') ?>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <?= $this->Paginator->prev('Prev', ['url' => ['?' => $this->request->getQueryParams()]]) ?>
      <div class="flex items-center gap-2">
        <?= $this->Paginator->numbers(['modulus' => 5, 'url' => ['?' => $this->request->getQueryParams()]]) ?>
      </div>
      <?= $this->Paginator->next('Next', ['url' => ['?' => $this->request->getQueryParams()]]) ?>
    </div>
  </div>
</div>

<script>
  async function copyIdTes(btn) {
    const val = btn?.dataset?.copy || '';
    if (!val) return;

    try {
      await navigator.clipboard.writeText(val);
      const prev = btn.textContent;
      btn.textContent = 'Copied';
      setTimeout(() => (btn.textContent = prev), 900);
    } catch (e) {
      const ta = document.createElement('textarea');
      ta.value = val;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);

      const prev = btn.textContent;
      btn.textContent = 'Copied';
      setTimeout(() => (btn.textContent = prev), 900);
    }
  }
</script>
