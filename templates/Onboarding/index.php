<?php
/** @var \App\View\AppView $this */

$this->assign('title', 'Onboarding');

// safety: kalau controller lupa set, ambil dari request
$identity = $identity ?? $this->request->getAttribute('identity');

// dari controller
$reviewStatus = $reviewStatus ?? 'draft';
$isVerified   = $isVerified ?? false;

$groupUrl = $groupUrl ?? '';
$adminWa1 = $adminWa1 ?? '';
$adminWa2 = $adminWa2 ?? '';

$nama   = $identity ? (string)$identity->get('nama_lengkap') : 'Peserta';
$email  = $identity ? (string)$identity->get('email') : '';
$status = $identity ? (string)$identity->get('status') : 'pendaftaran';

$statusText = 'Pendaftaran';
$badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-200';

// initials
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
    ['label' => 'Daftar Ulang',     'href' => $this->Url->build('/daftar-ulang'), 'active' => false, 'enabled' => true],
    ['label' => 'Onboarding',       'href' => $this->Url->build('/onboarding'),   'active' => true,  'enabled' => true],
];

// helper WA link
$waLink = function(string $raw): string {
    $num = preg_replace('/\D+/', '', $raw);
    if ($num === '') return '#';
    if (str_starts_with($num, '0')) $num = '62' . substr($num, 1);
    return 'https://wa.me/' . $num;
};

$cardDark = 'rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl shadow-xl shadow-black/10';
$cardInner = 'rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl';

$btnPrimaryEnabled  = 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/20';
$btnPrimaryDisabled = 'bg-white/10 text-slate-300 cursor-not-allowed ring-1 ring-white/10';

$btnSoft = 'inline-flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-2 ring-1 ring-white/10 transition';

$joinEnabled = ($isVerified && !empty($groupUrl));
$joinLabel   = $joinEnabled ? 'Join Grup WhatsApp' : 'Link Grup Belum Tersedia';

