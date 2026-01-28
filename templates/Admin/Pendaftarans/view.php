<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Pendaftaran $pendaftaran */

$this->assign('title', 'Detail Pendaftaran');

$makeFileLink = function (?string $path) {
    $path = trim((string)$path);
    if ($path === '') return null;
    if (preg_match('#^https?://#i', $path)) return $path;
    return $this->Url->build('/' . ltrim($path, '/'));
};

$pasFoto = $makeFileLink($pendaftaran->pas_foto_path ?? null);
$ktp     = $makeFileLink($pendaftaran->ktp_pdf_path ?? null);
$ijazah  = $makeFileLink($pendaftaran->ijazah_pdf_path ?? null);
$trans   = $makeFileLink($pendaftaran->transkrip_pdf_path ?? null);

$backQuery = $this->request->getQueryParams();

/**
 * ===== Sumber Informasi Pendaftaran (kolom baru) =====
 * Kolom DB:
 * - info_sumber
 * - info_referral_code
 * - info_instansi_nama
 * - info_sumber_lain
 */
$src = (string)($pendaftaran->info_sumber ?? '');

$labelMap = [
  'website'        => 'Website',
  'facebook'       => 'Facebook',
  'instagram'      => 'Instagram',
  'tiktok'         => 'Tiktok',
  'jeca_group'     => 'JECA Group',
  'jeca_relations' => 'JECA Relations',
  'instansi'       => 'Instansi',
  'lain'           => 'Sumber lain',
];

$srcLabel = $labelMap[$src] ?? ($src !== '' ? $src : '-');

$extraLabel = '';
$extraValue = '';

if ($src === 'jeca_group' || $src === 'jeca_relations') {
  $extraLabel = 'Kode Referal';
  $extraValue = (string)($pendaftaran->info_referral_code ?? '');
} elseif ($src === 'instansi') {
  $extraLabel = 'Nama Instansi';
  $extraValue = (string)($pendaftaran->info_instansi_nama ?? '');
} elseif ($src === 'lain') {
  $extraLabel = 'Keterangan';
  $extraValue = (string)($pendaftaran->info_sumber_lain ?? '');
}
?>

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">Detail Pendaftaran</h1>
    <p class="text-slate-600 mt-1 text-sm">
      ID: <span class="font-semibold"><?= (int)$pendaftaran->id ?></span> •
      User ID: <span class="font-semibold"><?= (int)($pendaftaran->user_id ?? 0) ?></span>
    </p>
  </div>

  <div class="flex gap-2">
    <a
      href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Pendaftarans','action'=>'index', '?' => $backQuery]) ?>"
      class="px-4 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
    >
      Kembali
    </a>
  </div>
</div>

