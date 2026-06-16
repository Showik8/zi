<?php
/* =========================================================================
   index.php — მთავარი გვერდი. ყველა ნაწილი კომპონენტებად ჩაშენებულია.
   ჰოსტინგზე ატვირთვისთანავე ეს ფაილი ტვირთავს მთელ საიტს.
   ========================================================================= */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/render.php';

$settings = get_settings();
$movies   = get_movies();

/* --- მონაცემების დაჯგუფება კომპონენტებისთვის --- */
$featured = array_values(array_filter($movies, fn($m) => !empty($m['featured'])));
if (!$featured) {
    $featured = array_slice($movies, 0, 5);
}
$trending = array_values(array_filter($movies, fn($m) => !empty($m['trending'])));
if (!$trending) {
    $trending = array_slice($movies, 0, 12);
}
$recent = array_values(array_filter($movies, fn($m) => !empty($m['recent'])));
usort($recent, fn($a, $b) => ($b['progress'] ?? 0) <=> ($a['progress'] ?? 0));

$collectionsCat = get_categories()['collections'] ?? [];
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($settings['site_title']) ?> · ფილმები და სერიალები</title>
  <link rel="stylesheet" href="<?= e(base_url()) ?>/assets/css/styles.css" />
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>
  <?php include __DIR__ . '/includes/hero.php'; ?>

  <main>
    <?php include __DIR__ . '/includes/collections.php'; ?>
    <?php include __DIR__ . '/includes/recent.php'; ?>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="<?= e(base_url()) ?>/assets/js/card.js"></script>
  <script src="<?= e(base_url()) ?>/assets/js/main.js"></script>
</body>
</html>
