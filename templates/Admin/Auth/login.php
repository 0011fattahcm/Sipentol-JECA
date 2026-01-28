<?php
$this->assign('title', 'Admin Login');
?>
<div class="min-h-[70vh] flex items-center justify-center">
  <div class="w-full max-w-md rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl p-6">
    <h1 class="text-2xl font-bold">Admin Login</h1>
    <p class="text-slate-300 mt-2">Masuk untuk mengelola pengumuman dan jadwal tes.</p>

    <?= $this->Form->create(null) ?>
      <div class="mt-5">
        <label class="text-sm font-semibold text-slate-200">Username</label>
        <input name="username" required class="mt-1 w-full rounded-xl bg-white/10 ring-1 ring-white/10 px-4 py-3 text-white outline-none">
      </div>
      <div class="mt-4">
        <label class="text-sm font-semibold text-slate-200">Password</label>
        <input type="password" name="password" required class="mt-1 w-full rounded-xl bg-white/10 ring-1 ring-white/10 px-4 py-3 text-white outline-none">
      </div>
      <button class="mt-6 w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-3 font-semibold">
        Login
      </button>
    <?= $this->Form->end() ?>
  </div>
</div>
