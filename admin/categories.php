<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();
$bu = base_url();

$groups = [
    'types'       => 'ტიპები (ფილმი, ეპიზოდი…)',
    'genres'      => 'ჟანრები',
    'dubbing'     => 'გახმოვანება',
    'countries'   => 'ქვეყნები',
    'collections' => 'კოლექციები',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cats = get_categories();
    $group = $_POST['group'] ?? '';
    $action = $_POST['action'] ?? '';
    if (isset($groups[$group])) {
        if ($action === 'add') {
            $label = trim($_POST['label'] ?? '');
            $key = trim($_POST['key'] ?? '');
            if ($key === '') {
                $key = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $label));
            }
            $key = trim($key, '-');
            if ($key !== '' && $label !== '') {
                $exists = false;
                foreach ($cats[$group] as $row) {
                    if ($row['key'] === $key) { $exists = true; break; }
                }
                if (!$exists) {
                    $cats[$group][] = ['key' => $key, 'label' => $label];
                    save_categories($cats);
                    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'დამატებულია: ' . $label];
                } else {
                    $_SESSION['flash'] = ['type' => 'err', 'msg' => 'ასეთი key უკვე არსებობს.'];
                }
            }
        } elseif ($action === 'delete') {
            $key = $_POST['key'] ?? '';
            $cats[$group] = array_values(array_filter($cats[$group], fn($r) => $r['key'] !== $key));
            save_categories($cats);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'წაიშალა.'];
        }
    }
    header('Location: ' . $bu . '/admin/categories.php');
    exit;
}

$pageTitle = 'კატეგორიები';
require __DIR__ . '/includes/admin_header.php';
$cats = get_categories();
?>
<p class="hint" style="color:var(--muted);margin-bottom:20px">აქ მართავ ფილტრის ყველა მენიუს. „key“ — ლათინური იდენტიფიკატორი (ცარიელად დატოვებისას ავტომატურად შეიქმნება), „label“ — ხილული სახელი.</p>

<?php foreach ($groups as $gkey => $gtitle): ?>
  <div class="panel">
    <h3><?= e($gtitle) ?></h3>
    <div class="checks" style="margin-bottom:16px">
      <?php foreach ($cats[$gkey] ?? [] as $row): ?>
        <span class="badge" style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px 6px 12px">
          <?= e($row['label']) ?> <small style="color:var(--muted-2)">(<?= e($row['key']) ?>)</small>
          <form method="post" onsubmit="return confirm('წავშალო?');" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="group" value="<?= e($gkey) ?>">
            <input type="hidden" name="key" value="<?= e($row['key']) ?>">
            <button class="btn btn-sm btn-danger" type="submit" style="padding:2px 8px">✕</button>
          </form>
        </span>
      <?php endforeach; ?>
      <?php if (empty($cats[$gkey])): ?><span style="color:var(--muted-2)">ცარიელია</span><?php endif; ?>
    </div>
    <form method="post" class="toolbar">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="group" value="<?= e($gkey) ?>">
      <div class="search-box"><input type="text" name="label" placeholder="ხილული სახელი (label)" required></div>
      <div class="search-box"><input type="text" name="key" placeholder="key (არასავალდებულო)"></div>
      <button class="btn btn-gold" type="submit">+ დამატება</button>
    </form>
  </div>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
