<?php
/** @var \App\View\AppView $this */
/** @var string $message */
?>
<div data-flash data-flash-type="error" class="mb-4 transition-all duration-200 transform">
  <div class="relative overflow-hidden rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-white shadow-lg shadow-rose-500/10">
    <div class="absolute inset-y-0 left-0 w-1.5 bg-rose-500"></div>

    <div class="flex gap-3 p-4 pl-5">
      <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
        <!-- alert icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.53 20h14.94a2 2 0 001.74-3.14l-7.5-13a2 2 0 00-3.42 0z"/>
        </svg>
      </div>

      <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-rose-900">Terjadi Kesalahan</p>
        <div class="mt-0.5 text-sm text-rose-800">
          <?= h($message) ?>
        </div>
      </div>

      <button
        type="button"
        data-flash-close
        class="ml-2 inline-flex h-9 w-9 items-center justify-center rounded-xl text-rose-700 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-400"
        aria-label="Tutup notifikasi"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>
</div>
