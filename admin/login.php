<?php
/* admin/login.php — ავტორიზაცია */
require_once dirname(__DIR__) . '/config.php';

$bu = base_url();
$error = '';

if (admin_logged_in()) {
    header('Location: ' . $bu . '/admin/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = (string)($_POST['pass'] ?? '');
    $s = get_settings();
    $hash = $s['admin_pass_hash'] ?? '';
    $okPass = $hash ? password_verify($pass, $hash) : ($pass === 'admin123');
    if ($user === ($s['admin_user'] ?? 'admin') && $okPass) {
        session_regenerate_id(true);
        $_SESSION['admin_ok'] = true;
        header('Location: ' . $bu . '/admin/index.php');
        exit;
    }
    $error = 'მომხმარებელი ან პაროლი არასწორია';
}
$settings = get_settings();
?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>შესვლა · ადმინ პანელი</title>
  <link rel="stylesheet" href="<?= e($bu) ?>/assets/css/admin.css" />
</head>
<body>
  <div class="login-wrap">
    <form class="login-card" method="post">
      <div class="admin-brand">CINEM<span>A</span>TA <small>admin</small></div>
      <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
      <div class="field">
        <label>მომხმარებელი</label>
        <input type="text" name="user" value="admin" autocomplete="username" required />
      </div>
      <div class="field">
        <label>პაროლი</label>
        <input type="password" name="pass" autocomplete="current-password" required />
      </div>
      <button class="btn btn-gold" type="submit">შესვლა</button>
      <p class="login-note">ნაგულისხმევი: admin / admin123 — შესვლის შემდეგ შეცვალე პარამეტრებში.</p>
    </form>
  </div>
</body>
</html>
