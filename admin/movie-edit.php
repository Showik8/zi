<?php
require_once dirname(__DIR__) . '/config.php';
require_once ROOT_DIR . '/lib/upload.php';
require_admin();

$bu = base_url();
$cats = get_categories();
$id = $_GET['id'] ?? ($_POST['id'] ?? '');
$existing = $id ? get_movie($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cur = $existing ?? [];

    // --- მედია: poster (url/file) ---
    $poster = $cur['poster'] ?? '';
    if (($_POST['poster_src'] ?? 'url') === 'file') {
        $poster = handle_image_upload('poster_file', $poster);
    } elseif (trim($_POST['poster_url'] ?? '') !== '') {
        $poster = trim($_POST['poster_url']);
    }

    // --- backdrop (url/file) ---
    $backdrop = $cur['backdrop'] ?? '';
    if (($_POST['backdrop_src'] ?? 'url') === 'file') {
        $backdrop = handle_image_upload('backdrop_file', $backdrop);
    } elseif (trim($_POST['backdrop_url'] ?? '') !== '') {
        $backdrop = trim($_POST['backdrop_url']);
    }

    // --- video (url/file mp4) ---
    $videoUrl = $cur['video_url'] ?? '';
    $videoFile = $cur['video_file'] ?? '';
    if (($_POST['video_src'] ?? 'url') === 'file') {
        $up = handle_video_upload('video_file', '');
        if ($up !== '') {
            $videoFile = $up;
            $videoUrl = '';
        }
    } else {
        $videoUrl = trim($_POST['video_url'] ?? '');
        if ($videoUrl !== '') {
            $videoFile = '';
        }
    }

    $movie = [
        'id'          => $id,
        'title'       => trim($_POST['title'] ?? ''),
        'title_en'    => trim($_POST['title_en'] ?? ''),
        'type'        => $_POST['type'] ?? 'movie',
        'year'        => (int)($_POST['year'] ?? 0),
        'rating'      => (float)($_POST['rating'] ?? 0),
        'country'     => $_POST['country'] ?? '',
        'genres'      => array_values((array)($_POST['genres'] ?? [])),
        'dubbing'     => array_values((array)($_POST['dubbing'] ?? [])),
        'collection'  => $_POST['collection'] ?? '',
        'duration'    => (int)($_POST['duration'] ?? 0),
        'director'    => trim($_POST['director'] ?? ''),
        'cast'        => array_values(array_filter(array_map('trim', explode(',', $_POST['cast'] ?? '')))),
        'description' => trim($_POST['description'] ?? ''),
        'poster'      => $poster,
        'backdrop'    => $backdrop,
        'video_url'   => $videoUrl,
        'video_file'  => $videoFile,
        'trending'    => !empty($_POST['trending']),
        'featured'    => !empty($_POST['featured']),
        'recent'      => !empty($_POST['recent']),
        'progress'    => (int)($_POST['progress'] ?? 0),
    ];
    if (!empty($cur['created_at'])) {
        $movie['created_at'] = $cur['created_at'];
    }

    if ($movie['title'] === '') {
        $_SESSION['flash'] = ['type' => 'err', 'msg' => 'სათაური სავალდებულოა.'];
    } else {
        $saved = upsert_movie($movie);
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'ფილმი შენახულია: ' . $saved['title']];
        header('Location: ' . $bu . '/admin/movies.php');
        exit;
    }
    $existing = $movie; // შენახე შეყვანილი ფორმაში
}

$m = $existing ?? [
    'type' => 'movie', 'year' => (int)date('Y'), 'rating' => 7.0, 'genres' => [], 'dubbing' => [],
    'progress' => 0, 'cast' => [],
];

$pageTitle = $id ? 'ფილმის რედაქტირება' : 'ფილმის დამატება';
require __DIR__ . '/includes/admin_header.php';

