<?php
/* includes/header.php — საიტის თავსართი + ნავიგაცია + ფილტრის ღილაკი */
$bu = base_url();
?>
<header class="site-header" id="header">
  <a href="<?= e($bu) ?>/index.php" class="brand">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M2 7h20M2 17h20M7 2v20M17 2v20" stroke-linecap="round" />
    </svg>
    CINEM<span class="brand-dot">A</span>TA
  </a>

  <nav class="nav-menu" id="navMenu">
    <a href="<?= e($bu) ?>/index.php" class="is-active">მთავარი</a>
    <a href="<?= e($bu) ?>/filter.php">ფილმები</a>
    <a href="<?= e($bu) ?>/index.php#collections">კოლექციები</a>
    <a href="<?= e($bu) ?>/index.php#recent">ბოლოს ნანახი</a>
    <a href="<?= e($bu) ?>/scroll-movie.php" class="nav-reels">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="18" height="18" rx="3" />
        <path d="M9 3v18M3 9h6M3 15h6" />
      </svg>
      Scroll Movie
    </a>
  </nav>

  <div class="header-right">
    <!-- ფილტრი — ცალკე გვერდი, იხსნება მხოლოდ ხელით -->
    <a href="<?= e($bu) ?>/filter.php" class="header-filter" aria-label="ფილტრი">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 5h18M6 12h12M10 19h4" stroke-linecap="round" />
      </svg>
      <span>ფილტრი</span>
    </a>
    <button class="hamburger" id="hamburger" aria-label="მენიუ" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
<div class="nav-backdrop" id="navBackdrop"></div>
