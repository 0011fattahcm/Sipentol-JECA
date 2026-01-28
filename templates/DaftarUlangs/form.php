<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\DaftarUlang $daftarUlang */

$this->assign('title', 'Daftar Ulang');

// safety: kalau controller lupa set identity, coba ambil dari request
$identity = $identity ?? $this->request->getAttribute('identity');

// dari controller
$canDaftarUlang = $canDaftarUlang ?? false;

// status review dari controller (kalau belum diset, ambil dari entity)
$reviewStatus = $reviewStatus ?? (string)($daftarUlang->status ?? 'draft');

// lock kalau sudah verified
$isLocked = ($reviewStatus === 'verified');

// disable submit jika belum dibuka ATAU sudah verified
$disabled = (!$canDaftarUlang) || $isLocked;

$nama   = $identity ? (string)$identity->get('nama_lengkap') : 'Peserta';
$email  = $identity ? (string)$identity->get('email') : '';
$status = $identity ? (string)$identity->get('status') : 'pendaftaran';

// =====================
// FIX: status badge sidebar harus konsisten dengan status user (bukan hardcode)
// =====================
$statusKey = strtolower(trim((string)$status));

$statusLabelMap = [
  'pendaftaran'     => 'Pendaftaran',
  'menunggu_tes'    => 'Menunggu Tes',
  'tes'             => 'Tes Online',
  'menunggu_hasil'  => 'Menunggu Hasil',
  'daftar_ulang'    => 'Daftar Ulang',
  'onboarding'      => 'Onboarding',
];

$badgeMap = [
  'pendaftaran'     => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
  'menunggu_tes'    => 'bg-amber-50 text-amber-700 ring-amber-200',
  'tes'             => 'bg-sky-50 text-sky-700 ring-sky-200',
  'menunggu_hasil'  => 'bg-slate-50 text-slate-700 ring-slate-200',
  'daftar_ulang'    => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
  'onboarding'      => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
];

$statusText = $statusLabelMap[$statusKey] ?? 'Pendaftaran';
$badgeClass = $badgeMap[$statusKey] ?? 'bg-indigo-50 text-indigo-700 ring-indigo-200';

$initials = 'U';
if (!empty($nama)) {
    $parts = preg_split('/\s+/', trim($nama));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);
    $initials = mb_strtoupper($first . ($last ?: ''));
}

$nav = [
    ['label' => 'Dashboard',        'href' => $this->Url->build('/dashboard'),    'active' => false, 'enabled' => true],
    ['label' => 'Form Pendaftaran', 'href' => $this->Url->build('/pendaftaran'),  'active' => false, 'enabled' => true],
    ['label' => 'Tes Online',       'href' => $this->Url->build('/tes-online'),   'active' => false, 'enabled' => true],
    ['label' => 'Daftar Ulang',     'href' => $this->Url->build('/daftar-ulang'), 'active' => true,  'enabled' => true],
    ['label' => 'Onboarding',       'href' => '#', 'active' => false, 'enabled' => false],
];

$fileInput =
    'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm ' .
    'file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 ' .
    'hover:file:bg-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 outline-none';

$label = 'text-sm font-semibold text-slate-800';
$help  = 'text-xs text-slate-500 mt-1';

function fileLink(?string $path): ?string {
    if (!$path) return null;
    return $path; // diasumsikan sudah berupa "/uploads/...."
}

$formulirLink   = fileLink($daftarUlang->formulir_pendaftaran_pdf ?? null);
$buktiLink      = fileLink($daftarUlang->bukti_pembayaran_img ?? null);
$perjanjianLink = fileLink($daftarUlang->surat_perjanjian_pdf ?? null);
$ortuLink       = fileLink($daftarUlang->surat_persetujuan_orangtua_pdf ?? null);

$card = 'rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6';
$btnDraft = 'inline-flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 px-3 py-2 text-xs font-semibold transition';
$btnView  = 'inline-flex items-center justify-center rounded-lg bg-white hover:bg-slate-50 text-slate-700 px-3 py-2 text-xs font-semibold ring-1 ring-slate-200 transition';

$btnPrimaryEnabled = 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/20';
$btnPrimaryDisabled = 'bg-white/10 text-slate-300 cursor-not-allowed ring-1 ring-white/10';
?>

