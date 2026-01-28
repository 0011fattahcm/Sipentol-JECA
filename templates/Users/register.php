<?php
$agreeError = $agreeError ?? null; // nanti dari controller kalau backend validasi gagal
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SiPentol JECA</title>
    <?= $this->Html->css('app.css') ?>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4">

<div class="max-w-6xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row">

    <!-- PANEL KIRI -->
<div class="w-full lg:w-1/2 p-10 bg-gradient-to-br from-indigo-600 to-indigo-500 text-white relative overflow-hidden">
  <div class="absolute inset-0 bg-white/10 backdrop-blur-md"></div>

  <div class="relative z-10">
    <div class="flex justify-center mb-6">
<?= $this->Html->image('jeca-logo.png', [
    'alt' => 'JECA Logo',
    'class' => 'w-24 h-24 rounded-full bg-white/70 p-2 shadow-lg'
]) ?>
    </div>

    <h1 class="text-3xl font-bold text-center mb-1 tracking-wide">SiPentol JECA</h1>
    <p class="text-center text-indigo-100 mb-8">Sistem Pendaftaran Online LPK JECA</p>

    <h2 class="text-xl font-semibold mb-3">Persyaratan Umum</h2>

    <ul class="space-y-2 text-indigo-50 text-sm leading-relaxed">
      <li>• Minimal lulusan SMA/SMK sederajat</li>
      <li>• Usia 18 s/d 30 tahun</li>
      <li>• Sehat jasmani & rohani</li>
      <li>• Tinggi badan ≥ 160 cm (pria) & 150 cm (wanita)</li>
      <li>• Berat badan proporsional</li>
      <li>• Sikap & mental baik</li>
      <li>• Tidak terikat dengan LPK lain</li>
      <li>• Tidak berstatus pelajar aktif</li>
      <li>• Tidak berstatus karyawan aktif</li>
      <li>• Mendapat izin orang tua</li>
      <li>• Belum pernah ikut program magang Jepang sebelumnya</li>
    </ul>
  </div>
</div>


    <!-- PANEL KANAN – FORM REGISTER -->
    <div class="w-full lg:w-1/2 p-12 flex items-center justify-center">

        <div class="w-full max-w-md">

            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Buat Akun SiPentol</h2>
            <p class="text-center text-gray-500 mb-8">Daftarkan diri Anda untuk mengikuti seleksi JECA.</p>

            <?= $this->Form->create($user, [
                'class' => 'space-y-4',
                'id'    => 'registerForm'
            ]) ?>

                <!-- NAMA -->
                <?= $this->Form->control('nama_lengkap', [
                    'label' => 'Nama Lengkap',
                    'class' => 'w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5',
                    'placeholder' => 'Nama Lengkap'
                ]) ?>

                <!-- EMAIL -->
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'type'  => 'email',
                    'class' => 'w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5',
                    'placeholder' => 'Email Anda'
                ]) ?>

                <!-- PASSWORD -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <?= $this->Form->password('password', [
                            'id' => 'password',
                            'class' => 'w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 pr-12',
                            'placeholder' => 'Password'
                        ]) ?>

                        <button type="button" onclick="togglePassword('password', 'eye1')"
                                class="absolute inset-y-0 right-3 flex items-center">
                            <svg id="eye1" class="h-5 w-5 text-gray-500" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- KONFIRMASI PASSWORD -->
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password"
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 pr-12"
                               placeholder="Konfirmasi Password">

                        <button type="button" onclick="togglePassword('confirm_password', 'eye2')"
                                class="absolute inset-y-0 right-3 flex items-center">
                            <svg id="eye2" class="h-5 w-5 text-gray-500" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- TERMS -->
                <div class="flex items-start space-x-3">
              <label class="flex items-start gap-3 text-sm text-gray-700 select-none">
    <input
      id="agree"
      type="checkbox"
      name="agree_terms"
      value="1"
      class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
      required
    >
    <span>
      Saya menyetujui
      <a href="#" onclick="openTerms(event)" class="text-indigo-600 hover:underline font-semibold">Syarat & Ketentuan</a>
    </span>
  </label>

                </div>

                <!-- BUTTON DAFTAR -->
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition">
                    Daftar Sekarang
                </button>

                <p class="text-center mt-4 text-sm">
                    Sudah punya akun?
                    <a href="/users/login" class="text-indigo-600 font-medium">Login di sini</a>
                </p>

            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
 <p id="agreeWarn" class="mt-2 text-sm text-red-600 hidden">
    Anda wajib menyetujui Syarat & Ketentuan untuk mendaftar.
  </p>

  <?php if (!empty($agreeError)): ?>
    <p class="mt-2 text-sm text-red-600">
      <?= h($agreeError) ?>
    </p>
  <?php endif; ?>
