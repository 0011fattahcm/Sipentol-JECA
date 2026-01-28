<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mailing $mailing
 * @var \Cake\Collection\CollectionInterface $recipients
 */
$this->assign('title', 'Detail Email');

$sentAt = $mailing->sent_at ? $mailing->sent_at->format('Y-m-d H:i') : '-';
$targetLabel = ($mailing->target ?? '') === 'semua' ? 'Semua user' : 'User tertentu';

$successCount = (int)($mailing->success_count ?? 0);
$failCount    = (int)($mailing->fail_count ?? 0);
$totalCount   = (int)($mailing->total_count ?? ($successCount + $failCount));

$failed = [];
if (!empty($mailing->failed_json)) {
  $d = json_decode((string)$mailing->failed_json, true);
  if (is_array($d)) $failed = $d;
}
?>

<div class="max-w-6xl">
  <div class="flex items-start justify-between gap-4">
    <div>
      <div class="text-3xl font-extrabold text-slate-900">Detail Email</div>
      <div class="text-slate-600 mt-1">Riwayat pengiriman email dari admin.</div>
    </div>

    <a href="<?= $this->Url->build(['action' => 'index']) ?>"
       class="inline-flex items-center justify-center px-4 h-11 rounded-2xl bg-white/70 ring-1 ring-slate-200 hover:bg-white text-slate-700 font-semibold">
      Kembali
    </a>
  </div>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-5">
    <!-- left meta -->
    <div class="lg:col-span-5">
      <div class="p-5 rounded-3xl bg-white/60 ring-1 ring-slate-200 space-y-4">
        <div>
          <div class="text-xs font-extrabold text-slate-500">SUBJECT</div>
          <div class="text-lg font-extrabold text-slate-900 mt-1"><?= h($mailing->subject ?? '-') ?></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="p-4 rounded-2xl bg-white/70 ring-1 ring-slate-200">
            <div class="text-xs font-extrabold text-slate-500">TARGET</div>
            <div class="mt-1 font-extrabold text-slate-900"><?= h($targetLabel) ?></div>
          </div>
          <div class="p-4 rounded-2xl bg-white/70 ring-1 ring-slate-200">
            <div class="text-xs font-extrabold text-slate-500">SENT AT</div>
            <div class="mt-1 font-extrabold text-slate-900"><?= h($sentAt) ?></div>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div class="p-4 rounded-2xl bg-emerald-50 ring-1 ring-emerald-100">
            <div class="text-xs font-extrabold text-emerald-700">SUKSES</div>
            <div class="mt-1 text-2xl font-extrabold text-emerald-800"><?= $successCount ?></div>
          </div>
          <div class="p-4 rounded-2xl bg-rose-50 ring-1 ring-rose-100">
            <div class="text-xs font-extrabold text-rose-700">GAGAL</div>
            <div class="mt-1 text-2xl font-extrabold text-rose-800"><?= $failCount ?></div>
          </div>
          <div class="p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-200">
            <div class="text-xs font-extrabold text-slate-500">TOTAL</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900"><?= $totalCount ?></div>
          </div>
        </div>

        <div>
          <div class="text-sm font-extrabold text-slate-800 mb-2">Penerima</div>
          <div class="max-h-[320px] overflow-y-auto pr-2 space-y-2">
            <?php if (!empty($recipients)): ?>
              <?php foreach ($recipients as $u): ?>
                <div class="p-3 rounded-2xl bg-white/70 ring-1 ring-slate-200">
                  <div class="font-extrabold text-slate-900"><?= h($u->nama_lengkap ?: 'User') ?></div>
                  <div class="text-sm text-slate-500"><?= h($u->email) ?></div>
                  <div class="text-xs text-slate-400">ID: <?= (int)$u->id ?></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-slate-500 text-sm">Tidak ada data penerima.</div>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($failed)): ?>
          <div class="p-4 rounded-2xl bg-rose-50 ring-1 ring-rose-100">
            <div class="font-extrabold text-rose-800">Gagal Terkirim</div>
            <div class="mt-2 space-y-2 text-sm">
              <?php foreach ($failed as $f): ?>
                <div class="p-3 rounded-2xl bg-white ring-1 ring-rose-100">
                  <div class="font-extrabold text-rose-800"><?= h($f['email'] ?? '-') ?></div>
                  <div class="text-rose-700 text-xs mt-1"><?= h($f['error'] ?? '-') ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- right: html preview -->
    <div class="lg:col-span-7">
      <div class="p-5 rounded-3xl bg-white/60 ring-1 ring-slate-200">
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="text-lg font-extrabold text-slate-900">Preview HTML Email</div>
            <div class="text-sm text-slate-500">Konten yang tersimpan di database (body_html).</div>
          </div>
        </div>

        <div class="mt-4 rounded-2xl bg-white ring-1 ring-slate-200 p-5 overflow-auto prose max-w-none">
          <?= (string)($mailing->body_html ?? '') ?>
        </div>
      </div>
    </div>
  </div>
</div>
