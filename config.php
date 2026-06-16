<?php
/* =========================================================================
   config.php — გლობალური კონფიგურაცია, ბილიკები, სესია
   ყველა PHP ფაილი ამას იძახებს პირველ რიგში.
   ========================================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---- root ბილიკი (ფაილური სისტემა) ---- */
define('ROOT_DIR', __DIR__);
define('DATA_DIR', ROOT_DIR . '/data');
define('UPLOAD_DIR', ROOT_DIR . '/uploads');

/* ---- ატვირთვების საქაღალდის შექმნა საჭიროებისას ---- */
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0775, true);
}
if (!is_dir(UPLOAD_DIR . '/videos')) {
    @mkdir(UPLOAD_DIR . '/videos', 0775, true);
}
if (!is_dir(UPLOAD_DIR . '/images')) {
    @mkdir(UPLOAD_DIR . '/images', 0775, true);
}

/* ---- ვებ root URL — მუშაობს ნებისმიერ ჰოსტინგზე / ქვესაქაღალდეში ---- */
function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    // საუკეთესო მეთოდი: app root-ის სხვაობა document root-თან
    $docroot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $root = str_replace('\\', '/', ROOT_DIR);
    if ($docroot !== '' && strpos($root, $docroot) === 0) {
        $base = rtrim(substr($root, strlen($docroot)), '/');
    } else {
        // fallback: SCRIPT_NAME-დან ცნობილი ქვესაქაღალდეების მოჭრა
        $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        foreach (['/admin', '/api'] as $sub) {
            if (substr($script, -strlen($sub)) === $sub) {
                $script = substr($script, 0, -strlen($sub));
            }
        }
        $base = rtrim($script, '/');
    }
    if ($base === '/') {
        $base = '';
    }
    return $base;
}

/* ---- მცირე escape helper ---- */
function e($str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/* ---- მედია URL: სრული ბმული ან ლოკალური ატვირთვა ---- */
function media_url($path): string
{
    $path = (string)$path;
    if ($path === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }
    return base_url() . '/' . ltrim($path, '/');
}

require_once ROOT_DIR . '/lib/db.php';
require_once ROOT_DIR . '/lib/seed.php';

/* პირველ გაშვებაზე ბაზის ავტომატური შექმნა */
ensure_seeded();
