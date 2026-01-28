<?php
/** @var \App\View\AppView $this */
$title = $this->fetch('title') ?: 'Admin';
?>
<!doctype html>
<html lang="id">
<head>
  <?= $this->Html->charset() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title) ?></title>
  <?= $this->Html->css('app') ?>
</head>
<body class="min-h-screen bg-slate-950 text-white">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <?= $this->Flash->render() ?>
      <?= $this->fetch('content') ?>
    </div>
  </div>
</body>
</html>
