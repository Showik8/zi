<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    delete_scroll($_POST['id'] ?? '');
    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'ვიდეო წაიშალა.'];
    header('Location: ' . base_url() . '/admin/scroll.php');
    exit;
}

$pageTitle = 'Scroll Movie';
require __DIR__ . '/includes/admin_header.php';
$items = get_scroll();
?>
<div class="page-actions">
  <h3 style="margin:0">Scroll Movie ვიდეოები</h3>
  <a class="btn btn-gold" href="<?= e($adminBase) ?>/scroll-edit.php">+ ვიდეოს დამატება</a>
</div>

<div class="table-wrap">
  <table class="data">
    <thead><tr><th></th><th>სათაური</th><th>წელი</th><th>ჟანრი</th><th>რეიტ.</th><th>წყარო</th><th>მოქმედება</th></tr></thead>
    <tbody>
      <?php foreach ($items as $v): ?>
        <tr>
          <td><img class="thumb" loading="lazy" src="<?= e(media_url($v['poster'] ?? '')) ?>" alt=""></td>
          <td><strong><?= e($v['title'] ?? '') ?></strong></td>
          <td><?= e($v['year'] ?? '') ?></td>
          <td><span class="badge"><?= e($v['genre'] ?? '') ?></span></td>
          <td class="chip-gold"><?= number_format((float)($v['rating'] ?? 0), 1) ?></td>
          <td><span class="badge"><?= !empty($v['video_file']) ? 'ატვირთული' : 'URL' ?></span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-sm" href="<?= e($adminBase) ?>/scroll-edit.php?id=<?= e($v['id']) ?>">რედაქ.</a>
              <form method="post" onsubmit="return confirm('წავშალო?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= e($v['id']) ?>">
                <button class="btn btn-sm btn-danger" type="submit">წაშლა</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?><tr><td colspan="7" class="empty-state">ვიდეოები ჯერ არ არის.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
