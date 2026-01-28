<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Login');
?>

<div class="w-full max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row">

    <!-- LEFT PANEL -->
    <div class="w-full lg:w-1/2 p-10 bg-gradient-to-br from-indigo-700 to-indigo-500 text-white relative">
        <div class="absolute inset-0 bg-white/10 backdrop-blur-[2px]"></div>

        <div class="relative z-10">
            <div class="flex justify-center mb-6">
                <?= $this->Html->image('jeca-logo.png', [
    'alt' => 'JECA Logo',
    'class' => 'w-24 h-24 rounded-full bg-white/70 p-2 shadow-lg'
]) ?>
            </div>

            <h1 class="text-3xl font-bold text-center tracking-wide">SiPentol JECA</h1>
            <p class="text-center text-indigo-100 mt-2 mb-10">
                Sistem Pendaftaran Online LPK JECA
            </p>

            <div class="rounded-xl bg-white/10 border border-white/15 p-5">
                <h2 class="text-lg font-semibold mb-3">Persyaratan Umum</h2>
                <ul class="space-y-2 text-indigo-50 text-sm leading-relaxed">
                    <li>• Minimal lulusan SMA/SMK sederajat</li>
                    <li>• Usia 18 s/d 30 tahun (saat pendaftaran)</li>
                    <li>• Sehat jasmani & rohani</li>
                    <li>• Tinggi badan ≥ 160 cm (pria) & 150 cm (wanita)</li>
                    <li>• Berat badan proporsional</li>
                    <li>• Sikap & mental baik</li>
                    <li>• Tidak terikat dengan LPK lain</li>
                    <li>• Tidak berstatus pelajar aktif / karyawan aktif</li>
                    <li>• Mendapat izin orang tua</li>
                    <li>• Belum pernah ikut program magang Jepang</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full lg:w-1/2 p-10 lg:p-12 flex items-center justify-center">
        <div class="w-full max-w-md">

            <h2 class="text-2xl font-bold text-gray-900 text-center">Masuk</h2>
            <p class="text-center text-gray-500 mt-2 mb-8">
                Gunakan email dan password yang Anda daftarkan.
            </p>

            <!-- Flash messages -->
            <?php if ($this->Flash->render()): ?>
                <div class="mb-5">
                    <?= $this->Flash->render() ?>
                </div>
            <?php endif; ?>

            <?= $this->Form->create(null, [
                'class' => 'space-y-5',
                'autocomplete' => 'on',
            ]) ?>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <!-- mail icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    </span>

                    <input
                        type="email"
                        name="email"
                        value="<?= h($this->request->getData('email') ?? '') ?>"
                        class="w-full rounded-xl border-gray-300 pl-11 pr-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="nama@email.com"
                        required
                    >
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <!-- lock icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3 1.343 3 3v3H9v-3c0-1.657 1.343-3 3-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 11V8a5 5 0 00-10 0v3" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11h10a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                        </svg>
                    </span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full rounded-xl border-gray-300 pl-11 pr-12 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Masukkan password"
                        required
                    >

                    <button
                        type="button"
                        onclick="togglePassword('password', 'eye-login')"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700"
                        aria-label="Toggle password"
                    >
                        <svg id="eye-login" xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-gray-500">Pastikan password sesuai saat registrasi.</span>
                    <!-- placeholder kalau nanti mau fitur lupa password -->
                <a href="<?= $this->Url->build('/forgot-password') ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
  Lupa password?
</a>

                </div>
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 shadow-md hover:shadow-lg transition"
            >
                Masuk ke Dashboard
            </button>

            <p class="text-center text-sm text-gray-600 mt-6">
                Belum punya akun?
                <a href="<?= $this->Url->build('/register') ?>" class="text-indigo-600 font-semibold hover:text-indigo-700">
                    Daftar di sini
                </a>
            </p>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
function togglePassword(id, iconId) {
    const field = document.getElementById(id);
    const icon  = document.getElementById(iconId);
    if (!field || !icon) return;

    if (field.type === "password") {
        field.type = "text";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-6 0-10-7-10-7a21.77 21.77 0 012.702-3.592M21 21l-4.35-4.35M9.53 9.53L3 3m6 6a3 3 0 104.243 4.243" />
        `;
    } else {
        field.type = "password";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
        `;
    }
}
</script>
