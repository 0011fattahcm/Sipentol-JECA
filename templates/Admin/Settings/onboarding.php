<?php
/** @var \App\View\AppView $this */
/** @var array $settings */

$this->assign('title', 'Onboarding Settings');

$groupUrl = (string)($settings['onboarding_group_url'] ?? '');
$wa1      = (string)($settings['onboarding_admin_wa_1'] ?? '');
$wa2      = (string)($settings['onboarding_admin_wa_2'] ?? '');
?>

<div class="space-y-5">
    <div class="rounded-2xl bg-white ring-1 ring-black/5 shadow-xl shadow-black/10 p-6">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-slate-900">Onboarding Settings</h1>
            <p class="text-sm text-slate-600 mt-1">
                Input link grup WhatsApp & kontak admin untuk tampil di halaman Onboarding user.
            </p>
        </div>

        <?= $this->Form->create(null, ['class' => 'space-y-6']) ?>

        <div>
            <label class="text-sm font-semibold text-slate-800">Link Grup WhatsApp</label>
            <input
                type="url"
                name="onboarding_group_url"
                value="<?= h($groupUrl) ?>"
                placeholder="https://chat.whatsapp.com/xxxxxx"
                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm
                       focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 outline-none"
            />
            <div class="text-xs text-slate-500 mt-1">Boleh kosong kalau belum ada.</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-800">WhatsApp Admin 1</label>
                <input
                    type="text"
                    name="onboarding_admin_wa_1"
                    value="<?= h($wa1) ?>"
                    placeholder="0813xxxxxxx"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm
                           focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 outline-none"
                />
                <div class="text-xs text-slate-500 mt-1">Hanya angka. Contoh: 0813...</div>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-800">WhatsApp Admin 2</label>
                <input
                    type="text"
                    name="onboarding_admin_wa_2"
                    value="<?= h($wa2) ?>"
                    placeholder="0896xxxxxxx"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm
                           focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 outline-none"
                />
                <div class="text-xs text-slate-500 mt-1">Hanya angka. Boleh kosong.</div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 text-sm font-semibold shadow-lg transition"
            >
                Simpan
            </button>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
