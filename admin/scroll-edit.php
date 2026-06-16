<?php
require_once dirname(__DIR__) . '/config.php';
require_once ROOT_DIR . '/lib/upload.php';
require_admin();
$bu = base_url();

$id = $_GET['id'] ?? ($_POST['id'] ?? '');
$existing = null;
if ($id) {
    foreach (get_scroll() as $it) {
        if ((string)($it['id'] ?? '') === $id) { $existing = $it; break; }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cur = $existing ?? [];

    // poster
    $poster = $cur['poster'] ?? '';
    if (($_POST['poster_src'] ?? 'url') === 'file') {
        $poster = handle_image_upload('poster_file', $poster);
    } elseif (trim($_POST['poster_url'] ?? '') !== '') {
        $poster = trim($_POST['poster_url']);
    }

    // video
    $videoUrl = $cur['video_url'] ?? '';
    $videoFile = $cur['video_file'] ?? '';
    if (($_POST['video_src'] ?? 'url') === 'file') {
        $up = handle_video_upload('video_file', '');
        if ($up !== '') { $videoFile = $up; $videoUrl = ''; }
    } else {
        $videoUrl = trim($_POST['video_url'] ?? '');
        if ($videoUrl !== '') { $videoFile = ''; }
    }

    $item = [
        'id'          => $id,
        'title'       => trim($_POST['title'] ?? ''),
        'year'        => (int)($_POST['year'] ?? 0),
        'genre'       => trim($_POST['genre'] ?? ''),
        'rating'      => (float)($_POST['rating'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'poster'      => $poster,
        'video_url'   => $videoUrl,
        'video_file'  => $videoFile,
    ];
    if ($item['title'] === '') {
        $_SESSION['flash'] = ['type' => 'err', 'msg' => 'სათაური სავალდებულოა.'];
        $existing = $item;
    } else {
        upsert_scroll($item);
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'ვიდეო შენახულია.'];
        header('Location: ' . $bu . '/admin/scroll.php');
        exit;
    }
}

$v = $existing ?? ['year' => (int)date('Y'), 'rating' => 8.0];
$pageTitle = $id ? 'Scroll ვიდეოს რედაქტირება' : 'Scroll ვიდეოს დამატება';
require __DIR__ . '/includes/admin_header.php';
?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= e($id) ?>">

  <div class="panel">
    <h3>ვიდეოს მონაცემები</h3>
    <div class="form-grid">
      <div class="field full">
        <label>სათაური *</label>
        <input type="text" name="title" value="<?= e($v['title'] ?? '') ?>" required>
      </div>
      <div class="field">
        <label>წელი</label>
        <input type="number" name="year" value="<?= e($v['year'] ?? '') ?>" min="1900" max="2100">
      </div>
      <div class="field">
        <label>ჟანრი</label>
        <input type="text" name="genre" value="<?= e($v['genre'] ?? '') ?>" placeholder="მაგ. sci-fi · thriller">
      </div>
      <div class="field">
        <label>რეიტინგი (0–10)</label>
        <input type="number" name="rating" value="<?= e($v['rating'] ?? '') ?>" step="0.1" min="0" max="10">
      </div>
      <div class="field full">
        <label>აღწერა</label>
        <textarea name="description"><?= e($v['description'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="panel">
    <h3>პოსტერი / ქავერი (არასავალდებულო)</h3>
    <div class="srcswitch" data-srcswitch>
      <div class="seg">
        <label><input type="radio" name="poster_src" value="url" checked> ბმული</label>
        <label><input type="radio" name="poster_src" value="file"> ფაილი</label>
      </div>
      <div data-panel="url"><div class="field"><input type="url" name="poster_url" value="<?= e($v['poster'] ?? '') ?>" placeholder="https://…"></div></div>
      <div data-panel="file" hidden>
        <div class="filepick"><label class="btn">სურათის არჩევა<input type="file" name="poster_file" accept="image/*"></label><span class="file-name">ფაილი არ არის არჩეული</span></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <h3>ვიდეო (mp4)</h3>
    <div class="srcswitch" data-srcswitch>
      <div class="seg">
        <label><input type="radio" name="video_src" value="url" checked> ბმული (URL)</label>
        <label><input type="radio" name="video_src" value="file"> ფაილის ატვირთვა</label>
      </div>
      <div data-panel="url"><div class="field"><input type="url" name="video_url" value="<?= e($v['video_url'] ?? '') ?>" placeholder="https://…/clip.mp4"></div></div>
      <div data-panel="file" hidden>
        <div class="filepick"><label class="btn">ვიდეოს არჩევა<input type="file" name="video_file" accept="video/mp4,video/webm"></label><span class="file-name">ფაილი არ არის არჩეული</span></div>
      </div>
      <?php if (!empty($v['video_file'])): ?><div class="current-media">ატვირთული: <a href="<?= e(media_url($v['video_file'])) ?>" target="_blank"><?= e($v['video_file']) ?></a></div><?php endif; ?>
    </div>
  </div>

  <div class="form-foot">
    <button class="btn btn-gold" type="submit">შენახვა</button>
    <a class="btn" href="<?= e($adminBase) ?>/scroll.php">გაუქმება</a>
  </div>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
