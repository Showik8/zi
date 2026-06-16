<?php
/* =========================================================================
   movie.php — ფილმის დეტალური გვერდი + დაკვრა (url ან ატვირთული mp4)
   ========================================================================= */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/render.php';

$id = $_GET['id'] ?? '';
$m = get_movie($id);
$bu = base_url();
$settings = get_settings();
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $m ? e($m['title']) : 'ვერ მოიძებნა' ?> · <?= e($settings['site_title']) ?></title>
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/styles.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <?php if (!$m): ?>
    <main class="container" style="padding:140px 0 80px;text-align:center">
      <h2>ფილმი ვერ მოიძებნა</h2>
      <p class="empty-note"><a class="btn-ghost" href="<?= e($bu) ?>/filter.php">დაბრუნდი კატალოგში</a></p>
    </main>
  <?php else: ?>
    <?php
      $src = !empty($m['video_file']) ? media_url($m['video_file']) : ($m['video_url'] ?? '');
      $g = cat_label_map('genres');
      $gn = array_map(fn($k) => $g[$k] ?? $k, $m['genres'] ?? []);
      $countries = cat_label_map('countries');
      $dub = cat_label_map('dubbing');
      $dn = array_map(fn($k) => $dub[$k] ?? $k, $m['dubbing'] ?? []);
      $typeMap = cat_label_map('types');
    ?>
    <main class="movie-detail">
      <div class="detail-backdrop" style="background-image:url('<?= e(media_url($m['backdrop'] ?? $m['poster'] ?? '')) ?>')"></div>
      <div class="container detail-inner">
        <div class="detail-player">
          <?php if ($src): ?>
            <video controls playsinline preload="metadata" poster="<?= e(media_url($m['poster'] ?? '')) ?>" src="<?= e($src) ?>"></video>
          <?php else: ?>
            <div class="detail-novideo">ვიდეო ხელმისაწვდომი არ არის</div>
          <?php endif; ?>
        </div>
        <div class="detail-info">
          <h1><?= e($m['title']) ?></h1>
          <div class="detail-meta">
            <span class="hero-rating"><?= ICON_STAR ?><?= number_format((float)($m['rating'] ?? 0), 1) ?></span>
            <span class="pill"><?= e($m['year'] ?? '') ?></span>
            <?php if (!empty($m['duration'])): ?><span class="pill"><?= (int)$m['duration'] ?> წთ</span><?php endif; ?>
            <span class="pill"><?= e($typeMap[$m['type'] ?? ''] ?? '') ?></span>
            <?php if (!empty($m['country'])): ?><span class="pill"><?= e($countries[$m['country']] ?? $m['country']) ?></span><?php endif; ?>
          </div>
          <?php if ($gn): ?><p class="detail-genres"><?= e(implode(' · ', $gn)) ?></p><?php endif; ?>
          <p class="detail-desc"><?= e($m['description'] ?? '') ?></p>
          <ul class="detail-facts">
            <?php if (!empty($m['director'])): ?><li><b>რეჟისორი:</b> <?= e($m['director']) ?></li><?php endif; ?>
            <?php if (!empty($m['cast'])): ?><li><b>მსახიობები:</b> <?= e(implode(', ', (array)$m['cast'])) ?></li><?php endif; ?>
            <?php if ($dn): ?><li><b>გახმოვანება:</b> <?= e(implode(', ', $dn)) ?></li><?php endif; ?>
          </ul>
        </div>
      </div>
    </main>
  <?php endif; ?>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <script src="<?= e($bu) ?>/assets/js/main.js"></script>
</body>
</html>