<div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
  <!-- LEFT: ringkasan + file -->
  <div class="glass-soft p-4">
    <div class="font-extrabold text-slate-900">Ringkasan</div>

    <div class="mt-3 space-y-2 text-sm text-slate-700">
      <div>
        <div class="text-xs text-slate-500">Nama Lengkap</div>
        <div class="font-semibold"><?= h($pendaftaran->nama_lengkap ?? '-') ?></div>
      </div>

      <div>
        <div class="text-xs text-slate-500">NIK</div>
        <div class="font-semibold"><?= h($pendaftaran->nik ?? '-') ?></div>
      </div>

      <div>
        <div class="text-xs text-slate-500">Jenis Kelamin</div>
        <div class="font-semibold"><?= h($pendaftaran->jenis_kelamin ?? '-') ?></div>
      </div>

      <div>
        <div class="text-xs text-slate-500">Tanggal Lahir</div>
        <div class="font-semibold">
          <?= h($pendaftaran->tanggal_lahir ?? '-') ?>
          (<?= h($pendaftaran->usia ?? '-') ?>)
        </div>
      </div>

      <div>
        <div class="text-xs text-slate-500">Kontak</div>
        <div class="font-semibold"><?= h($pendaftaran->email ?? '-') ?></div>
        <div class="font-semibold"><?= h($pendaftaran->whatsapp ?? '-') ?></div>
      </div>
    </div>

    <div class="mt-5">
      <div class="font-extrabold text-slate-900">File</div>
      <div class="mt-3 flex flex-wrap gap-2">
        <?php if ($pasFoto): ?><a target="_blank" href="<?= h($pasFoto) ?>" class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">Pas Foto</a><?php endif; ?>
        <?php if ($ktp): ?><a target="_blank" href="<?= h($ktp) ?>" class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">KTP</a><?php endif; ?>
        <?php if ($ijazah): ?><a target="_blank" href="<?= h($ijazah) ?>" class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">Ijazah</a><?php endif; ?>
        <?php if ($trans): ?><a target="_blank" href="<?= h($trans) ?>" class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold">Transkrip</a><?php endif; ?>
        <?php if (!$pasFoto && !$ktp && !$ijazah && !$trans): ?>
          <span class="text-slate-500 text-sm">Tidak ada file.</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- RIGHT: data lengkap -->
  <div class="lg:col-span-2 glass-soft p-4">
    <div class="font-extrabold text-slate-900">Data Lengkap</div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
      <!-- CARD: sumber informasi (selalu tampil di paling atas grid) -->
      <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4 sm:col-span-2">
        <div class="text-xs text-slate-500">Sumber Informasi Pendaftaran</div>

        <div class="mt-2 flex flex-wrap items-center gap-2">
          <?php if ($srcLabel !== '-'): ?>
            <span class="inline-flex items-center rounded-xl bg-slate-900 text-white px-3 py-1 text-xs font-semibold">
              <?= h($srcLabel) ?>
            </span>
          <?php else: ?>
            <span class="text-slate-500 text-sm">-</span>
          <?php endif; ?>
        </div>

        <?php if ($extraLabel !== ''): ?>
          <div class="mt-3">
            <div class="text-xs text-slate-500"><?= h($extraLabel) ?></div>
            <div class="mt-1 font-semibold text-slate-900 break-words">
              <?= trim($extraValue) !== '' ? h($extraValue) : '-' ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <?php
        $rows = [
          'Alamat Lengkap' => $pendaftaran->alamat_lengkap ?? '-',
          'Domisili Saat Ini' => $pendaftaran->domisili_saat_ini ?? '-',
          'Tinggi Badan' => ($pendaftaran->tinggi_badan ?? '-') . ' cm',
          'Berat Badan' => ($pendaftaran->berat_badan ?? '-') . ' kg',
          'Pendidikan (Jenjang)' => $pendaftaran->pendidikan_jenjang ?? '-',
          'Pendidikan (Instansi)' => $pendaftaran->pendidikan_instansi ?? '-',
          'Pendidikan (Jurusan)' => $pendaftaran->pendidikan_jurusan ?? '-',
          'Pendidikan (Tahun Kelulusan)' => $pendaftaran->pendidikan_tahun_kelulusan ?? '-',
          'Ayah (Nama)' => $pendaftaran->ayah_nama ?? '-',
          'Ayah (Usia)' => $pendaftaran->ayah_usia ?? '-',
          'Ayah (Pekerjaan)' => $pendaftaran->ayah_pekerjaan ?? '-',
          'Ibu (Nama)' => $pendaftaran->ibu_nama ?? '-',
          'Ibu (Usia)' => $pendaftaran->ibu_usia ?? '-',
          'Ibu (Pekerjaan)' => $pendaftaran->ibu_pekerjaan ?? '-',
          'Instagram' => $pendaftaran->instagram_url ?? '-',
          'Facebook' => $pendaftaran->facebook_url ?? '-',
          'Created' => $pendaftaran->created ?? '-',
          'Modified' => $pendaftaran->modified ?? '-',
        ];
      ?>

      <?php foreach ($rows as $label => $value): ?>
        <div class="rounded-2xl bg-white/70 ring-1 ring-slate-200 p-4">
          <div class="text-xs text-slate-500"><?= h($label) ?></div>
          <div class="mt-1 font-semibold text-slate-900 break-words"><?= h((string)$value) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
