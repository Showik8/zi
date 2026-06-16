<?php
/* includes/hero.php — გმირი (slider) + ტრენდულების რეილი ჰეროს შიგნით.
   ტრენდულები ჩაშენებულია ჰეროში, მაგრამ აღწერას ხელს არ უშლის (ცალკე ფენა ქვემოთ). */
$bu = base_url();
/** @var array $featured */
/** @var array $trending */
?>
<section class="hero" id="home">
  <div class="hero-slides" id="heroSlides">
    <?php foreach ($featured as $i => $m): ?>
      <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>">
        <div class="hero-bg" style="background-image:url('<?= e(media_url($m['backdrop'] ?? $m['poster'] ?? '')) ?>')"></div>
        <div class="container">
          <div class="hero-content">
            <span class="hero-tag">გამორჩეული</span>
            <h1 class="hero-title"><?= e($m['title'] ?? '') ?></h1>
            <div class="hero-meta">
              <span class="hero-rating"><?= ICON_STAR ?><?= number_format((float)($m['rating'] ?? 0), 1) ?></span>
              <span class="pill"><?= e($m['year'] ?? '') ?></span>
              <?php $g = cat_label_map('genres'); $gn = array_map(fn($k) => $g[$k] ?? $k, $m['genres'] ?? []); ?>
              <span class="pill"><?= e(implode(' · ', array_slice($gn, 0, 2))) ?></span>
            </div>
            <p class="hero-desc"><?= e($m['description'] ?? '') ?></p>
            <div class="hero-actions">
              <a class="btn-primary" href="<?= e($bu) ?>/movie.php?id=<?= e($m['id'] ?? '') ?>"><?= ICON_PLAY ?> ყურება</a>
              <a class="btn-ghost" href="<?= e($bu) ?>/filter.php">+ ყველა ფილმი</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <button class="hero-nav prev" id="heroPrev" aria-label="წინა">
    <span class="nav-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
  </button>
  <button class="hero-nav next" id="heroNext" aria-label="შემდეგი">
    <span class="nav-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
  </button>

  <!-- ===== ტრენდულების რეილი — ჰეროს შიგნით, ქვედა ფენაში ===== -->
  <div class="hero-trending" id="heroTrending">
    <div class="hero-trending-head">
      <span class="eyebrow">ტრენდული ახლა</span>
      <div class="hero-dots" id="heroDots"></div>
    </div>
    <div class="trend-rail" id="trendRail">
      <?php foreach ($trending as $m): ?>
        <?= render_card($m) ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
