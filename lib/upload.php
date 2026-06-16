<?php
/* =========================================================================
   lib/upload.php — სურათისა და ვიდეოს ატვირთვა (file ან url)
   აბრუნებს შენახულ ბილიკს (uploads/...) ან url-ს. შეცდომისას ცარიელ string-ს.
   ========================================================================= */

function _safe_name(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $name);
    return trim($name, '-_.') ?: 'file';
}

function _unique(string $dir, string $base, string $ext): string
{
    $name = $base . '.' . $ext;
    $n = 1;
    while (is_file($dir . '/' . $name)) {
        $name = $base . '-' . $n . '.' . $ext;
        $n++;
    }
    return $name;
}

/* სურათის ატვირთვა; $field — $_FILES გასაღები. ცარიელ ფაილზე აბრუნებს $fallbackUrl-ს */
function handle_image_upload(string $field, string $fallbackUrl = ''): string
{
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif', 'avif' => 'image/avif'];
    return _store_file($field, UPLOAD_DIR . '/images', 'uploads/images', $allowed, 12 * 1024 * 1024, $fallbackUrl);
}

/* ვიდეოს ატვირთვა (mp4 და ა.შ.) */
function handle_video_upload(string $field, string $fallbackUrl = ''): string
{
    $allowed = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime', 'm4v' => 'video/x-m4v', 'ogg' => 'video/ogg'];
    return _store_file($field, UPLOAD_DIR . '/videos', 'uploads/videos', $allowed, 600 * 1024 * 1024, $fallbackUrl);
}

function _store_file(string $field, string $absDir, string $relDir, array $allowed, int $maxSize, string $fallback): string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $fallback;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return $fallback;
    }
    if ($f['size'] > $maxSize) {
        return $fallback;
    }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) {
        return $fallback;
    }
    if (!is_dir($absDir)) {
        @mkdir($absDir, 0775, true);
    }
    $base = _safe_name(pathinfo($f['name'], PATHINFO_FILENAME));
    $name = _unique($absDir, $base, $ext);
    $dest = $absDir . '/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        return $fallback;
    }
    return $relDir . '/' . $name;
}
