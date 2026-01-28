<?php
/**
 * @var \App\View\AppView $this
 * @var string $title
 */
$title = $this->fetch('title') ?: 'SiPentol';

$isAuthPage = false;
$action = (string)($this->request->getParam('action') ?? '');
$controller = (string)($this->request->getParam('controller') ?? '');

if ($controller === 'Users' && in_array($action, ['login', 'register', 'verifyOtp'], true)) {
    $isAuthPage = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= h($title) ?></title>

    <?= $this->Html->meta('csrfToken', $this->request->getAttribute('csrfToken')) ?>
    <?= $this->Html->css(['app']) ?>
</head>

<body class="<?= $isAuthPage ? 'min-h-screen bg-gray-100' : 'min-h-screen bg-slate-950' ?>">

<?php if ($isAuthPage): ?>
    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-6xl">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
<?php else: ?>
    <?= $this->Flash->render() ?>
    <?= $this->fetch('content') ?>

    <!-- Logout Modal (Tailwind, konsisten dengan UI gelap). -->
    <div id="logoutModal" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
        <!-- Overlay: visual only, jangan menangkap klik -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm pointer-events-none"></div>

        <!-- Dialog wrapper: wajib z-10 supaya di atas overlay -->
        <div class="relative z-10 min-h-screen flex items-center justify-center p-4 pointer-events-auto">
            <div class="w-full max-w-md rounded-2xl ring-1 ring-white/10 bg-slate-900/80 shadow-2xl overflow-hidden">
                <div class="px-6 py-5">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 w-10 h-10 rounded-2xl bg-rose-500/15 ring-1 ring-rose-500/20 flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-5 h-5 text-rose-300" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <path d="M16 17l5-5-5-5"/>
                                <path d="M21 12H9"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-white text-lg font-semibold leading-tight">Konfirmasi Logout</h3>
                            <p class="mt-1 text-white/70 text-sm">Anda yakin ingin keluar dari akun ini?</p>
                        </div>

                        <button type="button" id="logoutClose"
                                class="ml-2 w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 ring-1 ring-white/10 text-white/80 hover:text-white transition inline-flex items-center justify-center"
                                aria-label="Tutup">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 pb-6 flex items-center justify-end gap-3">
                    <button type="button" id="logoutCancel"
                            class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 ring-1 ring-white/10 text-white/80 hover:text-white text-sm font-semibold transition">
                        Batal
                    </button>

                    <!-- pointer-events-auto untuk jaga-jaga (anti layer blocking) -->
                    <button type="button" id="logoutConfirm"
                            class="pointer-events-auto h-11 px-5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold shadow ring-1 ring-rose-400/30 transition">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form logout (POST + CSRF) — hanya ada di halaman non-auth -->
    <?= $this->Form->create(null, [
      'url' => ['controller' => 'Users', 'action' => 'logout'],
      'id'  => 'logoutForm',
      'style' => 'display:none'
    ]) ?>
    <?= $this->Form->end() ?>

<?php endif; ?>

<?= $this->fetch('script') ?>

<?php if (!$isAuthPage): ?>
<script>
(function () {
  const modal = document.getElementById('logoutModal');
  const btnConfirm = document.getElementById('logoutConfirm');
  const btnCancel = document.getElementById('logoutCancel');
  const btnClose = document.getElementById('logoutClose');
  const form = document.getElementById('logoutForm');

  function openModal() {
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal() {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
  }

  // tombol logout (desktop + mobile)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-logout-btn');
    if (!btn) return;
    e.preventDefault();
    openModal();
  });

  btnConfirm.addEventListener('click', function () {
    if (form) form.submit();
  });

  btnCancel.addEventListener('click', closeModal);
  btnClose.addEventListener('click', closeModal);

  // klik area kosong (wrapper) untuk tutup: kita deteksi klik tepat pada container modal
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });

  // ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
  });
})();
</script>
<?php endif; ?>

</body>
</html>
