<?php
/* =========================================================================
   filter.php — ცალკე გვერდი: ფილტრი + ძიება + infinite scroll.
   იხსნება მხოლოდ ხელით (ჰედერის "ფილტრი" ღილაკით). მთელ ეკრანს იკავებს —
   დანარჩენი საიტი "იხურება". × ღილაკი აბრუნებს მთავარ გვერდზე.
   ========================================================================= */
require_once __DIR__ . '/config.php';

$bu = base_url();
$cats = get_categories();
$movies = get_movies();
$settings = get_settings();

/* წლების დიაპაზონი სლაიდერისთვის */
$years = array_map(fn($m) => (int)($m['year'] ?? 0), $movies);
$years = array_filter($years, fn($y) => $y > 0);
$yearMin = $years ? min($years) : 1990;
$yearMax = $years ? max($years) : (int)date('Y');

/* წინასწარ შერჩეული პარამეტრები URL-დან (მაგ. footer-ის ბმულები) */
$preType = $_GET['type'] ?? '';
$preCollections = (array)($_GET['collections'] ?? []);

function group_checks(array $items, string $name, array $preset = []): string
{
    $out = '';
    foreach ($items as $row) {
        $k = $row['key'];
        $checked = in_array($k, $preset, true) ? ' checked' : '';
        $out .= '<label class="fcheck"><input type="checkbox" name="' . e($name) . '[]" value="' . e($k) . '"' . $checked . '>'
            . '<span class="fbox"></span><span class="flabel">' . e($row['label']) . '</span></label>';
    }
    return $out;
}
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ფილტრი · <?= e($settings['site_title']) ?></title>
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/styles.css" />
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/filter.css" />
</head>
<body class="filter-body">
  <div class="filter-page" id="filterPage"
       data-endpoint="<?= e($bu) ?>/api/movies.php"
       data-base="<?= e($bu) ?>">

    <!-- ზედა ბარი -->
    <div class="filter-top">
      <a href="<?= e($bu) ?>/index.php" class="brand">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7h20M2 17h20M7 2v20M17 2v20" stroke-linecap="round" /></svg>
        CINEM<span class="brand-dot">A</span>TA
      </a>
      <div class="filter-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7" /><path d="M21 21l-4.3-4.3" stroke-linecap="round" /></svg>
        <input type="search" id="searchInput" placeholder="ძიება სათაურით, ჟანრით, ქვეყნით, აღწერით…" autocomplete="off" />
      </div>
      <button class="filter-mobtoggle" id="mobToggle" aria-label="ფილტრები">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5h18M6 12h12M10 19h4" stroke-linecap="round" /></svg>
      </button>
      <a href="<?= e($bu) ?>/index.php" class="filter-close" aria-label="დახურვა">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
      </a>
    </div>

    <div class="filter-layout">
      <!-- გვერდითი ფილტრები -->
      <aside class="filter-sidebar" id="filterSidebar">
        <form id="filterForm">
          <div class="fgroup">
            <h4>ტიპი</h4>
            <label class="fcheck"><input type="checkbox" name="types[]" value="all" id="typeAll" checked><span class="fbox"></span><span class="flabel">ყველა</span></label>
            <?= group_checks($cats['types'], 'types', $preType ? [$preType] : []) ?>
          </div>

          <div class="fgroup">
            <h4>ჟანრი</h4>
            <?= group_checks($cats['genres'], 'genres') ?>
          </div>

          <div class="fgroup">
            <h4>გახმოვანება</h4>
            <?= group_checks($cats['dubbing'], 'dubbing') ?>
          </div>

          <div class="fgroup">
            <h4>ქვეყანა</h4>
            <?= group_checks($cats['countries'], 'countries') ?>
          </div>

          <div class="fgroup">
            <h4>კოლექცია</h4>
            <?= group_checks($cats['collections'], 'collections', $preCollections) ?>
          </div>

          <div class="fgroup">
            <h4>რეიტინგი: <span id="ratingOut">0 – 10</span></h4>
            <div class="frange" data-range="rating">
              <input type="range" name="rating_min" min="0" max="10" step="0.1" value="0">
              <input type="range" name="rating_max" min="0" max="10" step="0.1" value="10">
            </div>
          </div>

          <div class="fgroup">
            <h4>წელი: <span id="yearOut"><?= $yearMin ?> – <?= $yearMax ?></span></h4>
            <div class="frange" data-range="year">
              <input type="range" name="year_min" min="<?= $yearMin ?>" max="<?= $yearMax ?>" step="1" value="<?= $yearMin ?>">
              <input type="range" name="year_max" min="<?= $yearMin ?>" max="<?= $yearMax ?>" step="1" value="<?= $yearMax ?>">
            </div>
          </div>

          <div class="fgroup-actions">
            <button type="button" class="btn-ghost" id="resetBtn">გასუფთავება</button>
            <button type="button" class="btn-primary" id="applyBtn">ფილტრის გამოყენება</button>
          </div>
        </form>
      </aside>

      <!-- შედეგები -->
      <section class="filter-results">
        <div class="results-head">
          <span class="results-count" id="resultsCount">იტვირთება…</span>
          <div class="results-sort">
            <label>დახარისხება:</label>
            <select id="sortSelect">
              <option value="new">ახლები</option>
              <option value="rating">რეიტინგი</option>
              <option value="year">წელი</option>
              <option value="title">სათაური</option>
            </select>
          </div>
        </div>

        <div class="results-grid" id="resultsGrid"></div>

        <div class="results-loader" id="resultsLoader" hidden>
          <span class="spinner"></span> იტვირთება…
        </div>
        <div class="results-end" id="resultsEnd" hidden>ეს ყველაფერია 🎬</div>
        <div class="results-sentinel" id="sentinel"></div>
      </section>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
  </div>

  <script src="<?= e($bu) ?>/assets/js/card.js"></script>
  <script src="<?= e($bu) ?>/assets/js/filter.js"></script>
</body>
</html>
