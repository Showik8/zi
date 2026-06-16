<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();

/* წაშლა */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    delete_movie($_POST['id'] ?? '');
    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'ფილმი წაიშალა.'];
    header('Location: ' . base_url() . '/admin/movies.php');
    exit;
}

$pageTitle = 'ფილმები';
require __DIR__ . '/includes/admin_header.php';

$movies = get_movies();
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $ql = mb_strtolower($q, 'UTF-8');
    $movies = array_filter($movies, fn($m) => mb_strpos(mb_strtolower(json_encode($m, JSON_UNESCAPED_UNICODE), 'UTF-8'), $ql) !== false);
}
usort($movies, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$typeMap = cat_label_map('types');
?>
<div class="page-actions">
  <form class="search-box" method="get">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="ძიება…" />
  </form>
  <a class="btn btn-gold" href="<?= e($adminBase) ?>/movie-edit.php">+ ფილმის დამატება</a>
</div>

<div class="table-wrap">
  <table class="data">
    <thead><tr><th></th><th>სათაური</th><th>ტიპი</th><th>წელი</th><th>რეიტ.</th><th>ნიშნულები</th><th>მოქმედება</th></tr></thead>
    <tbody>
      <?php foreach ($movies as $m): ?>
        <tr>
          <td><img class="thumb" loading="lazy" src="<?= e(media_url($m['poster'] ?? '')) ?>" alt=""></td>
          <td><strong><?= e($m['title'] ?? '') ?></strong></td>
          <td><span class="badge"><?= e($typeMap[$m['type'] ?? ''] ?? '') ?></span></td>
          <td><?= e($m['year'] ?? '') ?></td>
          <td class="chip-gold"><?= number_format((float)($m['rating'] ?? 0), 1) ?></td>
          <td>
            <?php if (!empty($m['trending'])): ?><span class="badge">ტრენდი</span><?php endif; ?>
            <?php if (!empty($m['featured'])): ?><span class="badge">ჰერო</span><?php endif; ?>
          </td>
          <td>
            <div class="row-actions">
              <a class="btn btn-sm" href="<?= e($adminBase) ?>/movie-edit.php?id=<?= e($m['id']) ?>">რედაქ.</a>
              <form method="post" onsubmit="return confirm('ნამდვილად წავშალო „<?= e($m['title'] ?? '') ?>“?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e($m['id']) ?>">
                <button class="btn btn-sm btn-danger" type="submit">წაშლა</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$movies): ?><tr><td colspan="7" class="empty-state">ფილმები ვერ მოიძებნა.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