?>
<div class="min-h-screen bg-slate-950 text-white relative overflow-hidden">
    <!-- Background blobs (samain kayak halaman lain) -->
    <div class="absolute -top-40 -left-40 w-[520px] h-[520px] bg-indigo-600/25 blur-3xl rounded-full"></div>
    <div class="absolute top-32 -right-44 w-[560px] h-[560px] bg-fuchsia-600/20 blur-3xl rounded-full"></div>
    <div class="absolute bottom-[-220px] left-1/3 w-[680px] h-[680px] bg-cyan-500/10 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Mobile Topbar -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <div>
                <div class="text-white text-xl font-semibold">SiPentol</div>
                <div class="text-slate-300 text-sm">Onboarding</div>
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
            <?= $this->element('dashboard/sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'onboarding']) ?>

            <!-- MAIN -->
            <section class="lg:col-span-9 space-y-6">

                <!-- Banner status (kunci onboarding sebelum verified) -->
                <?php if (!$isVerified): ?>
                    <div class="rounded-2xl bg-amber-50 text-amber-900 ring-1 ring-amber-200 p-4">
                        <div class="font-semibold">Onboarding belum dibuka</div>
                        <div class="text-sm mt-1">
                            Onboarding akan aktif setelah Daftar Ulang kamu <b>diverifikasi</b> oleh admin.
                            (Status saat ini: <b><?= h($reviewStatus) ?></b>)
                        </div>
                    </div>
                <?php else: ?>
                    <div class="rounded-2xl bg-emerald-50 text-emerald-900 ring-1 ring-emerald-200 p-4">
                        <div class="font-semibold">Selamat bergabung 🎉</div>
                        <div class="text-sm mt-1">
                            Daftar ulang kamu sudah <b>terverifikasi</b>. Silakan join grup WhatsApp dan ikuti arahan admin.
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="<?= $cardInner ?> p-6">
                    <div class="text-slate-300 text-sm">SiPentol</div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight mt-1">
                        Selamat datang di LPK JECA
                    </h1>
                    <p class="text-slate-300 mt-2">
                        Di halaman ini kamu akan mendapatkan info langkah selanjutnya (grup WhatsApp, kontak admin, dan catatan).
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grup WA -->
                    <div class="<?= $cardDark ?> p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-bold">Grup WhatsApp Resmi</div>
                                <div class="text-sm text-slate-300 mt-1">
                                    Gabung grup untuk info jadwal, pengumuman, dan arahan lanjutan dari admin.
                                </div>
                            </div>
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-white/10 ring-1 ring-white/10 flex items-center justify-center">
                                    <!-- icon -->
                                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <?php if ($joinEnabled): ?>
                                <a
                                    href="<?= h($groupUrl) ?>"
                                    target="_blank" rel="noopener noreferrer"
                                    class="w-full inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold shadow-lg transition <?= $btnPrimaryEnabled ?>"
                                >
                                    Join Grup WhatsApp
                                </a>
                                <div class="text-xs text-slate-400 mt-2">
                                    Link grup diberikan oleh admin. Jika tidak bisa dibuka, hubungi admin di kanan.
                                </div>
                            <?php else: ?>
                                <button
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold shadow-lg transition <?= $btnPrimaryDisabled ?>"
                                >
                                    <?= h($joinLabel) ?>
                                </button>

                                <div class="text-xs text-slate-400 mt-2">
                                    <?php if (!$isVerified): ?>
                                        Selesaikan proses Daftar Ulang terlebih dahulu. Setelah diverifikasi, tombol join akan aktif.
                                    <?php else: ?>
                                        Link grup belum diinput admin. Silakan hubungi admin di kanan.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kontak Admin -->
                    <div class="<?= $cardDark ?> p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-bold">Kontak Admin</div>
                                <div class="text-sm text-slate-300 mt-1">
                                    Jika ada kendala (akun, jadwal, dokumen), hubungi admin:
                                </div>
                            </div>
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-white/10 ring-1 ring-white/10 flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92V21a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 1 5.18 2 2 0 0 1 3 3h4.09a2 2 0 0 1 2 1.72c.12.81.3 1.6.54 2.36a2 2 0 0 1-.45 2.11L7.91 10.91a16 16 0 0 0 6 6l1.72-1.27a2 2 0 0 1 2.11-.45c.76.24 1.55.42 2.36.54A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            <?php if (!empty($adminWa1)): ?>
                                <a
                                    href="<?= h($waLink($adminWa1)) ?>"
                                    target="_blank" rel="noopener noreferrer"
                                    class="w-full inline-flex items-center justify-between rounded-xl bg-white/10 hover:bg-white/15 ring-1 ring-white/10 px-4 py-3 transition"
                                >
                                    <span class="text-sm font-semibold"><?= h($adminWa1) ?></span>
                                    <span class="text-xs font-semibold text-white/80">Chat WhatsApp</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($adminWa2)): ?>
                                <a
                                    href="<?= h($waLink($adminWa2)) ?>"
                                    target="_blank" rel="noopener noreferrer"
                                    class="w-full inline-flex items-center justify-between rounded-xl bg-white/10 hover:bg-white/15 ring-1 ring-white/10 px-4 py-3 transition"
                                >
                                    <span class="text-sm font-semibold"><?= h($adminWa2) ?></span>
                                    <span class="text-xs font-semibold text-white/80">Chat WhatsApp</span>
                                </a>
                            <?php endif; ?>

                            <?php if (empty($adminWa1) && empty($adminWa2)): ?>
                                <div class="text-sm text-slate-300">
                                    Kontak admin belum diinput.
                                </div>
                            <?php endif; ?>

                            <div class="text-xs text-slate-400">
                                Jam operasional admin mengikuti jadwal kerja kantor.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="<?= $cardDark ?> p-6">
                    <div class="text-lg font-bold">Catatan</div>
                    <ul class="mt-3 space-y-2 text-sm text-slate-300 list-disc list-inside">
                        <li>Pastikan nomor WhatsApp kamu aktif dan bisa menerima pesan.</li>
                        <li>Info agenda akan diumumkan melalui grup dan dashboard.</li>
                        <li>Jika tombol join belum aktif, selesaikan daftar ulang terlebih dahulu.</li>
                    </ul>
                </div>

            </section>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <?= $this->element('dashboard/mobile_sidebar', compact('identity','nama','email','initials','badgeClass','statusText','nav') + ['active' => 'onboarding']) ?>
</div>