<div class="min-h-screen bg-slate-950 text-white relative overflow-hidden">
    <!-- Background blobs (samain kayak dashboard/form) -->
    <div class="absolute -top-40 -left-40 w-[520px] h-[520px] bg-indigo-600/25 blur-3xl rounded-full"></div>
    <div class="absolute top-32 -right-44 w-[560px] h-[560px] bg-fuchsia-600/20 blur-3xl rounded-full"></div>
    <div class="absolute bottom-[-220px] left-1/3 w-[680px] h-[680px] bg-cyan-500/10 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Mobile Topbar -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <div>
                <div class="text-white text-xl font-semibold">SiPentol</div>
                <div class="text-slate-300 text-sm">Daftar Ulang</div>
            </div>
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-2 ring-1 ring-white/10"
                onclick="document.getElementById('mobileSidebar').classList.remove('hidden')"
            >
                Menu
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Sidebar desktop (ikut pola halaman lain) -->
            <?= $this->element('dashboard/sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'daftarulang']) ?>

            <!-- MAIN -->
            <section class="lg:col-span-9 space-y-6">

                <!-- =========================
                     FIX UTAMA: jangan tampilkan warning kuning jika sudah verified
                     ========================= -->
                <?php if (!$isLocked && !$canDaftarUlang): ?>
                    <div class="rounded-2xl bg-amber-50 text-amber-900 ring-1 ring-amber-200 p-4">
                        <div class="font-semibold">Form Daftar Ulang belum dibuka</div>
                        <div class="text-sm mt-1">Status tes Anda belum lulus. Form ini akan aktif otomatis setelah dinyatakan lulus oleh admin.</div>
                    </div>
                <?php endif; ?>

                <?php if ($reviewStatus === 'need_fix'): ?>
                    <div class="mb-4 rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4">
                        <div class="font-bold text-amber-800">Perlu Perbaikan</div>
                        <div class="text-sm text-amber-700 mt-1">
                            Berkas kamu diminta diperbaiki oleh admin. Silakan upload ulang.
                        </div>

                        <?php if (!empty($daftarUlang->admin_note)): ?>
                            <div class="mt-2 text-sm text-amber-800">
                                <span class="font-semibold">Catatan admin:</span>
                                <?= h($daftarUlang->admin_note) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($reviewStatus === 'verified'): ?>
                    <div class="mb-4 rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4">
                        <div class="font-bold text-emerald-800">Terverifikasi</div>
                        <div class="text-sm text-emerald-700 mt-1">
                            Berkas kamu sudah diverifikasi. Status kamu aktif. Upload sudah dikunci.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="text-slate-300 text-sm">SiPentol</div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Form Daftar Ulang</h1>
                            <p class="text-slate-300 mt-2">
                                Download draft PDF, isi dengan tulisan tangan, kemudian scan dan upload sesuai kolom.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                form="daftarUlangForm"
                                <?= $disabled ? 'disabled' : '' ?>
                                class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold shadow-lg transition
                                <?= $disabled ? $btnPrimaryDisabled : $btnPrimaryEnabled ?>"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-4">
                    <div class="text-sm text-slate-600">
                        Upload file sesuai ketentuan: PDF untuk dokumen scan, gambar untuk bukti pembayaran.
                    </div>
                </div>

                <?= $this->Form->create($daftarUlang, [
                    'id' => 'daftarUlangForm',
                    'type' => 'file',
                    'class' => 'space-y-6',
                ]) ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- 1) Formulir pendaftaran (PDF) -->
                    <div class="<?= $card ?>">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Formulir Pendaftaran (Scan PDF)</h2>
                                <p class="text-sm text-slate-600 mt-1">Upload hasil scan formulir yang sudah diisi.</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <?php if (!$canDaftarUlang): ?>
                                    <span class="text-xs font-semibold text-slate-400 cursor-not-allowed">Download Draft</span>
                                <?php else: ?>
                                    <a href="<?= $this->Url->build('/daftar-ulang/draft/formulir') ?>"
                                       class="<?= $btnDraft ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Download Draft
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($formulirLink)): ?>
                                    <a href="<?= h($formulirLink) ?>"
                                       class="<?= $btnView ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Lihat File
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="<?= $label ?>">Upload PDF</label>
                            <input type="file" name="formulir_pendaftaran_pdf" class="<?= $fileInput ?>" accept="application/pdf" <?= $disabled ? 'disabled' : '' ?>>
                            <div class="<?= $help ?>">Format: PDF. Ukuran disarankan maksimal 10MB.</div>
                        </div>
                    </div>

                    <!-- 2) Bukti pembayaran (Gambar) -->
                    <div class="<?= $card ?>">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Bukti Pembayaran Pelatihan (Gambar)</h2>
                                <p class="text-sm text-slate-600 mt-1">Upload bukti transfer/pembayaran.</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <?php if (!empty($buktiLink)): ?>
                                    <a href="<?= h($buktiLink) ?>"
                                       class="<?= $btnView ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Lihat File
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="<?= $label ?>">Upload gambar</label>
                            <input type="file" name="bukti_pembayaran_img" class="<?= $fileInput ?>" accept="image/jpeg,image/png,image/webp" <?= $disabled ? 'disabled' : '' ?>>
                            <div class="<?= $help ?>">Format: JPG/PNG/WebP. Ukuran disarankan maksimal 10MB.</div>
                        </div>
                    </div>

                    <!-- 3) Surat perjanjian (PDF) -->
                    <div class="<?= $card ?>">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Surat Perjanjian Keikutsertaan (Scan PDF)</h2>
                                <p class="text-sm text-slate-600 mt-1">Upload hasil scan surat perjanjian.</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <?php if (!$canDaftarUlang): ?>
                                    <span class="text-xs font-semibold text-slate-400 cursor-not-allowed">Download Draft</span>
                                <?php else: ?>
                                    <a href="<?= $this->Url->build('/daftar-ulang/draft/perjanjian') ?>"
                                       class="<?= $btnDraft ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Download Draft
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($perjanjianLink)): ?>
                                    <a href="<?= h($perjanjianLink) ?>"
                                       class="<?= $btnView ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Lihat File
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="<?= $label ?>">Upload PDF</label>
                            <input type="file" name="surat_perjanjian_pdf" class="<?= $fileInput ?>" accept="application/pdf" <?= $disabled ? 'disabled' : '' ?>>
                            <div class="<?= $help ?>">Format: PDF. Ukuran disarankan maksimal 10MB.</div>
                        </div>
                    </div>

                    <!-- 4) Surat persetujuan ortu (PDF) -->
                    <div class="<?= $card ?>">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Surat Persetujuan Orang Tua (Scan PDF)</h2>
                                <p class="text-sm text-slate-600 mt-1">Upload hasil scan surat persetujuan orang tua.</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <?php if (!$canDaftarUlang): ?>
                                    <span class="text-xs font-semibold text-slate-400 cursor-not-allowed">Download Draft</span>
                                <?php else: ?>
                                    <a href="<?= $this->Url->build('/daftar-ulang/draft/persetujuan-ortu') ?>"
                                       class="<?= $btnDraft ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Download Draft
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($ortuLink)): ?>
                                    <a href="<?= h($ortuLink) ?>"
                                       class="<?= $btnView ?>"
                                       target="_blank" rel="noopener noreferrer">
                                        Lihat File
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="<?= $label ?>">Upload PDF</label>
                            <input type="file" name="surat_persetujuan_orangtua_pdf" class="<?= $fileInput ?>" accept="application/pdf" <?= $disabled ? 'disabled' : '' ?>>
                            <div class="<?= $help ?>">Format: PDF. Ukuran disarankan maksimal 10MB.</div>
                        </div>
                    </div>

                </div>

                <!-- Footer action -->
                <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="text-sm text-slate-600">
                            Setelah semua berkas lengkap, admin akan melakukan pengecekan.
                        </div>

                        <button
                            type="submit"
                            <?= $disabled ? 'disabled' : '' ?>
                            class="inline-flex items-center justify-center rounded-xl px-6 py-3 text-sm font-semibold shadow-lg transition
                            <?= $disabled ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/20' ?>"
                        >
                            Simpan Semua Upload
                        </button>
                    </div>
                </div>

                <?= $this->Form->end() ?>

            </section>
        </div>
    </div>

    <?= $this->element('dashboard/mobile_sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'daftarulang']) ?>
</div>
