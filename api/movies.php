<?php
/* =========================================================================
   api/movies.php — ფილტრი + ძიება + infinite scroll-ის JSON ენდპოინტი
   მაგ: api/movies.php?type=movie&genres[]=action&q=neon&page=2&per_page=12
   ========================================================================= */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$result = filter_movies([
    'type'        => $_GET['type']        ?? 'all',
    'types'       => $_GET['types']       ?? [],
    'genres'      => $_GET['genres']      ?? [],
    'countries'   => $_GET['countries']   ?? [],
    'dubbing'     => $_GET['dubbing']      ?? [],
    'collections' => $_GET['collections'] ?? [],
    'q'           => $_GET['q']           ?? '',
    'rating_min'  => $_GET['rating_min']  ?? 0,
    'rating_max'  => $_GET['rating_max']  ?? 10,
    'year_min'    => $_GET['year_min']    ?? 0,
    'year_max'    => $_GET['year_max']    ?? 9999,
    'sort'        => $_GET['sort']        ?? 'new',
    'page'        => $_GET['page']        ?? 1,
    'per_page'    => $_GET['per_page']    ?? 12,
]);

/* label-ების რუკები კლიენტისთვის (ბეჯების საჩვენებლად) */
$cats = get_categories();
$labels = [
    'types'       => [],
    'genres'      => [],
    'countries'   => [],
    'dubbing'     => [],
    'collections' => [],
];
foreach ($labels as $g => $_) {
    foreach ($cats[$g] ?? [] as $row) {
        $labels[$g][$row['key']] = $row['label'] ?? $row['key'];
    }
}

echo json_encode([
    'ok'      => true,
    'items'   => $result['items'],
    'total'   => $result['total'],
    'page'    => $result['page'],
    'hasMore' => $result['has_more'],
    'labels'  => $labels,
    'baseUrl' => base_url(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
