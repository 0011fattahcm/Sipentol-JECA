<?php
/** @var \App\View\AppView $this */
/** @var \Cake\Datasource\ResultSetInterface|\Cake\ORM\ResultSet $users */
/** @var string $q */

$this->assign('title', 'User');

$q = (string)($q ?? $this->request->getQuery('q', ''));
$limit = (int)$this->request->getQuery('limit', 10);
if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 10;

// biar paginator selalu bawa query string (q, limit, dll)
$this->Paginator->setTemplates([
  'number' => '<a class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" href="{{url}}">{{text}}</a>',
  'current' => '<span class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center rounded-xl bg-slate-900 text-white font-semibold">{{text}}</span>',
  'ellipsis' => '<span class="min-w-[38px] h-10 px-3 inline-flex items-center justify-center text-slate-400">…</span>',
  'prevActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" rel="prev" href="{{url}}">Prev</a>',
  'prevDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/60 text-slate-400 font-semibold cursor-not-allowed">Prev</span>',
  'nextActive' => '<a class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold" rel="next" href="{{url}}">Next</a>',
  'nextDisabled' => '<span class="h-10 px-4 inline-flex items-center justify-center rounded-xl ring-1 ring-slate-200 bg-white/60 text-slate-400 font-semibold cursor-not-allowed">Next</span>',
]);
?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold">User</h1>
    <p class="text-slate-600 mt-1 text-sm">Kelola akun user: edit, aktif/nonaktif, dan hapus.</p>
  </div>

  <form class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2" method="get">
    <input
      name="q"
      value="<?= h($q) ?>"
      placeholder="Cari nama / email..."
      class="w-full sm:w-72 max-w-full px-4 py-2 rounded-2xl ring-1 ring-slate-200 bg-white/70 outline-none"
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
          <th class="py-3 px-3">Email</th>
          <th class="py-3 px-3">Status</th>
          <th class="py-3 px-3 text-right">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($users as $u): ?>
          <?php
              $isOff = ((int)($u->is_active ?? 1) === 0);
          ?>
          <tr class="border-t border-slate-200/70">
            <td class="py-3 px-3 font-semibold"><?= (int)$u->id ?></td>

            <td class="py-3 px-3">
              <div class="font-semibold text-slate-900"><?= h($u->nama_lengkap) ?></div>
              <div class="text-xs text-slate-500">Created: <?= h($u->created ?? '-') ?></div>
            </td>

            <td class="py-3 px-3 text-slate-700"><?= h($u->email) ?></td>

            <td class="py-3 px-3">
             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ring-1
  <?= $isOff ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200' ?>">
  <?= $isOff ? 'Nonaktif' : 'Aktif' ?>
</span>

            </td>

            <td class="py-3 px-3">
              <div class="flex justify-end gap-2">
                <a
                  href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'view', $u->id]) ?>"
                  class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
                >
                  Detail
                </a>

                <a
                  href="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'edit', $u->id, '?' => $this->request->getQueryParams()]) ?>"
                  class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
                >
                  Edit
                </a>

             <button
  type="button"
  class="px-3 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
  data-toggle-url="<?= h($this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'toggle', $u->id, '?' => $this->request->getQueryParams()])) ?>"
  data-toggle-title="<?= $isOff ? 'Aktifkan akun user?' : 'Nonaktifkan akun user?' ?>"
  data-toggle-desc="<?= $isOff
    ? 'User akan bisa login kembali setelah diaktifkan.'
    : 'User tidak akan bisa login jika akun dinonaktifkan.' ?>"
  data-toggle-actiontext="<?= $isOff ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan' ?>"
  data-toggle-variant="<?= $isOff ? 'success' : 'danger' ?>"
  onclick="openToggleModal(this)"
>
  <?= $isOff ? 'Aktifkan' : 'Nonaktifkan' ?>
</button>


                <button
  type="button"
  class="px-3 py-2 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700"
  data-delete-url="<?= h($this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'delete', $u->id, '?' => $this->request->getQueryParams()])) ?>"
  data-delete-title="Hapus user?"
  data-delete-desc="User ini akan dihapus permanen. Jika ada data terkait, bisa ikut bermasalah."
  onclick="openDeleteModal(this)"
>
  Hapus
</button>

              </div>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if ((int)$users->count() === 0): ?>
          <tr>
            <td colspan="5" class="py-6 px-3 text-slate-600">Tidak ada data user.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination bar -->
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

<?php
// Ambil CSRF token Cake (otomatis ada kalau CsrfProtectionMiddleware aktif)
$csrfToken = (string)$this->request->getAttribute('csrfToken');
?>

