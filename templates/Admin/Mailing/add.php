<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface $users
 */
$this->assign('title', 'Kirim Email');

$sender = 'sipentol@jecaid.com';

// list placeholder yang tersedia
$placeholders = [
  ['key' => '{{nama_lengkap}}',  'label' => 'Nama Lengkap (nama_lengkap)'],
  ['key' => '{{email}}',         'label' => 'Email (email)'],
  ['key' => '{{test_access_id}}','label' => 'Test Access ID (online_tests.test_access_id)'],
  ['key' => '{{test_url}}',      'label' => 'Test URL (online_tests.test_url)'],
];
?>

<div class="min-h-[calc(100vh-120px)]">
  <div class="max-w-6xl">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-3xl font-extrabold text-slate-900">Kirim Email</div>
        <div class="text-slate-600 mt-1">
          Pengirim: <span class="font-semibold text-slate-900"><?= h($sender) ?></span>
        </div>
      </div>

      <a href="<?= $this->Url->build(['action' => 'index']) ?>"
         class="inline-flex items-center justify-center px-4 h-11 rounded-2xl bg-white/70 ring-1 ring-slate-200 hover:bg-white text-slate-700 font-semibold">
        Kembali
      </a>
    </div>

    <?= $this->Form->create(null, [
      'class' => 'mt-6',
      'id' => 'mailingForm',
    ]) ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
      <!-- LEFT: Penerima -->
      <div class="lg:col-span-4">
        <div class="p-5 rounded-3xl bg-white/60 ring-1 ring-slate-200">
          <div class="flex items-start justify-between gap-4">
            <div>
              <div class="text-lg font-extrabold text-slate-900">Penerima</div>
              <div class="text-sm text-slate-500 mt-1">Centang user yang akan menerima email ini.</div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 select-none">
              <input id="checkAllUsers" type="checkbox" class="w-4 h-4 rounded border-slate-300">
              Pilih semua
            </label>
          </div>

          <!-- list user scroll -->
          <div class="mt-4 max-h-[360px] overflow-y-auto pr-2 space-y-2">
            <?php foreach ($users as $u): ?>
              <label class="group flex items-start gap-3 p-3 rounded-2xl bg-white/70 ring-1 ring-slate-200 hover:bg-white transition cursor-pointer">
                <input
                  type="checkbox"
                  name="user_ids[]"
                  value="<?= (int)$u->id ?>"
                  class="userChk mt-1 w-4 h-4 rounded border-slate-300"
                >
                <div class="min-w-0">
                  <div class="font-extrabold text-slate-900 leading-tight">
                    <?= h($u->nama_lengkap ?: 'User') ?>
                  </div>
                  <div class="text-sm text-slate-500 truncate"><?= h($u->email) ?></div>
                  <div class="text-xs text-slate-400 mt-0.5">ID: <?= (int)$u->id ?></div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>

          <!-- IMPORTANT: controller kamu baca key "is_all" (bukan target_all) -->
          <label class="mt-4 flex items-start gap-3 p-4 rounded-2xl bg-white/70 ring-1 ring-slate-200 cursor-pointer hover:bg-white transition">
            <input id="isAll" name="is_all" value="1" type="checkbox" class="mt-1 w-4 h-4 rounded border-slate-300">
            <div>
              <div class="font-extrabold text-slate-900">Jadikan target “semua user” (override checkbox)</div>
              <div class="text-sm text-slate-500 mt-1">
                Jika dicentang, email akan dikirim ke semua user terlepas dari pilihan penerima.
              </div>
            </div>
          </label>

          <div class="mt-3 text-xs text-slate-500">
            Placeholder tersedia:
            <?php foreach ($placeholders as $ph): ?>
              <span class="inline-flex items-center px-2 py-0.5 rounded-xl bg-slate-900/5 text-slate-700 font-semibold mr-1">
                <?= h($ph['key']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- RIGHT: Subject + Editor -->
      <div class="lg:col-span-8">
        <div class="p-5 rounded-3xl bg-white/60 ring-1 ring-slate-200">
          <div class="space-y-4">
            <div>
              <div class="text-sm font-extrabold text-slate-800 mb-2">Subject</div>
              <input
                type="text"
                name="subject"
                id="subject"
                placeholder="Contoh: Jadwal Tes / Pengumuman Daftar Ulang / dll"
                class="w-full h-12 px-4 rounded-2xl bg-white/80 ring-1 ring-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
              >
            </div>

            <div>
              <div class="flex items-center justify-between gap-3 mb-2">
                <div class="text-sm font-extrabold text-slate-800">Body</div>

                <!-- Dropdown placeholder (insert ke cursor) -->
                <div class="relative">
                  <button type="button" id="phBtn"
                          class="inline-flex items-center gap-2 px-3 h-10 rounded-2xl bg-white/80 ring-1 ring-slate-200 hover:bg-white text-slate-700 font-semibold">
                    <span class="text-slate-900">{ }</span>
                    Placeholder
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 opacity-70">
                      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                    </svg>
                  </button>

                  <div id="phMenu"
                       class="hidden absolute right-0 mt-2 w-[320px] p-2 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 z-20">
                    <?php foreach ($placeholders as $ph): ?>
                      <button type="button"
                              data-ph="<?= h($ph['key']) ?>"
                              class="phItem w-full text-left px-3 py-2 rounded-xl hover:bg-slate-50">
                        <div class="font-extrabold text-slate-900"><?= h($ph['key']) ?></div>
                        <div class="text-xs text-slate-500 mt-0.5"><?= h($ph['label']) ?></div>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <!-- TOOLBAR + Editor -->
              <div class="rounded-2xl ring-1 ring-slate-200 bg-white/80 overflow-hidden">
                <div id="toolbar" class="flex flex-wrap items-center gap-1 px-2 py-2 border-b border-slate-200">
                  <!-- format block -->
                  <select id="fmtBlock"
                          class="h-9 px-3 rounded-xl bg-white ring-1 ring-slate-200 text-sm font-semibold text-slate-700">
                    <option value="p">Normal</option>
                    <option value="h1">Heading 1</option>
                    <option value="h2">Heading 2</option>
                    <option value="h3">Heading 3</option>
                  </select>

                  <div class="w-px h-8 bg-slate-200 mx-1"></div>

                  <button type="button" class="tbtn" data-cmd="bold" title="Bold"><b>B</b></button>
                  <button type="button" class="tbtn italic" data-cmd="italic" title="Italic"><i>I</i></button>
                  <button type="button" class="tbtn underline" data-cmd="underline" title="Underline"><u>U</u></button>

                  <div class="w-px h-8 bg-slate-200 mx-1"></div>

                  <button type="button" class="tbtn" data-cmd="insertUnorderedList" title="Bullets">• List</button>
                  <button type="button" class="tbtn" data-cmd="insertOrderedList" title="Numbered">1. List</button>

                  <div class="w-px h-8 bg-slate-200 mx-1"></div>

                  <button type="button" class="tbtn" id="linkBtn" title="Link">🔗</button>
                  <button type="button" class="tbtn" id="imgBtn" title="Image">🖼</button>

                  <div class="w-px h-8 bg-slate-200 mx-1"></div>

                  <button type="button" class="tbtn" data-cmd="removeFormat" title="Clear format">Tx</button>
                </div>

                <!-- contenteditable editor -->
                <div id="editor"
                     class="min-h-[360px] p-4 outline-none text-slate-800"
                     contenteditable="true"
                     data-placeholder="Tulis email di sini..."></div>
              </div>

              <div class="text-xs text-slate-500 mt-2">
                Editor menghasilkan HTML. Hindari paste script/iframe.
              </div>

              <!-- IMPORTANT: controller kamu baca key "body" (bukan body_html) -->
              <input type="hidden" name="body" id="body">
              <!-- optional: controller kamu cek confirmed === '1' -->
              <input type="hidden" name="confirmed" id="confirmed" value="0">

            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
              <button type="button" id="previewBtn"
                      class="inline-flex items-center justify-center px-5 h-11 rounded-2xl bg-white/80 ring-1 ring-slate-200 hover:bg-white text-slate-800 font-extrabold">
                Preview per User
              </button>

              <a href="<?= $this->Url->build(['action' => 'index']) ?>"
                 class="inline-flex items-center justify-center px-5 h-11 rounded-2xl bg-white/80 ring-1 ring-slate-200 hover:bg-white text-slate-800 font-extrabold">
                Batal
              </a>

              <button type="submit" id="submitBtn"
                      class="inline-flex items-center justify-center px-6 h-11 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold shadow">
                Kirim Email
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?= $this->Form->end() ?>
  </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-slate-900/40"></div>

  <div class="relative w-[min(980px,92vw)] max-h-[88vh] mx-auto mt-[6vh] rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
      <div>
        <div class="font-extrabold text-slate-900 text-lg">Preview Email</div>
        <div class="text-sm text-slate-500">Pastikan placeholder sudah berubah sesuai user yang dipilih.</div>
      </div>
      <button type="button" id="closePreview"
              class="px-4 h-10 rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 font-extrabold text-slate-700">
        Tutup
      </button>
    </div>

    <div class="p-5 space-y-4 overflow-y-auto" style="max-height: calc(88vh - 72px);">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-4">
          <div class="flex items-center justify-between gap-3 mb-2">
            <div class="text-sm font-extrabold text-slate-800">Pilih User</div>

            <div class="flex items-center gap-2">
              <span id="previewCounter" class="text-xs font-semibold text-slate-500">0 / 0</span>

              <button type="button" id="prevUserBtn"
                      class="px-3 h-9 rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 font-extrabold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">
                Prev
              </button>

              <button type="button" id="nextUserBtn"
                      class="px-3 h-9 rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 font-extrabold text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">
                Next
              </button>
            </div>
          </div>

          <select id="previewUserSelect"
                  class="w-full h-11 px-3 rounded-2xl bg-white ring-1 ring-slate-200 font-semibold text-slate-800">
            <option value="">-- pilih user --</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= (int)$u->id ?>">
                <?= h(($u->nama_lengkap ?: 'User') . ' — ' . $u->email . ' (ID: ' . (int)$u->id . ')') ?>
              </option>
            <?php endforeach; ?>
          </select>

          <div class="mt-3 text-xs text-slate-500">
            Endpoint preview yang dipakai: <span class="font-mono">/rx78gpo1p6/mailing/preview-user</span>
          </div>
        </div>

        <div class="md:col-span-8">
          <div class="text-sm font-extrabold text-slate-800 mb-2">Subject (Preview)</div>
          <div id="previewSubject"
               class="px-4 py-3 rounded-2xl bg-slate-50 ring-1 ring-slate-200 text-slate-900 font-semibold">
            -
          </div>
        </div>
      </div>

      <div>
        <div class="text-sm font-extrabold text-slate-800 mb-2">Body (Preview)</div>
        <div id="previewBody"
             class="rounded-2xl bg-white ring-1 ring-slate-200 p-5 min-h-[220px] prose max-w-none">
          <div class="text-slate-500 text-sm">Pilih user untuk memuat preview.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Confirm Send Modal (custom, bukan alert/confirm bawaan browser) -->
<div id="confirmSendModal" class="hidden fixed inset-0 z-[60]">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

  <div class="relative w-[min(520px,92vw)] mx-auto mt-[18vh] rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
      <div class="text-lg font-extrabold text-slate-900">Konfirmasi Pengiriman</div>
      <div id="confirmSendDesc" class="text-sm text-slate-500 mt-1">
        Kirim email ini?
      </div>
    </div>

    <div class="px-6 py-5">
      <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wide">Subject</div>
        <div id="confirmSendSubject" class="mt-1 font-semibold text-slate-900 break-words">-</div>

        <div class="mt-4 text-xs font-extrabold text-slate-600 uppercase tracking-wide">Penerima</div>
        <div id="confirmSendTo" class="mt-1 text-slate-700 font-semibold">-</div>
      </div>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
      <button type="button" id="confirmCancelBtn"
              class="inline-flex items-center justify-center px-5 h-11 rounded-2xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 text-slate-800 font-extrabold">
        Batal
      </button>

      <button type="button" id="confirmOkBtn"
              class="inline-flex items-center justify-center px-6 h-11 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold shadow">
        Ya, Kirim
      </button>
    </div>
  </div>
</div>

<style>
  /* tombol toolbar */
  .tbtn{
    height: 36px;
    padding: 0 10px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 14px;
    color: rgb(51 65 85);
    background: rgba(255,255,255,.9);
    border: 1px solid rgb(226 232 240);
  }
  .tbtn:hover{ background: rgb(248 250 252); }

  /* placeholder */
  #editor:empty:before{
    content: attr(data-placeholder);
    color: rgb(148 163 184);
  }

  /* FIX LISTS: Tailwind/reset sering bikin list-style hilang */
  #editor ul, #editor ol{
    margin: 0.25rem 0;
    padding-left: 1.25rem;
    list-style-position: outside;
  }
  #editor ul{ list-style-type: disc; }
  #editor ol{ list-style-type: decimal; }
  #editor li{ margin: 0.15rem 0; }

  /* optional: supaya heading keliatan */
  #editor h1{ font-size: 1.5rem; font-weight: 800; margin: .5rem 0; }
  #editor h2{ font-size: 1.25rem; font-weight: 800; margin: .5rem 0; }
  #editor h3{ font-size: 1.125rem; font-weight: 800; margin: .5rem 0; }
