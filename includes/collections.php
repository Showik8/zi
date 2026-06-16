<?php
/* includes/collections.php — კოლექციები სტუდიების მიხედვით (ფილტრით) */
/** @var array $movies */
/** @var array $collectionsCat */
$first = $collectionsCat[0]['key'] ?? '';
?>
<section class="section section-tight" id="collections">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">სტუდიების მიხედვით</span>
        <h2>კოლექციები</h2>
      </div>
      <div class="filters" id="collectionFilters">
        <?php foreach ($collectionsCat as $i => $c): ?>
          <button class="filter-btn <?= $i === 0 ? 'is-active' : '' ?>" data-filter="<?= e($c['key']) ?>"><?= e($c['label']) ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="collections-grid" id="collectionsGrid"
         data-endpoint="<?= e(base_url()) ?>/api/movies.php"
         data-initial="<?= e($first) ?>"><!-- JS --></div>
  </div>
</section>
