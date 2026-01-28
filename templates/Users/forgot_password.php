<div class="min-h-[70vh] flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-center mb-2">Lupa Password</h2>
    <p class="text-center text-gray-600 mb-6">
      Masukkan email akun Anda. Kami akan kirim link untuk reset password.
    </p>

    <?= $this->Form->create(null, ['class' => 'space-y-4']) ?>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input
          type="email"
          name="email"
          class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Email Anda"
          required
        >
      </div>

      <button class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 shadow">
        Kirim Link Reset
      </button>

      <p class="text-center text-sm text-gray-600">
        <a href="<?= $this->Url->build(['action' => 'login']) ?>" class="text-indigo-600 font-semibold hover:underline">
          Kembali ke Login
        </a>
      </p>
      <?= $this->Flash->render() ?>
    <?= $this->Form->end() ?>
  </div>
</div>
