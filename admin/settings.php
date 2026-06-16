<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();
$bu = base_url();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = get_settings();
    $s['site_title'] = trim($_POST['site_title'] ?? $s['site_title']);
    $newUser = trim($_POST['admin_user'] ?? '');
    if ($newUser !== '') {
        $s['admin_user'] = $newUser;
    }
    $msg = 'პარამეტრები შენახულია.';
    $type = 'ok';
    $pass = (string)($_POST['new_pass'] ?? '');
    if ($pass !== '') {
        if (strlen($pass) < 5) {
            $msg = 'პაროლი ძალიან მოკლეა (მინ. 5 სიმბოლო).';
            $type = 'err';
        } elseif ($pass !== ($_POST['new_pass2'] ?? '')) {
            $msg = 'პაროლები არ ემთხვევა.';
            $type = 'err';
        } else {
            $s['admin_pass_hash'] = password_hash($pass, PASSWORD_DEFAULT);
            $msg = 'პარამეტრები და პაროლი განახლდა.';
        }
    }
    if ($type === 'ok') {
        save_settings($s);
    }
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: ' . $bu . '/admin/settings.php');
    exit;
}

$pageTitle = 'პარამეტრები';
require __DIR__ . '/includes/admin_header.php';
$s = get_settings();
?>
<form method="post" style="max-width:560px">
  <div class="panel">
    <h3>საიტი</h3>
    <div class="field">
      <label>საიტის სათაური</label>
      <input type="text" name="site_title" value="<?= e($s['site_title']) ?>">
    </div>
  </div>

  <div class="panel">
    <h3>ადმინისტრატორი</h3>
    <div class="field">
      <label>მომხმარებლის სახელი</label>
      <input type="text" name="admin_user" value="<?= e($s['admin_user']) ?>" autocomplete="username">
    </div>
    <div class="field">
      <label>ახალი პაროლი (დატოვე ცარიელი თუ არ ცვლი)</label>
      <input type="password" name="new_pass" autocomplete="new-password">
    </div>
    <div class="field">
      <label>გაიმეორე ახალი პაროლი</label>
      <input type="password" name="new_pass2" autocomplete="new-password">
    </div>
  </div>

  <div class="form-foot">
    <button class="btn btn-gold" type="submit">შენახვა</button>
  </div>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
