<?php
/* admin/includes/admin_header.php — საერთო ადმინ ლეიაუტი (მოითხოვს ავტორიზაციას) */
require_once dirname(__DIR__, 2) . '/config.php';
require_admin();

$bu = base_url();
$adminBase = $bu . '/admin';
$current = basename($_SERVER['SCRIPT_NAME']);
$settings = get_settings();

function nav_item(string $href, string $file, string $label, string $icon, string $current, string $adminBase): string
{
    $active = $current === $file ? ' is-active' : '';
    return '<a class="anav-item' . $active . '" href="' . e($adminBase . '/' . $href) . '">' . $icon . '<span>' . e($label) . '</span></a>';
}
$ic = [
    'dash'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>',
    'film'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/></svg>',
    'cat'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"/></svg>',
    'reel'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 3v18M3 9h6M3 15h6"/></svg>',
    'set'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 6 19.4l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4.6 14H4a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 6 6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 12 4.6V4a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 2.9 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>',
    'out'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 17l5-5-5-5M21 12H9M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>',
    'site'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>',
];
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ადმინ პანელი · <?= e($settings['site_title']) ?></title>
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/admin.css" />
</head>
<body class="admin">
  <aside class="admin-side" id="adminSide">
    <a class="admin-brand" href="<?= e($adminBase) ?>/index.php">
      CINEM<span>A</span>TA <small>admin</small>
    </a>
    <nav class="admin-nav">
      <?= nav_item('index.php', 'index.php', 'მთავარი', $ic['dash'], $current, $adminBase) ?>
      <?= nav_item('movies.php', 'movies.php', 'ფილმები', $ic['film'], $current, $adminBase) ?>
      <?= nav_item('movie-edit.php', 'movie-edit.php', 'ფილმის დამატება', $ic['film'], $current, $adminBase) ?>
      <?= nav_item('categories.php', 'categories.php', 'კატეგორიები', $ic['cat'], $current, $adminBase) ?>
      <?= nav_item('scroll.php', 'scroll.php', 'Scroll Movie', $ic['reel'], $current, $adminBase) ?>
      <?= nav_item('settings.php', 'settings.php', 'პარამეტრები', $ic['set'], $current, $adminBase) ?>
    </nav>
    <div class="admin-nav-bottom">
      <a class="anav-item" href="<?= e($bu) ?>/index.php" target="_blank"><?= $ic['site'] ?><span>საიტის ნახვა</span></a>
      <a class="anav-item" href="<?= e($adminBase) ?>/logout.php"><?= $ic['out'] ?><span>გასვლა</span></a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-top">
      <button class="admin-burger" id="adminBurger" aria-label="მენიუ">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
      </button>
      <h1 class="admin-title"><?= e($pageTitle ?? 'ადმინ პანელი') ?></h1>
      <span class="admin-user"><?= e($settings['admin_user']) ?></span>
    </header>
    <main class="admin-content">
      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash <?= e($_SESSION['flash']['type'] ?? 'ok') ?>"><?= e($_SESSION['flash']['msg']) ?></div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>
