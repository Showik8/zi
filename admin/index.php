<?php
$pageTitle = 'მთავარი';
require __DIR__ . '/includes/admin_header.php';

$movies = get_movies();
$scroll = get_scroll();
$cats = get_categories();
$catCount = count($cats['genres']) + count($cats['countries']) + count($cats['dubbing']) + count($cats['collections']) + count($cats['types']);
$trending = count(array_filter($movies, fn($m) => !empty($m['trending'])));
$featured = count(array_filter($movies, fn($m) => !empty($m['featured'])));

// ბოლოს დამატებული
$recent = $movies;
usort($recent, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$recent = array_slice($recent, 0, 6);
$typeMap = cat_label_map('types');
?>
<div class="stat-grid">
  <div class="stat"><div class="num"><?= count($movies) ?></div><div class="lbl">ფილმი / სერია</div></div>
  <div class="stat"><div class="num"><?= $scroll ? count($scroll) : 0 ?></div><div class="lbl">Scroll Movie ვიდეო</div></div>
  <div class="stat"><div class="num"><?= $trending ?></div><div class="lbl">ტრენდული</div></div>
  <div class="stat"><div class="num"><?= $featured ?></div><div class="lbl">ჰეროში (featured)</div></div>
  <div class="stat"><div class="num"><?= $catCount ?></div><div class="lbl">კატეგორია სულ</div></div>
</div>

<div class="page-actions">
  <h3 style="margin:0">სწრაფი მოქმედებები</h3>
  <div class="toolbar">
    <a class="btn btn-gold" href="<?= e($adminBase) ?>/movie-edit.php">+ ფილმის დამატება</a>
    <a class="btn" href="<?= e($adminBase) ?>/scroll-edit.php">+ Scroll ვიდეო</a>
    <a class="btn" href="<?= e($adminBase) ?>/categories.php">კატეგორიები</a>
  </div>
</div>

<div class="panel">
  <h3>ბოლოს დამატებული ფილმები</h3>
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th></th><th>სათაური</th><th>ტიპი</th><th>წელი</th><th>რეიტინგი</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recent as $m): ?>
          <tr>
            <td><img class="thumb" loading="lazy" src="<?= e(media_url($m['poster'] ?? '')) ?>" alt=""></td>
            <td><?= e($m['title'] ?? '') ?></td>
            <td><span class="badge"><?= e($typeMap[$m['type'] ?? ''] ?? '') ?></span></td>
            <td><?= e($m['year'] ?? '') ?></td>
            <td class="chip-gold"><?= number_format((float)($m['rating'] ?? 0), 1) ?></td>
            <td><a class="btn btn-sm" href="<?= e($adminBase) ?>/movie-edit.php?id=<?= e($m['id']) ?>">რედაქტირება</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="6" class="empty-state">ფილმები ჯერ არ არის.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
