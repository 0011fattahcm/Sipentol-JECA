<div class="min-h-[70vh] flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-center mb-2">Reset Password</h2>
    <p class="text-center text-gray-600 mb-6">
      Buat password baru untuk akun Anda.
    </p>

    <?= $this->Form->create(null, ['class' => 'space-y-4']) ?>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Password Baru</label>
        <input
          type="password"
          name="password"
          class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Minimal 6 karakter"
          required
        >
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Konfirmasi Password</label>
        <input
          type="password"
          name="confirm_password"
          class="w-full rounded-xl border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Ulangi password"
          required
        >
      </div>

      <button class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 shadow">
        Simpan Password Baru
      </button>
      <?= $this->Flash->render() ?>
    <?= $this->Form->end() ?>
  </div>
</div>
