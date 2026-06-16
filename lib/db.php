<?php
/* =========================================================================
   lib/db.php — JSON "მონაცემთა ბაზა" (MySQL-ის გარეშე)
   უსაფრთხო წაკითხვა/ჩაწერა ფაილის ლოქით + CRUD ჰელფერები.
   ========================================================================= */

/* ---------- დაბალი დონის JSON I/O ---------- */
function json_read(string $name, $fallback = [])
{
    $path = DATA_DIR . '/' . $name . '.json';
    if (!is_file($path)) {
        return $fallback;
    }
    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return $fallback;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $fallback;
}

function json_write(string $name, $data): bool
{
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }
    $path = DATA_DIR . '/' . $name . '.json';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    // ატომური ჩაწერა ლოქით
    $fp = fopen($path, 'c+');
    if (!$fp) {
        return false;
    }
    $ok = false;
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        $ok = true;
    }
    fclose($fp);
    return $ok;
}

/* ---------- ID გენერატორი ---------- */
function next_id(array $items, string $prefix = ''): string
{
    $max = 0;
    foreach ($items as $it) {
        $id = (string)($it['id'] ?? '');
        if (preg_match('/(\d+)$/', $id, $m)) {
            $max = max($max, (int)$m[1]);
        }
    }
    return $prefix . ($max + 1);
}

/* ========================================================================
   MOVIES
   ======================================================================== */
function get_movies(): array
{
    return json_read('movies', []);
}

function save_movies(array $movies): bool
{
    return json_write('movies', array_values($movies));
}

function get_movie(string $id): ?array
{
    foreach (get_movies() as $m) {
        if ((string)($m['id'] ?? '') === $id) {
            return $m;
        }
    }
    return null;
}

