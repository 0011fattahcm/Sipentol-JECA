<?php
/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\ResultSetInterface|\Cake\ORM\ResultSet $pendaftarans */
/** @var string $q */
/** @var int $limit */

$this->assign('title', 'Pendaftaran');

$q = (string)($q ?? $this->request->getQuery('q', ''));
$limit = (int)$this->request->getQuery('limit', 10);
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

$makeFileLink = function (?string $path) {
    $path = trim((string)$path);
    if ($path === '') {
        return null;
    }

    // Kalau sudah full URL, balikin apa adanya
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    // Kalau simpan path relatif (mis: uploads/ktp/a.pdf), pastikan jadi URL dari webroot
    return $this->Url->build('/' . ltrim($path, '/'));
};
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Pendaftaran</h1>
    <p class="text-slate-600 mt-1 text-sm">Data yang diinput oleh user (pendaftar). File upload bisa dibuka dari sini.</p>
  </div>

  <form class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2" method="get">
    <input
      name="q"
      value="<?= h($q) ?>"
      placeholder="Cari nama / NIK / email / WA..."
      class="w-full sm:w-80 max-w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
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

    <button class="px-4 py-2 rounded-2xl bg-slate-900 text-white font-semibold">
      Search
    </button>
  </form>
</div>

<div class="mt-5 glass-soft p-4">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-500">
          <th class="py-3 px-3">ID</th>
          <th class="py-3 px-3">Nama</th>
          <th class="py-3 px-3">Kontak</th>
          <th class="py-3 px-3">Data</th>
          <th class="py-3 px-3">File</th>
          <th class="py-3 px-3">Waktu</th>
          <th class="py-3 px-3">Aksi</th>

        </tr>
      </thead>

<tbody>
  <?php if ((int)$pendaftarans->count() === 0): ?>
    <tr class="border-t border-slate-200/70">
      <td colspan="7" class="py-6 px-3 text-slate-600">Tidak ada data pendaftaran.</td>
    </tr>
  <?php endif; ?>

  <?php foreach ($pendaftarans as $p): ?>
    <?php
      $pasFoto = $makeFileLink($p->pas_foto_path ?? null);
      $ktp     = $makeFileLink($p->ktp_pdf_path ?? null);
      $ijazah  = $makeFileLink($p->ijazah_pdf_path ?? null);
      $trans   = $makeFileLink($p->transkrip_pdf_path ?? null);
    ?>
    <tr class="border-t border-slate-200/70 align-top">
      <td class="py-3 px-3 font-semibold"><?= (int)$p->id ?></td>

      <td class="py-3 px-3">
        <div class="font-semibold text-slate-900"><?= h($p->nama_lengkap ?? '-') ?></div>
        <div class="text-xs text-slate-500">User ID: <?= h($p->user_id ?? '-') ?></div>
      </td>

      <td class="py-3 px-3 text-slate-700">
        <div class="text-sm"><?= h($p->email ?? '-') ?></div>
        <div class="text-sm"><?= h($p->whatsapp ?? '-') ?></div>
        <?php if (!empty($p->instagram_url)): ?>
          <div class="text-xs text-slate-500 break-words"><?= h($p->instagram_url) ?></div>
        <?php endif; ?>
        <?php if (!empty($p->facebook_url)): ?>
          <div class="text-xs text-slate-500 break-words"><?= h($p->facebook_url) ?></div>
        <?php endif; ?>
      </td>

      <td class="py-3 px-3 text-slate-700">
        <div><span class="text-xs text-slate-500">NIK:</span> <span class="font-semibold"><?= h($p->nik ?? '-') ?></span></div>
        <div><span class="text-xs text-slate-500">JK:</span> <?= h($p->jenis_kelamin ?? '-') ?></div>
        <div><span class="text-xs text-slate-500">TTL:</span> <?= h($p->tanggal_lahir ?? '-') ?> (<?= h($p->usia ?? '-') ?>)</div>

        <div class="mt-2">
          <div class="text-xs text-slate-500">Pendidikan</div>
          <div class="font-semibold"><?= h($p->pendidikan_jenjang ?? '-') ?></div>
          <div><?= h($p->pendidikan_instansi ?? '-') ?></div>
          <div class="text-xs text-slate-500">
            <?= h($p->pendidikan_jurusan ?? '-') ?> • <?= h($p->pendidikan_tahun_kelulusan ?? '-') ?>
          </div>
        </div>
      </td>

      <td class="py-3 px-3">
        <div class="flex flex-wrap gap-2">
          <?php if ($pasFoto): ?>
            <a target="_blank" href="<?= h($pasFoto) ?>"
               class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
              Pas Foto
            </a>
          <?php endif; ?>

          <?php if ($ktp): ?>
            <a target="_blank" href="<?= h($ktp) ?>"
               class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
              KTP
            </a>
          <?php endif; ?>

          <?php if ($ijazah): ?>
            <a target="_blank" href="<?= h($ijazah) ?>"
               class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
              Ijazah
            </a>
          <?php endif; ?>

          <?php if ($trans): ?>
            <a target="_blank" href="<?= h($trans) ?>"
               class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">
              Transkrip
            </a>
          <?php endif; ?>

          <?php if (!$pasFoto && !$ktp && !$ijazah && !$trans): ?>
            <span class="text-slate-500 text-sm">-</span>
          <?php endif; ?>
        </div>
      </td>

      <td class="py-3 px-3 text-slate-700">
        <div class="font-semibold"><?= h($p->created ?? '-') ?></div>
        <div class="text-xs text-slate-500"><?= h($p->modified ?? '-') ?></div>
      </td>

      <!-- INI KOLOM AKSI: tombol Detail harus di dalam TR -->
      <td class="py-3 px-3">
        <div class="flex justify-end">
          <a
            href="<?= $this->Url->build([
              'prefix' => 'Admin',
              'controller' => 'Pendaftarans',
              'action' => 'view',
              $p->id,
              '?' => $this->request->getQueryParams()
            ]) ?>"
            class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
          >
            Detail
          </a>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
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
