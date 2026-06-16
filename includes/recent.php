<?php
/* includes/recent.php — ბოლოს ნანახი (slider) */
/** @var array $recent */
if (!$recent) {
    return;
}
?>
<section class="section recent-section" id="recent">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">გააგრძელე ყურება</span>
        <h2>ბოლოს ნანახი</h2>
      </div>
    </div>

    <div class="carousel-viewport">
      <div class="carousel-track" id="recentTrack">
        <?php foreach ($recent as $m): ?>
          <?= render_card($m, ['recent' => true]) ?>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="carousel-controls">
      <div class="dots" id="recentDots"></div>
      <div class="nav-group">
        <button class="nav-arrow" id="recPrev" aria-label="წინა"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
        <button class="nav-arrow" id="recNext" aria-label="შემდეგი"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
      </div>
    </div>
  </div>
</section>