</div>

    <!-- MODAL TERMS -->
    <div id="termsModal"
        class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4">

        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-3xl max-h-[85vh] overflow-y-auto">

            <h2 class="text-xl font-bold text-center text-indigo-700 mb-4">
                Syarat & Ketentuan Pendaftaran Akun — PT Giken Kaizen Educenter
            </h2>

            <div class="text-gray-700 text-sm leading-relaxed space-y-4">

                <p>Selamat datang di PT Giken Kaizen Educenter! Sebelum melanjutkan proses pendaftaran akun untuk menjadi peserta pelatihan, harap membaca dengan seksama Syarat dan Ketentuan berikut yang mengatur penggunaan akun dan layanan kami. Dengan melanjutkan pendaftaran, Anda setuju untuk mematuhi syarat dan ketentuan ini.</p>

                <h3 class="font-semibold text-indigo-700">1. Definisi</h3>
                <ul class="list-disc ml-6">
                    <li><strong>Siswa:</strong> Individu yang melakukan pendaftaran untuk mengikuti program pelatihan di PT Giken Kaizen Educenter.</li>
                    <li><strong>Layanan:</strong> Semua program dan kegiatan pelatihan yang disediakan.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">2. Persyaratan Pendaftaran</h3>
                <ul class="list-disc ml-6">
                    <li>Usia minimal 18 tahun.</li>
                    <li>Informasi yang diberikan harus akurat dan lengkap.</li>
                    <li>Memiliki identitas resmi yang valid.</li>
                    <li>Wajib menyetujui kebijakan privasi.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">3. Akun Pengguna</h3>
                <ul class="list-disc ml-6">
                    <li>Harus membuat username dan password yang aman.</li>
                    <li>Bertanggung jawab atas kerahasiaan akun.</li>
                    <li>Dilarang membagikan akun kepada pihak lain.</li>
                    <li>Segera laporkan penyalahgunaan akun.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">4. Kewajiban Pengguna</h3>
                <ul class="list-disc ml-6">
                    <li>Dilarang menggunakan layanan untuk tujuan ilegal.</li>
                    <li>Dilarang menyebarkan informasi yang merugikan atau menipu.</li>
                    <li>Bertanggung jawab atas semua data yang diunggah.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">5. Pembayaran dan Biaya</h3>
                <ul class="list-disc ml-6">
                    <li>Biaya (jika ada) diinformasikan secara jelas.</li>
                    <li>Pembayaran bersifat final kecuali terjadi kesalahan sistem.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">6. Penggunaan Data Pribadi</h3>
                <ul class="list-disc ml-6">
                    <li>Data hanya digunakan untuk administrasi internal.</li>
                    <li>Kami menjaga kerahasiaan data Anda sesuai kebijakan privasi.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">7. Pembatalan dan Penghentian Akun</h3>
                <ul class="list-disc ml-6">
                    <li>Akun dapat ditangguhkan jika terjadi pelanggaran.</li>
                    <li>Pengguna dapat mengajukan penghapusan akun.</li>
                </ul>

                <h3 class="font-semibold text-indigo-700">8. Perubahan Ketentuan</h3>
                <p>PT Giken Kaizen Educenter berhak mengubah ketentuan sewaktu-waktu.</p>

                <h3 class="font-semibold text-indigo-700">9. Pembatasan Tanggung Jawab</h3>
                <p>Kami tidak bertanggung jawab atas kerugian akibat gangguan sistem atau penggunaan layanan.</p>

                <h3 class="font-semibold text-indigo-700">10. Hukum yang Berlaku</h3>
                <p>Ketentuan ini tunduk pada hukum Republik Indonesia.</p>

                <p class="mt-4 text-center font-medium">Dengan mendaftar, Anda menyatakan telah membaca dan memahami seluruh ketentuan di atas.</p>

            </div>

            <div class="text-right mt-6">
                <button onclick="closeTerms()" class="bg-gray-300 px-4 py-2 rounded">Tutup</button>
                <button onclick="acceptTerms()" class="bg-indigo-600 text-white px-4 py-2 rounded ml-2">Setuju</button>
            </div>
        </div>
    </div>


    <script>
       function togglePassword(id, iconId) {
    const field = document.getElementById(id);
    const icon = document.getElementById(iconId);

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
        function openTerms(event) {
            event.preventDefault();
            const modal = document.getElementById("termsModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }

        function closeTerms() {
            const modal = document.getElementById("termsModal");
            modal.classList.add("hidden");
        }

        function acceptTerms() {
            document.getElementById("agree").checked = true;
            closeTerms();
        }

        (function () {
  const form = document.getElementById('registerForm');
  const agree = document.getElementById('agree');
  const warn = document.getElementById('agreeWarn');

  if (!form || !agree || !warn) return;

  function showWarn(show) {
    warn.classList.toggle('hidden', !show);
    agree.classList.toggle('ring-2', show);
    agree.classList.toggle('ring-red-500', show);
  }

  form.addEventListener('submit', function (e) {
    if (!agree.checked) {
      e.preventDefault();
      showWarn(true);
      agree.focus();
    }
  });

  agree.addEventListener('change', function () {
    if (agree.checked) showWarn(false);
  });
})();
    </script>

</body>

</html>
