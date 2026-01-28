<?php
/** @var \App\View\AppView $this */
$adminName = $adminName ?? 'Admin JECA';
$title = $this->fetch('title') ?: 'Admin JECA';
$currCtrl = (string)$this->request->getParam('controller');

function iconSvg($name) {
  if ($name === 'home') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-10.5z"/></svg>';
  if ($name === 'users') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
  if ($name === 'file') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>';
  if ($name === 'clock') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/></svg>';
  if ($name === 'upload') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5-5 5 5"/><path d="M12 5v14"/></svg>';
  if ($name === 'link') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"/><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"/></svg>';
  if ($name === 'activity') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9-4-18-3 9H2"/></svg>';

  // Pengumuman
  if ($name === 'megaphone') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11v2a2 2 0 0 0 2 2h2l5 4V5L7 9H5a2 2 0 0 0-2 2z"/><path d="M14 9.5c2.5 0 4.5-1.8 7-3.5v14c-2.5-1.7-4.5-3.5-7-3.5"/></svg>';

  // Mailing
  if ($name === 'mail') return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="m22 6-10 7L2 6"/></svg>';

  // Logout
  return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>';
}
?>
<!doctype html>
<html lang="id">
<head>
  <?= $this->Html->charset() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title) ?></title>
  <?= $this->Html->css('app') ?>
  <style>
    .admin-ambient {
      background:
        radial-gradient(900px 520px at 8% 12%, rgba(99,102,241,.20), transparent 60%),
        radial-gradient(720px 480px at 92% 18%, rgba(236,72,153,.18), transparent 55%),
        radial-gradient(820px 520px at 60% 96%, rgba(16,185,129,.14), transparent 55%),
        linear-gradient(180deg, #f6f7fb 0%, #eef2ff 45%, #f8fafc 100%);
    }
    .glass {
      background: rgba(255,255,255,.72);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid rgba(148,163,184,.28);
      box-shadow: 0 16px 46px rgba(15,23,42,.10);
      border-radius: 24px;
    }
    .glass-soft {
      background: rgba(255,255,255,.60);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(148,163,184,.25);
      box-shadow: 0 10px 30px rgba(15,23,42,.08);
      border-radius: 18px;
    }

    /* ===== Instagram-like sidebar expand on hover (rapih + row-hover) ===== */
/* ===== IG Sidebar (Collapsed -> Expanded on hover) ===== */
.admin-sidebar{
  width: 5.25rem;              /* 84px */
  transition: width .18s ease;
  will-change: width;
  position: sticky;
  top: 24px;
  z-index: 60;
}
.admin-sidebar:hover{ width: 17rem; /* 272px */ }

/* container */
.admin-nav{ display:flex; flex-direction:column; gap:.5rem; }

/* ===== BASE item ===== */
.admin-nav .nav-link{
  width:100%;
  display:flex;
  align-items:center;
  border-radius:18px;
  text-decoration:none;
  transition: background .15s ease, box-shadow .15s ease;
}

/* icon (selalu ukuran sama, biar align konsisten) */
.admin-nav .nav-icon{
  width:52px;
  height:46px;
  border-radius:18px;
  display:flex;
  align-items:center;
  justify-content:center;
  flex:0 0 auto;
}

/* label */
.admin-nav .nav-label{
  font-weight:800;
  font-size:14px;
  white-space:nowrap;
}

/* ===== COLLAPSED MODE (default, sebelum hover) ===== */
.admin-sidebar:not(:hover) .nav-link{
  justify-content:center;
  padding:8px;                 /* rapih */
}
.admin-sidebar:not(:hover) .nav-label{
  display:none;
}
.admin-sidebar:not(:hover) .nav-icon{
  background: rgba(255,255,255,.70);
  color: rgba(15,23,42,.80);
  border: 1px solid rgba(148,163,184,.45);
}
.admin-sidebar:not(:hover) .nav-link:hover{
  background: rgba(255,255,255,.75);
  box-shadow:none;
}

/* active collapsed */
.admin-sidebar:not(:hover) .nav-link.is-active{
  background: rgba(15,23,42,.92);
}
.admin-sidebar:not(:hover) .nav-link.is-active .nav-icon{
  background: transparent;
  color:#fff;
  border: 1px solid rgba(255,255,255,.12);
}

/* ===== EXPANDED MODE (hover sidebar) ===== */
.admin-sidebar:hover .nav-link{
  justify-content:flex-start;
  gap:12px;
  padding:10px;                /* konsisten */
}
.admin-sidebar:hover .nav-icon{
  background: rgba(255,255,255,.70);
  color: rgba(15,23,42,.85);
  border: 1px solid rgba(148,163,184,.45);
}
.admin-sidebar:hover .nav-label{
  display:block;
  color:#0f172a;
}

/* hover expanded */
.admin-sidebar:hover .nav-link:hover{
  background: rgba(255,255,255,.88);
  box-shadow: 0 10px 24px rgba(15,23,42,.08);
}
.admin-sidebar:hover .nav-link:hover .nav-icon{
  background:#fff;
  color: rgba(15,23,42,.92);
}

/* active expanded: row rapi, icon tidak “double box” */
.admin-sidebar:hover .nav-link.is-active{
  background: rgba(15,23,42,.92);
}
.admin-sidebar:hover .nav-link.is-active .nav-label{
  color:#fff;
}
.admin-sidebar:hover .nav-link.is-active .nav-icon{
  background: transparent;
  color:#fff;
  border: 1px solid rgba(255,255,255,.12);
}

/* divider */
.admin-divider{
  height:1px;
  background: rgba(148,163,184,.35);
  margin: .6rem .25rem;
  border-radius: 999px;
}

  </style>
</head>

<body class="min-h-screen admin-ambient text-slate-900">
  <div class="w-full px-3 sm:px-4 lg:px-6 py-6">

    <!-- Topbar -->
    <header class="glass px-5 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold">
          J
        </div>
        <div>
          <div class="font-extrabold leading-tight">Admin JECA</div>
          <div class="text-xs text-slate-500"><?= h($adminName) ?></div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <form
          class="hidden sm:flex items-center gap-2 glass-soft px-3 py-2"
          method="get"
          action="<?= $this->Url->build(['prefix'=>'Admin','controller'=>'Users','action'=>'index']) ?>"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" class="text-slate-500" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M21 21l-4.3-4.3"></path>
          </svg>

          <input
            type="text"
            name="q"
            placeholder="Search (nama/email)"
            class="w-56 bg-transparent outline-none text-sm text-slate-800 placeholder:text-slate-400"
            autocomplete="off"
          />
        </form>

        <div class="w-10 h-10 rounded-full glass-soft flex items-center justify-center font-bold text-slate-800">
          A
        </div>
      </div>
    </header>

    <div class="mt-6 flex gap-5">

      <!-- Sidebar -->
      <aside class="shrink-0">
        <div class="glass p-3 admin-sidebar overflow-visible">
          <?php
            $items = [
              ['ctrl'=>'Dashboard',    'label'=>'Dashboard',         'icon'=>'home',      'url'=>['prefix'=>'Admin','controller'=>'Dashboard','action'=>'index']],
              ['ctrl'=>'Users',        'label'=>'User',              'icon'=>'users',     'url'=>['prefix'=>'Admin','controller'=>'Users','action'=>'index']],
              ['ctrl'=>'Pendaftarans', 'label'=>'Pendaftaran',       'icon'=>'file',      'url'=>['prefix'=>'Admin','controller'=>'Pendaftarans','action'=>'index']],
              ['ctrl'=>'OnlineTests',  'label'=>'Tes Online',        'icon'=>'clock',     'url'=>['prefix'=>'Admin','controller'=>'OnlineTests','action'=>'index']],
              ['ctrl'=>'DaftarUlangs', 'label'=>'Daftar Ulang',      'icon'=>'upload',    'url'=>['prefix'=>'Admin','controller'=>'DaftarUlangs','action'=>'index']],
              ['ctrl'=>'Settings',     'label'=>'Onboarding',        'icon'=>'link',      'url'=>['prefix'=>'Admin','controller'=>'Settings','action'=>'onboarding']],
              ['ctrl'=>'Pengumuman',   'label'=>'Pengumuman',        'icon'=>'megaphone', 'url'=>['prefix'=>'Admin','controller'=>'Pengumuman','action'=>'index']],
              ['ctrl'=>'Mailing',      'label'=>'Mailing',           'icon'=>'mail',      'url'=>['prefix'=>'Admin','controller'=>'Mailing','action'=>'index']],
              ['ctrl'=>'ActivityLogs', 'label'=>'Activity Log User', 'icon'=>'activity',  'url'=>['prefix'=>'Admin','controller'=>'ActivityLogs','action'=>'index']],
            ];
          ?>

          <div class="admin-nav">
            <?php foreach ($items as $it): ?>
              <?php $isActive = (strtolower($it['ctrl']) === strtolower($currCtrl)); ?>
              <a
                href="<?= $this->Url->build($it['url']) ?>"
                class="nav-link <?= $isActive ? 'is-active' : '' ?>"
              >
                <span class="nav-icon">
                  <?= iconSvg($it['icon']) ?>
                </span>
                <span class="nav-label"><?= h($it['label']) ?></span>
              </a>
            <?php endforeach; ?>

            <div class="admin-divider"></div>

            <?= $this->Form->postLink(
              '<span class="nav-link">
                 <span class="nav-icon" style="border:1px solid rgba(148,163,184,.45); background:rgba(255,255,255,.70); color:rgba(15,23,42,.80);">
                   ' . iconSvg('logout') . '
                 </span>
                 <span class="nav-label">Logout</span>
               </span>',
              ['prefix'=>'Admin','controller'=>'Auth','action'=>'logout'],
              ['escapeTitle' => false]
            ) ?>
          </div>
        </div>
      </aside>

      <!-- Content -->
      <main class="flex-1 min-w-0 relative z-0">
        <?= $this->Flash->render() ?>
        <div class="glass p-6">
          <?= $this->fetch('content') ?>
        </div>
      </main>

    </div>
  </div>
</body>
</html>