<!-- Toggle Modal -->
<div id="toggleModal" class="fixed inset-0 z-[100] hidden">
  <!-- overlay -->
  <div class="absolute inset-0 bg-slate-900/50" onclick="closeToggleModal()"></div>

  <!-- dialog -->
  <div class="relative mx-auto mt-24 w-[92%] max-w-md">
    <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden">
      <div class="p-5 flex items-start justify-between gap-3">
        <div class="flex items-start gap-3">
          <div id="toggleIconWrap" class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl ring-1">
            <!-- icon -->
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v4m0 4h.01"></path>
              <path d="M10.29 3.86l-7.5 13A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.71-3.14l-7.5-13a2 2 0 0 0-3.42 0Z"></path>
            </svg>
          </div>

          <div>
            <h3 id="toggleTitle" class="text-lg font-extrabold text-slate-900">Ubah status akun?</h3>
            <p id="toggleDesc" class="mt-1 text-sm text-slate-600">
              Konfirmasi perubahan status akun user.
            </p>
          </div>
        </div>

        <button type="button" class="rounded-xl p-2 hover:bg-slate-100" onclick="closeToggleModal()" aria-label="Close">
          <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 6l12 12M18 6l-12 12"></path>
          </svg>
        </button>
      </div>

      <div class="px-5 pb-5 flex items-center justify-end gap-2">
        <button
          type="button"
          class="px-4 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
          onclick="closeToggleModal()"
        >
          Batal
        </button>

        <form id="toggleForm" method="post" action="">
          <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
          <button
            id="toggleSubmit"
            type="submit"
            class="px-4 py-2 rounded-xl font-semibold"
          >
            Ya, Lanjutkan
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openDeleteModal(btn) {
    const modal = document.getElementById('deleteModal');
    const form  = document.getElementById('deleteForm');
    const title = document.getElementById('deleteTitle');
    const desc  = document.getElementById('deleteDesc');

    const url   = btn.getAttribute('data-delete-url') || '';
    const t     = btn.getAttribute('data-delete-title') || 'Hapus data?';
    const d     = btn.getAttribute('data-delete-desc') || 'Aksi ini tidak bisa dibatalkan.';

    form.action = url;
    title.textContent = t;
    desc.textContent = d;

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    window.addEventListener('keydown', escCloseDelete);
  }

  function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    window.removeEventListener('keydown', escCloseDelete);
  }

  function escCloseDelete(e) {
    if (e.key === 'Escape') closeDeleteModal();
  }

  // === Toggle Modal ===
  function openToggleModal(btn) {
    const modal  = document.getElementById('toggleModal');
    const form   = document.getElementById('toggleForm');
    const title  = document.getElementById('toggleTitle');
    const desc   = document.getElementById('toggleDesc');
    const submit = document.getElementById('toggleSubmit');
    const icon   = document.getElementById('toggleIconWrap');

    const url    = btn.getAttribute('data-toggle-url') || '';
    const t      = btn.getAttribute('data-toggle-title') || 'Ubah status akun?';
    const d      = btn.getAttribute('data-toggle-desc') || 'Konfirmasi perubahan status akun user.';
    const text   = btn.getAttribute('data-toggle-actiontext') || 'Ya, Lanjutkan';
    const varnt  = btn.getAttribute('data-toggle-variant') || 'danger'; // danger | success

    form.action = url;
    title.textContent = t;
    desc.textContent = d;
    submit.textContent = text;

    // variant styling
    if (varnt === 'success') {
      submit.className = 'px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700';
      icon.className   = 'mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-700';
    } else {
      submit.className = 'px-4 py-2 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700';
      icon.className   = 'mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 ring-1 ring-rose-200 text-rose-700';
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    window.addEventListener('keydown', escCloseToggle);
  }

  function closeToggleModal() {
    const modal = document.getElementById('toggleModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    window.removeEventListener('keydown', escCloseToggle);
  }

  function escCloseToggle(e) {
    if (e.key === 'Escape') closeToggleModal();
  }
</script>


<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] hidden">
  <!-- overlay -->
  <div class="absolute inset-0 bg-slate-900/50" onclick="closeDeleteModal()"></div>

  <!-- dialog -->
  <div class="relative mx-auto mt-24 w-[92%] max-w-md">
    <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden">
      <div class="p-5 flex items-start justify-between gap-3">
        <div class="flex items-start gap-3">
          <div class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 ring-1 ring-rose-200">
            <!-- icon -->
            <svg viewBox="0 0 24 24" class="h-5 w-5 text-rose-700" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v4m0 4h.01"></path>
              <path d="M10.29 3.86l-7.5 13A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.71-3.14l-7.5-13a2 2 0 0 0-3.42 0Z"></path>
            </svg>
          </div>

          <div>
            <h3 id="deleteTitle" class="text-lg font-extrabold text-slate-900">Hapus data?</h3>
            <p id="deleteDesc" class="mt-1 text-sm text-slate-600">
              Aksi ini tidak bisa dibatalkan.
            </p>
          </div>
        </div>

        <button type="button" class="rounded-xl p-2 hover:bg-slate-100" onclick="closeDeleteModal()" aria-label="Close">
          <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 6l12 12M18 6l-12 12"></path>
          </svg>
        </button>
      </div>

      <div class="px-5 pb-5 flex items-center justify-end gap-2">
        <button
          type="button"
          class="px-4 py-2 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 font-semibold"
          onclick="closeDeleteModal()"
        >
          Batal
        </button>

        <form id="deleteForm" method="post" action="">
          <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
          <button
            type="submit"
            class="px-4 py-2 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700"
          >
            Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openDeleteModal(btn) {
    const modal = document.getElementById('deleteModal');
    const form  = document.getElementById('deleteForm');
    const title = document.getElementById('deleteTitle');
    const desc  = document.getElementById('deleteDesc');

    const url   = btn.getAttribute('data-delete-url') || '';
    const t     = btn.getAttribute('data-delete-title') || 'Hapus data?';
    const d     = btn.getAttribute('data-delete-desc') || 'Aksi ini tidak bisa dibatalkan.';

    form.action = url;
    title.textContent = t;
    desc.textContent = d;

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    // ESC to close
    window.addEventListener('keydown', escCloseOnce);
  }

  function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    window.removeEventListener('keydown', escCloseOnce);
  }

  function escCloseOnce(e) {
    if (e.key === 'Escape') closeDeleteModal();
  }
</script>
