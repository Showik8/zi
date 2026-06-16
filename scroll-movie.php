<?php
/* =========================================================================
   scroll-movie.php — TikTok-style reels, მონაცემები JSON ბაზიდან
   ========================================================================= */
require_once __DIR__ . '/config.php';

$bu = base_url();
$videos = get_scroll();
$settings = get_settings();

$STAR = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5.9 21.4l1.4-6.8L2.2 9.9l6.9-.8L12 2z"/></svg>';
$PLAY = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>';
$HEART = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.4-9C1 9 2.5 5.5 6 5.5c2 0 3.2 1.2 4 2.3.8-1.1 2-2.3 4-2.3 3.5 0 5 3.5 3.4 6.5C19 16.4 12 21 12 21z"/></svg>';
$COMMENT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a8 8 0 0 1-11.5 7.2L3 21l1.8-6.5A8 8 0 1 1 21 12z"/></svg>';
$SHARE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v14"/></svg>';
$VOLOFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M22 9l-6 6M16 9l6 6"/></svg>';
$CHEVRON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Scroll Movie · <?= e($settings['site_title']) ?></title>
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/styles.css" />
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/scroll-movie.css" />
</head>
<body class="reels-page">
  <div class="reels-top">
    <a href="<?= e($bu) ?>/index.php" class="reels-back" aria-label="უკან">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" /></svg>
    </a>
    <div class="reels-title"><span class="dot-live"></span>Scroll Movie</div>
  </div>

  <main class="reels" id="reels">
    <?php if (!$videos): ?>
      <section class="reel" style="color:#fff;text-align:center;padding:40px">ჯერ არცერთი ვიდეო არ არის დამატებული.</section>
    <?php endif; ?>
    <?php foreach ($videos as $i => $v): ?>
      <?php
        $src = !empty($v['video_file']) ? media_url($v['video_file']) : ($v['video_url'] ?? '');
        $poster = media_url($v['poster'] ?? '') ?: ('https://picsum.photos/seed/v-' . urlencode($v['title'] ?? 'x') . '/720/1280');
        $rating = number_format((float)($v['rating'] ?? 0), 1);
      ?>
      <section class="reel" data-index="<?= $i ?>">
        <video class="reel-blur" muted loop playsinline preload="none" poster="<?= e($poster) ?>" src="<?= e($src) ?>"></video>
        <video class="reel-video" loop playsinline preload="none" muted poster="<?= e($poster) ?>" src="<?= e($src) ?>"></video>
        <div class="reel-scrim"></div>
        <button class="reel-tap" aria-label="დაპაუზება / გაშვება"></button>
        <div class="reel-pausebadge"><?= $PLAY ?></div>

        <div class="reel-info">
          <div class="badges">
            <span class="reel-rating"><?= $STAR ?><?= $rating ?></span>
            <span class="chip"><?= e($v['year'] ?? '') ?></span>
            <span class="chip"><?= e($v['genre'] ?? '') ?></span>
          </div>
          <h2><?= e($v['title'] ?? '') ?></h2>
          <p><?= e($v['description'] ?? '') ?></p>
          <a class="watch" href="<?= e($bu) ?>/filter.php"><?= $PLAY ?> სრულად ნახვა</a>
        </div>

        <div class="reel-rail">
          <button class="rail-btn like" aria-label="მოწონება"><span class="ico"><?= $HEART ?></span><span class="lbl"><?= 12 + $i * 3 ?>K</span></button>
          <button class="rail-btn" aria-label="კომენტარი"><span class="ico"><?= $COMMENT ?></span><span class="lbl"><?= 1 + $i ?>.2K</span></button>
          <button class="rail-btn" aria-label="გაზიარება"><span class="ico"><?= $SHARE ?></span><span class="lbl">გაზიარება</span></button>
          <button class="rail-btn mute" aria-label="ხმა"><span class="ico"><?= $VOLOFF ?></span><span class="lbl">ხმა</span></button>
        </div>

        <div class="reel-progress"><span></span></div>
        <?php if ($i === 0): ?><div class="scroll-hint">დაასქროლე <?= $CHEVRON ?></div><?php endif; ?>
      </section>
    <?php endforeach; ?>
  </main>

  <script src="<?= e($bu) ?>/assets/js/scroll-movie.js"></script>
</body>
</html>
