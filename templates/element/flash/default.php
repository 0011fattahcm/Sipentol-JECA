<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="<?= h($class) ?>" onclick="this.classList.add('hidden');"><?= $message ?></div>
<script>
document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-flash-close]');
  if (!btn) return;
  const wrap = btn.closest('[data-flash]');
  if (!wrap) return;
  wrap.classList.add('opacity-0', '-translate-y-1');
  setTimeout(() => wrap.remove(), 180);
});

// optional: auto dismiss success (3.5s)
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-flash][data-flash-type="success"][data-autodismiss="1"]').forEach((el) => {
    setTimeout(() => {
      el.classList.add('opacity-0', '-translate-y-1');
      setTimeout(() => el.remove(), 180);
    }, 3500);
  });
});
</script>
