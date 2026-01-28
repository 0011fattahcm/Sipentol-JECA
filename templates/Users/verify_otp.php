<div class="flex justify-center items-center min-h-[70vh]">

    <div class="bg-white shadow-xl rounded-xl p-10 w-full max-w-md">

        <h2 class="text-2xl font-bold text-center mb-6">Verifikasi OTP</h2>

        <p class="text-center text-gray-600 mb-4">
            Kode OTP telah dikirim ke <strong><?= h($user->email) ?></strong> Silakan cek inbox atau spam Anda<br>
            Masukkan kode 6 digit untuk mengaktifkan akun Anda.
        </p>

        <?= $this->Form->create(null, ['class' => 'space-y-4']) ?>

        <div>
            <label class="block text-gray-700 font-medium mb-1">Kode OTP</label>
            <input type="text" name="otp_code" maxlength="6"
                class="border rounded-lg w-full p-3 text-center tracking-widest text-xl font-bold"
                placeholder="123456" required>
        </div>

        <button
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold shadow">
            Verifikasi Sekarang
        </button>

        <?= $this->Form->end() ?>

       <?php if ($user->otp_resend_count < 3): ?>
    <p class="text-center text-sm text-gray-500 mt-4">
        Tidak menerima kode?
        <a href="<?= $this->Url->build(['action' => 'resendOtp', $user->id]) ?>"
           class="text-indigo-600 font-semibold hover:underline">
            Kirim ulang OTP (<?= $user->otp_resend_count ?>/3)
        </a>
    </p>
<?php else: ?>
    <p class="text-center text-sm text-red-500 mt-4">
        Anda sudah mencapai batas maksimal pengiriman ulang OTP (3 kali).
    </p>
<?php endif; ?>



    </div>

</div>
