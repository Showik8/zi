<?php
/* =========================================================================
   includes/render.php — ფრონტ-ის საერთო პრეზენტაცია (ბარათები, იკონები)
   ========================================================================= */

const ICON_STAR = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5.9 21.4l1.4-6.8L2.2 9.9l6.9-.8L12 2z"/></svg>';
const ICON_PLAY = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>';

/* ერთი ფილმის ბარათი (კვადრატული პოსტერი). $opts: ['recent'=>bool,'badge'=>str] */
function render_card(array $m, array $opts = []): string
{
    $typeMap = cat_label_map('types');
    $type = $typeMap[$m['type'] ?? ''] ?? '';
    $rating = number_format((float)($m['rating'] ?? 0), 1);
    $poster = media_url($m['poster'] ?? '');
    $title = e($m['title'] ?? '');
    $year = e($m['year'] ?? '');
    $href = base_url() . '/movie.php?id=' . urlencode($m['id'] ?? '');

    $badge = '';
    if (!empty($opts['badge'])) {
        $badge = '<span class="card-badge">' . e($opts['badge']) . '</span>';
    }
    $progress = '';
    $sub = $year . ($type ? ' · ' . e($type) : '');
    if (!empty($opts['recent']) && (int)($m['progress'] ?? 0) > 0) {
        $p = (int)$m['progress'];
        $progress = '<div class="card-progress"><span style="width:' . $p . '%"></span></div>';
        $sub = $p . '% ნანახი · ' . $year;
    }

    return '<a class="card" href="' . e($href) . '">'
        . '<div class="card-poster">'
        . $badge
        . '<span class="card-rating">' . ICON_STAR . $rating . '</span>'
        . '<img loading="lazy" src="' . e($poster) . '" alt="' . $title . '">'
        . '<div class="card-play"><span>' . ICON_PLAY . '</span></div>'
        . $progress
        . '</div>'
        . '<div class="card-meta"><h3>' . $title . '</h3><div class="card-sub">' . $sub . '</div></div>'
        . '</a>';
}
