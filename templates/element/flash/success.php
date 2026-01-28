<?php
/** @var \App\View\AppView $this */
/** @var string $message */
?>
<div
  data-flash
  data-flash-type="success"
  data-autodismiss="1"
  class="mb-4 transition-all duration-200 transform"
>
  <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white shadow-lg shadow-emerald-500/10">
    <div class="absolute inset-y-0 left-0 w-1.5 bg-emerald-500"></div>

    <div class="flex gap-3 p-4 pl-5">
      <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
        <!-- check icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </div>

      <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-emerald-900">Berhasil</p>
        <div class="mt-0.5 text-sm text-emerald-800">
          <?= h($message) ?>
        </div>
      </div>

      <button
        type="button"
        data-flash-close
        class="ml-2 inline-flex h-9 w-9 items-center justify-center rounded-xl text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400"
        aria-label="Tutup notifikasi"
      >
        <!-- x icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>
</div>