/** ჩეკ-დახმარება */
function checked_in($val, $arr) { return in_array($val, (array)$arr, true) ? 'checked' : ''; }
function sel($a, $b) { return (string)$a === (string)$b ? 'selected' : ''; }
?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= e($id) ?>">

  <div class="panel">
    <h3>ძირითადი ინფორმაცია</h3>
    <div class="form-grid">
      <div class="field">
        <label>სათაური (ქართულად) *</label>
        <input type="text" name="title" value="<?= e($m['title'] ?? '') ?>" required>
      </div>
      <div class="field">
        <label>სათაური (ინგლისურად)</label>
        <input type="text" name="title_en" value="<?= e($m['title_en'] ?? '') ?>">
      </div>
      <div class="field">
        <label>ტიპი</label>
        <select name="type">
          <?php foreach ($cats['types'] as $t): ?>
            <option value="<?= e($t['key']) ?>" <?= sel($t['key'], $m['type'] ?? '') ?>><?= e($t['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>ქვეყანა</label>
        <select name="country">
          <option value="">—</option>
          <?php foreach ($cats['countries'] as $c): ?>
            <option value="<?= e($c['key']) ?>" <?= sel($c['key'], $m['country'] ?? '') ?>><?= e($c['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>წელი</label>
        <input type="number" name="year" value="<?= e($m['year'] ?? '') ?>" min="1900" max="2100">
      </div>
      <div class="field">
        <label>რეიტინგი (0–10)</label>
        <input type="number" name="rating" value="<?= e($m['rating'] ?? '') ?>" step="0.1" min="0" max="10">
      </div>
      <div class="field">
        <label>ხანგრძლივობა (წუთი)</label>
        <input type="number" name="duration" value="<?= e($m['duration'] ?? '') ?>" min="0">
      </div>
      <div class="field">
        <label>კოლექცია</label>
        <select name="collection">
          <option value="">—</option>
          <?php foreach ($cats['collections'] as $c): ?>
            <option value="<?= e($c['key']) ?>" <?= sel($c['key'], $m['collection'] ?? '') ?>><?= e($c['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>რეჟისორი</label>
        <input type="text" name="director" value="<?= e($m['director'] ?? '') ?>">
      </div>
      <div class="field">
        <label>მსახიობები (მძიმით გამოყოფილი)</label>
        <input type="text" name="cast" value="<?= e(implode(', ', (array)($m['cast'] ?? []))) ?>">
      </div>
      <div class="field full">
        <label>აღწერა</label>
        <textarea name="description"><?= e($m['description'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="panel">
    <h3>ჟანრები</h3>
    <div class="checks">
      <?php foreach ($cats['genres'] as $g): ?>
        <label><input type="checkbox" name="genres[]" value="<?= e($g['key']) ?>" <?= checked_in($g['key'], $m['genres'] ?? []) ?>> <?= e($g['label']) ?></label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel">
    <h3>გახმოვანება</h3>
    <div class="checks">
      <?php foreach ($cats['dubbing'] as $d): ?>
        <label><input type="checkbox" name="dubbing[]" value="<?= e($d['key']) ?>" <?= checked_in($d['key'], $m['dubbing'] ?? []) ?>> <?= e($d['label']) ?></label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel">
    <h3>პოსტერი (კვადრატული სურათი)</h3>
    <div class="srcswitch" data-srcswitch>
      <div class="seg">
        <label><input type="radio" name="poster_src" value="url" checked> ბმული (URL)</label>
        <label><input type="radio" name="poster_src" value="file"> ფაილის ატვირთვა</label>
      </div>
      <div data-panel="url">
        <div class="field"><input type="url" name="poster_url" value="<?= e($m['poster'] ?? '') ?>" placeholder="https://…/poster.jpg"></div>
      </div>
      <div data-panel="file" hidden>
        <div class="filepick">
          <label class="btn">ფაილის არჩევა<input type="file" name="poster_file" accept="image/*"></label>
          <span class="file-name">ფაილი არ არის არჩეული</span>
        </div>
      </div>
      <?php if (!empty($m['poster'])): ?><div class="current-media">მიმდინარე: <a href="<?= e(media_url($m['poster'])) ?>" target="_blank"><?= e($m['poster']) ?></a></div><?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <h3>ფონური სურათი (backdrop, ჰეროსთვის)</h3>
    <div class="srcswitch" data-srcswitch>
      <div class="seg">
        <label><input type="radio" name="backdrop_src" value="url" checked> ბმული (URL)</label>
        <label><input type="radio" name="backdrop_src" value="file"> ფაილის ატვირთვა</label>
      </div>
      <div data-panel="url">
        <div class="field"><input type="url" name="backdrop_url" value="<?= e($m['backdrop'] ?? '') ?>" placeholder="https://…/backdrop.jpg"></div>
      </div>
      <div data-panel="file" hidden>
        <div class="filepick">
          <label class="btn">ფაილის არჩევა<input type="file" name="backdrop_file" accept="image/*"></label>
          <span class="file-name">ფაილი არ არის არჩეული</span>
        </div>
      </div>
      <?php if (!empty($m['backdrop'])): ?><div class="current-media">მიმდინარე: <a href="<?= e(media_url($m['backdrop'])) ?>" target="_blank"><?= e($m['backdrop']) ?></a></div><?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <h3>ვიდეო (mp4 — ბმულით ან პირდაპირ ატვირთვით)</h3>
    <div class="srcswitch" data-srcswitch>
      <div class="seg">
        <label><input type="radio" name="video_src" value="url" checked> ბმული (URL)</label>
        <label><input type="radio" name="video_src" value="file"> ფაილის ატვირთვა (mp4)</label>
      </div>
      <div data-panel="url">
        <div class="field"><input type="url" name="video_url" value="<?= e($m['video_url'] ?? '') ?>" placeholder="https://…/movie.mp4"></div>
      </div>
      <div data-panel="file" hidden>
        <div class="filepick">
          <label class="btn">ვიდეოს არჩევა<input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime"></label>
          <span class="file-name">ფაილი არ არის არჩეული</span>
        </div>
        <p class="hint">დიდი ფაილისთვის გაითვალისწინე ჰოსტინგის upload_max_filesize / post_max_size.</p>
      </div>
      <?php if (!empty($m['video_file'])): ?><div class="current-media">ატვირთული ფაილი: <a href="<?= e(media_url($m['video_file'])) ?>" target="_blank"><?= e($m['video_file']) ?></a></div><?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <h3>განთავსება</h3>
    <div class="checks">
      <label><input type="checkbox" name="trending" value="1" <?= !empty($m['trending']) ? 'checked' : '' ?>> ტრენდული (ჰეროს რეილში)</label>
      <label><input type="checkbox" name="featured" value="1" <?= !empty($m['featured']) ? 'checked' : '' ?>> ჰეროს სლაიდი (featured)</label>
      <label><input type="checkbox" name="recent" value="1" <?= !empty($m['recent']) ? 'checked' : '' ?>> ბოლოს ნანახში</label>
    </div>
    <div class="field" style="max-width:240px;margin-top:14px">
      <label>ნახვის პროგრესი % (ბოლოს ნანახისთვის)</label>
      <input type="number" name="progress" value="<?= e($m['progress'] ?? 0) ?>" min="0" max="100">
    </div>
  </div>

  <div class="form-foot">
    <button class="btn btn-gold" type="submit">შენახვა</button>
    <a class="btn" href="<?= e($adminBase) ?>/movies.php">გაუქმება</a>
  </div>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