function upsert_movie(array $movie): array
{
    $movies = get_movies();
    $id = (string)($movie['id'] ?? '');
    $found = false;
    foreach ($movies as $i => $m) {
        if ((string)($m['id'] ?? '') === $id && $id !== '') {
            $movies[$i] = array_merge($m, $movie);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $movie['id'] = next_id($movies, 'm');
        $movie['created_at'] = $movie['created_at'] ?? date('Y-m-d');
        $movies[] = $movie;
    }
    save_movies($movies);
    return $movie;
}

function delete_movie(string $id): bool
{
    $movies = get_movies();
    $out = array_filter($movies, fn($m) => (string)($m['id'] ?? '') !== $id);
    return save_movies($out);
}

/* ========================================================================
   CATEGORIES / TAXONOMY (types, genres, countries, dubbing, collections)
   ======================================================================== */
function get_categories(): array
{
    $def = [
        'types' => [], 'genres' => [], 'countries' => [],
        'dubbing' => [], 'collections' => [],
    ];
    return array_merge($def, json_read('categories', []));
}

function save_categories(array $cats): bool
{
    return json_write('categories', $cats);
}

/* დახმარება: ბადებად -> label რუკა */
function cat_label_map(string $group): array
{
    $map = [];
    foreach (get_categories()[$group] ?? [] as $row) {
        if (isset($row['key'])) {
            $map[$row['key']] = $row['label'] ?? $row['key'];
        }
    }
    return $map;
}

/* ========================================================================
   SCROLL MOVIES (reels)
   ======================================================================== */
function get_scroll(): array
{
    return json_read('scroll', []);
}

function save_scroll(array $items): bool
{
    return json_write('scroll', array_values($items));
}

function upsert_scroll(array $item): array
{
    $items = get_scroll();
    $id = (string)($item['id'] ?? '');
    $found = false;
    foreach ($items as $i => $it) {
        if ((string)($it['id'] ?? '') === $id && $id !== '') {
            $items[$i] = array_merge($it, $item);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $item['id'] = next_id($items, 's');
        $items[] = $item;
    }
    save_scroll($items);
    return $item;
}

function delete_scroll(string $id): bool
{
    $items = get_scroll();
    $out = array_filter($items, fn($it) => (string)($it['id'] ?? '') !== $id);
    return save_scroll($out);
}

/* ========================================================================
   SETTINGS / ADMIN AUTH
   ======================================================================== */
function get_settings(): array
{
    $def = [
        'admin_user' => 'admin',
        // ნაგულისხმევი პაროლი: admin123  (admin პანელიდან შეცვალე!)
        'admin_pass_hash' => '$2y$10$e0NRf3i6Q0gO2y8mJ8kE1uF0xY3y0r3o1m9rN4Yk0Wl9bH2c8s8nq',
        'site_title' => 'CINEMATA',
    ];
    return array_merge($def, json_read('settings', []));
}

function save_settings(array $s): bool
{
    return json_write('settings', $s);
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_ok']);
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        header('Location: ' . base_url() . '/admin/login.php');
        exit;
    }
}

/* ========================================================================
   FILTER ENGINE — ფილტრაცია + ძიება + გვერდებად დაყოფა (infinite scroll)
   ======================================================================== */
function filter_movies(array $p): array
{
    $movies = get_movies();

    $type      = $p['type']      ?? 'all';                 // ერთი ტიპი ან all
    $types     = arr($p['types']     ?? []);                // მრავალარჩევანი (checkbox)
    if (in_array('all', $types, true)) {
        $types = [];
    }
    $genres    = arr($p['genres']    ?? []);                // checkbox-ები
    $countries = arr($p['countries'] ?? []);
    $dubbing   = arr($p['dubbing']   ?? []);
    $collections = arr($p['collections'] ?? []);
    $q         = trim((string)($p['q'] ?? ''));
    $ratingMin = isset($p['rating_min']) ? (float)$p['rating_min'] : 0;
    $ratingMax = isset($p['rating_max']) ? (float)$p['rating_max'] : 10;
    $yearMin   = isset($p['year_min']) ? (int)$p['year_min'] : 0;
    $yearMax   = isset($p['year_max']) ? (int)$p['year_max'] : 9999;
    $sort      = $p['sort'] ?? 'new';

    $qLower = mb_strtolower($q, 'UTF-8');

    $out = array_values(array_filter($movies, function ($m) use (
        $type, $types, $genres, $countries, $dubbing, $collections,
        $qLower, $ratingMin, $ratingMax, $yearMin, $yearMax
    ) {
        if ($type !== 'all' && ($m['type'] ?? '') !== $type) {
            return false;
        }
        if ($types && !in_array($m['type'] ?? '', $types, true)) {
            return false;
        }
        if ($genres && !array_intersect($genres, arr($m['genres'] ?? []))) {
            return false;
        }
        if ($countries && !in_array($m['country'] ?? '', $countries, true)) {
            return false;
        }
        if ($dubbing && !array_intersect($dubbing, arr($m['dubbing'] ?? []))) {
            return false;
        }
        if ($collections && !in_array($m['collection'] ?? '', $collections, true)) {
            return false;
        }
        $rating = (float)($m['rating'] ?? 0);
        if ($rating < $ratingMin || $rating > $ratingMax) {
            return false;
        }
        $year = (int)($m['year'] ?? 0);
        if ($year < $yearMin || $year > $yearMax) {
            return false;
        }
        // ძიება — ნებისმიერ ველში (არა მხოლოდ სათაური)
        if ($qLower !== '') {
            $hay = mb_strtolower(json_encode($m, JSON_UNESCAPED_UNICODE), 'UTF-8');
            if (mb_strpos($hay, $qLower) === false) {
                return false;
            }
        }
        return true;
    }));

    // დახარისხება
    usort($out, function ($a, $b) use ($sort) {
        switch ($sort) {
            case 'rating': return ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
            case 'year':   return ($b['year'] ?? 0) <=> ($a['year'] ?? 0);
            case 'title':  return strcmp($a['title'] ?? '', $b['title'] ?? '');
            case 'new':
            default:       return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        }
    });

    $total = count($out);

    // გვერდებად დაყოფა (infinite scroll)
    $page    = max(1, (int)($p['page'] ?? 1));
    $perPage = max(1, min(60, (int)($p['per_page'] ?? 12)));
    $offset  = ($page - 1) * $perPage;
    $slice   = array_slice($out, $offset, $perPage);

    return [
        'items'    => $slice,
        'total'    => $total,
        'page'     => $page,
        'per_page' => $perPage,
        'has_more' => ($offset + $perPage) < $total,
    ];
}

/* მასივად დაყვანა (string|array -> array) */
function arr($v): array
{
    if (is_array($v)) {
        return array_values(array_filter($v, fn($x) => $x !== '' && $x !== null));
    }
    if ($v === null || $v === '') {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode(',', (string)$v)), fn($x) => $x !== ''));
}