</style>

<script>
(function(){
  // ===== helpers
  function qs(sel){ return document.querySelector(sel); }
  function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

  // ===== recipients
  const checkAll = qs('#checkAllUsers');
  const userChks = () => qsa('.userChk');
  const isAll = qs('#isAll');

  if (checkAll) {
    checkAll.addEventListener('change', function(){
      userChks().forEach(chk => chk.checked = checkAll.checked);
    });
  }

  // kalau is_all dicentang, UX: matikan checklist manual (optional tapi enak)
  function syncRecipientUI(){
    const all = !!(isAll && isAll.checked);
    if (checkAll) checkAll.disabled = all;
    userChks().forEach(chk => chk.disabled = all);
  }
  if (isAll) {
    isAll.addEventListener('change', syncRecipientUI);
    syncRecipientUI();
  }

  // ===== editor (execCommand based) + FIX selection (biar list/numbering pasti jalan)
  const editor = qs('#editor');
  const hiddenBody = qs('#body'); // IMPORTANT
  const fmtBlock = qs('#fmtBlock');
  const toolbar = qs('#toolbar');

  let savedRange = null;

  function saveSelection(){
    const sel = window.getSelection();
    if (!sel || sel.rangeCount === 0) return;
    savedRange = sel.getRangeAt(0);
  }

  function restoreSelection(){
    if (!savedRange) return;
    const sel = window.getSelection();
    if (!sel) return;
    sel.removeAllRanges();
    sel.addRange(savedRange);
  }

  function syncHtml(){
    if (!hiddenBody || !editor) return;
    hiddenBody.value = editor.innerHTML || '';
  }

  function exec(cmd, value=null){
    if (!editor) return;
    editor.focus();
    restoreSelection();
    document.execCommand(cmd, false, value);
    saveSelection();
    syncHtml();
  }

  // simpan selection saat user klik / ngetik di editor
  if (editor) {
    ['keyup','mouseup','focus','input'].forEach(ev => {
      editor.addEventListener(ev, () => {
        saveSelection();
        syncHtml();
      });
    });

    // default template awal (hanya kalau kosong)
    if (!editor.innerHTML.trim()) {
      editor.innerHTML =
        '<p>Halo, {{nama_lengkap}}</p>' +
        '<p>Berikut informasi dari admin:</p>' +
        '<p><br></p>' +
        '<p>ID Tes Anda: {{test_access_id}}<br>URL tes: {{test_url}}</p>' +
        '<p><br></p>' +
        '<p>Terima kasih.</p>' +
        '<p><b>LPK JECA</b></p>';
    }
    syncHtml();
    saveSelection();
  }

  // IMPORTANT: cegah toolbar button “mengambil fokus” (yang bikin list/numbering gagal)
  if (toolbar) {
    toolbar.addEventListener('mousedown', function(e){
      const btn = e.target.closest('button,select');
      if (btn) e.preventDefault();
      saveSelection();
    });
  }

  // bind commands
  qsa('.tbtn[data-cmd]').forEach(btn => {
    btn.addEventListener('click', () => exec(btn.dataset.cmd));
  });

  if (fmtBlock) {
    fmtBlock.addEventListener('change', () => {
      const v = fmtBlock.value || 'p';
      exec('formatBlock', v);
      fmtBlock.value = 'p';
    });
  }

  // link
  const linkBtn = qs('#linkBtn');
  if (linkBtn) {
    linkBtn.addEventListener('click', () => {
      const url = prompt('Masukkan URL (https://...)');
      if (!url) return;
      exec('createLink', url);
    });
  }

  // image
  const imgBtn = qs('#imgBtn');
  if (imgBtn) {
    imgBtn.addEventListener('click', () => {
      const url = prompt('Masukkan URL gambar (https://...)');
      if (!url) return;
      exec('insertImage', url);
    });
  }

  // ===== placeholder dropdown (insert at cursor)
  const phBtn = qs('#phBtn');
  const phMenu = qs('#phMenu');

  function closeMenu(){ phMenu && phMenu.classList.add('hidden'); }
  function toggleMenu(){ phMenu && phMenu.classList.toggle('hidden'); }

  if (phBtn && phMenu) {
    phBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMenu();
    });

    document.addEventListener('click', () => closeMenu());

    qsa('.phItem').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const ph = item.dataset.ph || '';
        if (!ph || !editor) return;

        editor.focus();
        restoreSelection();
        document.execCommand('insertText', false, ph);
        saveSelection();
        syncHtml();
        closeMenu();
      });
    });
  }

  // ===== confirm send modal (styling)
  const form = qs('#mailingForm');
  const confirmedInput = qs('#confirmed');

  const confirmModal = qs('#confirmSendModal');
  const confirmDesc = qs('#confirmSendDesc');
  const confirmSubject = qs('#confirmSendSubject');
  const confirmTo = qs('#confirmSendTo');
  const btnCancel = qs('#confirmCancelBtn');
  const btnOk = qs('#confirmOkBtn');

  let pendingSubmit = false;

  function openConfirm({count, subject, toText}){
    if (!confirmModal) return;
    if (confirmDesc) confirmDesc.textContent = `Kirim email ini ke ${count} user?`;
    if (confirmSubject) confirmSubject.textContent = subject || '-';
    if (confirmTo) confirmTo.textContent = toText || `User terpilih (${count})`;
    confirmModal.classList.remove('hidden');
  }

  function closeConfirm(){
    if (!confirmModal) return;
    confirmModal.classList.add('hidden');
  }

  if (btnCancel) {
    btnCancel.addEventListener('click', (e) => {
      e.preventDefault();
      pendingSubmit = false;
      closeConfirm();
    });
  }

  if (confirmModal) {
    confirmModal.addEventListener('click', (e) => {
      if (e.target === confirmModal || (e.target.classList && e.target.classList.contains('bg-slate-900/50'))) {
        pendingSubmit = false;
        closeConfirm();
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && confirmModal && !confirmModal.classList.contains('hidden')) {
      pendingSubmit = false;
      closeConfirm();
    }
  });

  if (btnOk) {
    btnOk.addEventListener('click', (e) => {
      e.preventDefault();
      closeConfirm();

      // set confirmed biar backend lolos
      if (confirmedInput) confirmedInput.value = '1';

      // lanjut submit yang ditahan
      if (form) {
        pendingSubmit = false;
        form.submit();
      }
    });
  }

  if (form) {
    form.addEventListener('submit', function(e){
      // kalau submit berarti sudah confirmed via modal
      if (pendingSubmit) return;

      e.preventDefault();
      syncHtml();

      const subject = (qs('#subject')?.value || '').trim();
      const html = (hiddenBody?.value || '').trim();

      const targetAll = !!(isAll && isAll.checked);
      const selectedCount = qsa('.userChk').filter(x => x.checked).length;
      const totalUsers = qsa('.userChk').length;

      if (!subject) { alert('Subject wajib diisi.'); return; }
      if (!html) { alert('Body email masih kosong.'); return; }
      if (!targetAll && selectedCount === 0) { alert('Pilih minimal 1 penerima, atau centang "semua user".'); return; }

      const count = targetAll ? totalUsers : selectedCount;

      // IMPORTANT: jangan set confirmed sekarang. confirmed hanya di-set saat OK.
      if (confirmedInput) confirmedInput.value = '0';

      pendingSubmit = true;

      const toText = targetAll ? `Semua user (${totalUsers})` : `User terpilih (${selectedCount})`;
      openConfirm({ count, subject, toText });
    });
  }

  // ===== preview modal
  const previewBtn = qs('#previewBtn');
  const modal = qs('#previewModal');
  const closePreview = qs('#closePreview');
  const previewUserSelect = qs('#previewUserSelect');
  const previewSubjectEl = qs('#previewSubject');
  const previewBodyEl = qs('#previewBody');

  const prevUserBtn = qs('#prevUserBtn');
  const nextUserBtn = qs('#nextUserBtn');
  const previewCounter = qs('#previewCounter');

  let previewList = [];
  let previewIndex = -1;

  function setCounter(){
    const total = previewList.length;
    const current = (previewIndex >= 0) ? (previewIndex + 1) : 0;

    if (previewCounter) previewCounter.textContent = `${current} / ${total}`;
    if (prevUserBtn) prevUserBtn.disabled = !(previewIndex > 0);
    if (nextUserBtn) nextUserBtn.disabled = !(previewIndex >= 0 && previewIndex < total - 1);
  }

  function buildPreviewList(){
    const targetAll = !!(isAll && isAll.checked);
    const allIds = qsa('.userChk').map(chk => chk.value);
    const selectedIds = qsa('.userChk').filter(chk => chk.checked).map(chk => chk.value);

    previewList = targetAll ? allIds : (selectedIds.length ? selectedIds : allIds);
    previewIndex = previewList.length ? 0 : -1;
    setCounter();
  }

  function openModal(){
    if (!modal) return;
    modal.classList.remove('hidden');

    if (previewSubjectEl) previewSubjectEl.textContent = (qs('#subject')?.value || '').trim() || '-';
    if (previewBodyEl) previewBodyEl.innerHTML = '<div class="text-slate-500 text-sm">Pilih user untuk memuat preview.</div>';

    buildPreviewList();

    if (previewUserSelect) previewUserSelect.value = previewList.length ? previewList[0] : '';
    if (previewList.length) loadPreview(previewList[0]);
  }

  function closeModal(){
    if (!modal) return;
    modal.classList.add('hidden');
  }

  if (previewBtn) previewBtn.addEventListener('click', (e) => { e.preventDefault(); openModal(); });
  if (closePreview) closePreview.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal || (e.target.classList && e.target.classList.contains('bg-slate-900/40'))) closeModal();
    });
  }
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
  });

  async function loadPreview(userId){
    const subject = (qs('#subject')?.value || '').trim();
    syncHtml();
    const body = hiddenBody?.value || '';

    if (previewSubjectEl) previewSubjectEl.textContent = subject || '-';
    if (previewBodyEl) previewBodyEl.innerHTML = '<div class="text-slate-500 text-sm">Loading preview...</div>';

    const url = <?= json_encode($this->Url->build([
      'prefix' => 'Admin',
      'controller' => 'Mailing',
      'action' => 'previewUser',
    ])) ?>;

    const csrf = document.querySelector('input[name="_csrfToken"]')?.value || '';

    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 8000);

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(csrf ? {'X-CSRF-Token': csrf} : {})
        },
        body: JSON.stringify({ user_id: userId, subject, body }), // NOTE: pakai key "body"
        signal: controller.signal
      });

      clearTimeout(timer);

      const ct = (res.headers.get('content-type') || '').toLowerCase();
      if (!ct.includes('application/json')) {
        if (previewBodyEl) {
          previewBodyEl.innerHTML =
            `<div class="text-rose-700 text-sm font-semibold">
              Gagal memuat preview (response bukan JSON). Status: ${res.status}
            </div>`;
        }
        return;
      }

      const data = await res.json().catch(() => null);
      if (!res.ok || !data || !data.ok) {
        if (previewBodyEl) {
          previewBodyEl.innerHTML =
            `<div class="text-rose-700 text-sm font-semibold">
              Gagal memuat preview. Status: ${res.status}
            </div>`;
        }
        return;
      }

      if (previewSubjectEl) previewSubjectEl.textContent = data.subject || subject || '-';
      if (previewBodyEl) previewBodyEl.innerHTML = data.body || '';
    } catch (err) {
      clearTimeout(timer);
      const msg = (err && err.name === 'AbortError')
        ? 'Request timeout (server tidak merespon).'
        : 'Request gagal (cek Network/Console).';

      if (previewBodyEl) previewBodyEl.innerHTML = `<div class="text-rose-700 text-sm font-semibold">${msg}</div>`;
    }
  }

  if (previewUserSelect) {
    previewUserSelect.addEventListener('change', function(){
      const userId = this.value;
      if (!userId) return;

      const idx = previewList.indexOf(userId);
      previewIndex = (idx >= 0) ? idx : 0;
      setCounter();
      loadPreview(userId);
    });
  }

  if (prevUserBtn) {
    prevUserBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (previewIndex > 0) {
        previewIndex--;
        const uid = previewList[previewIndex];
        if (previewUserSelect) previewUserSelect.value = uid;
        setCounter();
        loadPreview(uid);
      }
    });
  }

  if (nextUserBtn) {
    nextUserBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (previewIndex < previewList.length - 1) {
        previewIndex++;
        const uid = previewList[previewIndex];
        if (previewUserSelect) previewUserSelect.value = uid;
        setCounter();
        loadPreview(uid);
      }
    });
  }

})();
</script>
