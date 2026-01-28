<?php
/** @var \App\View\AppView $this */
/** @var \App\Model\Entity\Pengumuman $pengumuman */
/** @var iterable $users */
/** @var array|null $selectedUserIds */

$selectedUserIds = $selectedUserIds ?? [];
$target = (string)($pengumuman->target ?? 'semua');

if (!empty($pengumuman->target_user_ids) && empty($selectedUserIds)) {
  $decoded = json_decode((string)$pengumuman->target_user_ids, true);
  if (is_array($decoded)) $selectedUserIds = array_values(array_unique(array_map('intval', $decoded)));
}
?>

<div class="space-y-6">

  <div>
    <label class="block text-sm font-semibold text-slate-700 mb-2">Judul</label>
    <?= $this->Form->control('judul', [
      'label' => false,
      'class' => 'w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-300',
      'placeholder' => 'Judul pengumuman...'
    ]) ?>
  </div>

  <div>
    <label class="block text-sm font-semibold text-slate-700 mb-2">Isi</label>
    <?= $this->Form->control('isi', [
      'type' => 'textarea',
      'label' => false,
      'rows' => 6,
      'class' => 'w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-300',
      'placeholder' => 'Isi pengumuman...'
    ]) ?>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-semibold text-slate-700 mb-2">Target</label>
      <?= $this->Form->control('target', [
        'label' => false,
        'type' => 'select',
        'options' => [
          'semua' => 'Semua User',
          'tertentu' => 'User Tertentu'
        ],
        'value' => $target,
        'class' => 'w-full rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-300',
        'id' => 'targetSelect'
      ]) ?>
      <p class="text-xs text-slate-500 mt-2">Pilih “User Tertentu” jika hanya ingin menampilkan ke user tertentu.</p>
    </div>

    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-4 py-3">
      <?= $this->Form->control('is_active', [
        'type' => 'checkbox',
        'label' => false,
        'hiddenField' => false,
        'checked' => !empty($pengumuman->is_active),
        'class' => 'w-5 h-5 rounded border-slate-300',
        'id' => 'isActive'
      ]) ?>
      <div>
        <div class="font-semibold text-slate-700">Aktifkan</div>
        <div class="text-sm text-slate-500">Tampilkan pengumuman ini di dashboard siswa.</div>
      </div>
    </div>
  </div>

  <!-- Checkbox list user -->
  <div id="userPickerWrap" class="rounded-2xl border border-slate-200 bg-white/70 p-4">
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="font-semibold text-slate-800">User Tertentu</div>
        <div class="text-sm text-slate-500">Centang user yang ingin menerima pengumuman ini.</div>
      </div>

      <div class="flex items-center gap-2">
        <button type="button" id="btnCheckAll"
          class="px-3 py-2 text-sm font-semibold rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50">
          Check all
        </button>
        <button type="button" id="btnUncheckAll"
          class="px-3 py-2 text-sm font-semibold rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50">
          Clear
        </button>
      </div>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[320px] overflow-auto pr-1">
      <?php foreach ($users as $u): ?>
        <?php
          $uid = (int)$u->id;
          $checked = in_array($uid, $selectedUserIds, true);
        ?>
        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 hover:bg-slate-50 cursor-pointer">
          <input
            type="checkbox"
            name="target_user_ids[]"
            value="<?= h((string)$uid) ?>"
            class="mt-1 w-5 h-5 rounded border-slate-300 userCheck"
            <?= $checked ? 'checked' : '' ?>
          />
          <div class="min-w-0">
            <div class="font-semibold text-slate-800 truncate"><?= h((string)($u->nama_lengkap ?? 'User')) ?></div>
            <div class="text-xs text-slate-500 truncate"><?= h((string)($u->email ?? '')) ?></div>
            <div class="text-xs text-slate-400 mt-1">ID: <?= h((string)$uid) ?></div>
          </div>
        </label>
      <?php endforeach; ?>
    </div>

    <p class="text-xs text-slate-500 mt-3">Catatan: Jika target “Semua User”, list ini otomatis dinonaktifkan.</p>
  </div>

</div>

<script>
  (function () {
    const targetSelect = document.getElementById('targetSelect');
    const wrap = document.getElementById('userPickerWrap');
    const checks = () => Array.from(document.querySelectorAll('.userCheck'));

    const apply = () => {
      const isTertentu = targetSelect.value === 'tertentu';
      wrap.style.opacity = isTertentu ? '1' : '.55';
      wrap.style.pointerEvents = isTertentu ? 'auto' : 'none';

      // kalau bukan tertentu, uncheck semua biar gak kepost
      if (!isTertentu) {
        checks().forEach(c => c.checked = false);
      }
    };

    document.getElementById('btnCheckAll')?.addEventListener('click', () => {
      if (targetSelect.value !== 'tertentu') return;
      checks().forEach(c => c.checked = true);
    });

    document.getElementById('btnUncheckAll')?.addEventListener('click', () => {
      checks().forEach(c => c.checked = false);
    });

    targetSelect.addEventListener('change', apply);
    apply();
  })();
</script>
