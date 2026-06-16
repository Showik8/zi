<?php
/* =========================================================================
   lib/seed.php — პირველი გაშვებისას ქმნის JSON ბაზას დემო-მონაცემებით.
   ჰოსტინგზე ატვირთვისთანავე index.php ავტომატურად ჩართავს ამას.
   ========================================================================= */

function ensure_seeded(): void
{
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }

    /* ---------- categories / taxonomy ---------- */
    if (!is_file(DATA_DIR . '/categories.json')) {
        json_write('categories', seed_categories());
    }

    /* ---------- settings (+ admin პაროლის ჰეში runtime-ზე) ---------- */
    if (!is_file(DATA_DIR . '/settings.json')) {
        json_write('settings', [
            'admin_user'      => 'admin',
            'admin_pass_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'site_title'      => 'CINEMATA',
        ]);
    }

    /* ---------- movies ---------- */
    if (!is_file(DATA_DIR . '/movies.json')) {
        json_write('movies', seed_movies());
    }

    /* ---------- scroll movies (reels) ---------- */
    if (!is_file(DATA_DIR . '/scroll.json')) {
        json_write('scroll', seed_scroll());
    }
}

/* ===================================================================== */
function seed_categories(): array
{
    return [
        'types' => [
            ['key' => 'movie',     'label' => 'ფილმები'],
            ['key' => 'episode',   'label' => 'ეპიზოდები'],
            ['key' => 'animation', 'label' => 'ანიმაცია'],
            ['key' => 'series',    'label' => 'სერიალები'],
        ],
        'genres' => [
            ['key' => 'sci-fi',    'label' => 'სამეცნ. ფანტასტიკა'],
            ['key' => 'thriller',  'label' => 'თრილერი'],
            ['key' => 'drama',     'label' => 'დრამა'],
            ['key' => 'crime',     'label' => 'კრიმინალი'],
            ['key' => 'adventure', 'label' => 'სათავგადასავლო'],
            ['key' => 'mystery',   'label' => 'მისტიკა'],
            ['key' => 'action',    'label' => 'ექშენი'],
            ['key' => 'cyberpunk', 'label' => 'კიბერპანკი'],
            ['key' => 'western',   'label' => 'ვესტერნი'],
            ['key' => 'family',    'label' => 'საოჯახო'],
            ['key' => 'fantasy',   'label' => 'ფენტეზი'],
            ['key' => 'comedy',    'label' => 'კომედია'],
            ['key' => 'romance',   'label' => 'რომანტიკა'],
            ['key' => 'horror',    'label' => 'საშინელება'],
        ],
        'countries' => [
            ['key' => 'usa',     'label' => 'აშშ'],
            ['key' => 'uk',      'label' => 'დიდი ბრიტანეთი'],
            ['key' => 'georgia', 'label' => 'საქართველო'],
            ['key' => 'france',  'label' => 'საფრანგეთი'],
            ['key' => 'japan',   'label' => 'იაპონია'],
            ['key' => 'korea',   'label' => 'კორეა'],
            ['key' => 'germany', 'label' => 'გერმანია'],
        ],
        'dubbing' => [
            ['key' => 'geo_dub',  'label' => 'ქართულად ნახმოვანებული'],
            ['key' => 'geo_sub',  'label' => 'ქართული სუბტიტრები'],
            ['key' => 'eng',      'label' => 'ინგლისური'],
            ['key' => 'rus',      'label' => 'რუსული'],
            ['key' => 'original', 'label' => 'ორიგინალი'],
        ],
        'collections' => [
            ['key' => 'marvel',    'label' => 'Marvel'],
            ['key' => 'netflix',   'label' => 'Netflix'],
            ['key' => 'dc',        'label' => 'DC'],
            ['key' => 'universal', 'label' => 'Universal Pictures'],
        ],
    ];
}

/* ===================================================================== */
function seed_movies(): array
{
    // [title, type, year, rating, country, [genres], [dubbing], collection, trending, featured, recent/progress, seed, description]
    $rows = [
        ['Echoes of Tomorrow', 'movie', 2025, 8.7, 'usa', ['sci-fi','thriller'], ['geo_dub','geo_sub'], '', true, true, 72,
            'მომავლის ქალაქში დეტექტივი აღმოაჩენს, რომ მისი მოგონებები სხვისია. დროსთან რბოლა იწყება, სანამ სიმართლე გვიან არ იქნება.'],
        ['Crimson Harbor', 'movie', 2024, 8.2, 'uk', ['drama','crime'], ['geo_sub','eng'], '', true, true, 30,
            'პორტის პატარა ქალაქში ერთი ღამე ცვლის ყველაფერს. ოჯახური საიდუმლოები და ძველი ვალები ზედაპირზე ამოდის.'],
        ['The Last Cartographer', 'movie', 2025, 9.0, 'france', ['adventure','mystery'], ['geo_sub','original'], '', true, true, 0,
            'უკანასკნელი რუკის შემქმნელი ეძებს ადგილს, რომელიც არცერთ რუკაზე არ არსებობს — და რომელიც, შესაძლოა, არც უნდა არსებობდეს.'],
        ['Neon Requiem', 'movie', 2023, 7.9, 'japan', ['action','cyberpunk'], ['geo_dub','original'], '', true, true, 60,
            'ნეონის ქალაქში ნაქირავები მკვლელი ბოლო დავალებას იღებს — საკუთარი წარსულის წაშლას ხელს ვერავინ უშლის.'],
        ['Quiet Frontier', 'movie', 2025, 8.4, 'usa', ['western','drama'], ['geo_sub'], '', true, true, 25,
            'მიტოვებულ სასაზღვრო ქალაქში მარტოხელა მცველი იცავს იმას, რაც ყველამ მიატოვა — და საკუთარ თავსაც.'],

        ['Dark Tide', 'episode', 2025, 8.1, 'usa', ['thriller','mystery'], ['geo_sub'], '', true, false, 0, 'სეზონი 2, ეპიზოდი 4 — ტალღა უფრო ღრმად მიდის.'],
        ['Hollow Crown', 'episode', 2024, 8.6, 'uk', ['drama'], ['geo_sub','eng'], '', true, false, 0, 'სეზონი 1, ეპიზოდი 8 — ტახტი მძიმეა.'],
        ['Static', 'episode', 2025, 7.7, 'usa', ['sci-fi'], ['original'], '', false, false, 0, 'სეზონი 3, ეპიზოდი 1 — სიგნალი ქრება.'],
        ['The Vow', 'episode', 2023, 8.3, 'korea', ['romance','drama'], ['geo_sub'], '', false, false, 0, 'სეზონი 1, ეპიზოდი 6 — დანაპირები ფასი აქვს.'],

        ['Starlight Kids', 'animation', 2025, 8.9, 'usa', ['family','adventure'], ['geo_dub'], '', true, false, 0, 'ვარსკვლავური ბავშვების სათავგადასავლო მოგზაურობა.'],
        ['Paper Dragons', 'animation', 2024, 8.5, 'japan', ['fantasy','family'], ['geo_dub','geo_sub'], '', true, false, 85, 'ქაღალდის დრაკონები ცოცხლდებიან.'],
        ['Lumen', 'animation', 2025, 9.1, 'france', ['family','fantasy'], ['geo_dub'], '', true, false, 90, 'პატარა სინათლის ნაპერწკალი ეძებს გზას სახლისკენ უსასრულო სიბნელეში.'],
        ['Tiny Galaxy', 'animation', 2023, 8.0, 'usa', ['family','comedy'], ['geo_dub'], '', false, false, 0, 'პატარა გალაქტიკაში დიდი თავგადასავალია.'],

        ['Iron Pulse', 'movie', 2024, 8.3, 'usa', ['action','sci-fi'], ['geo_dub','geo_sub'], 'marvel', false, false, 0, 'რკინის გული ახალ მისიას იღებს.'],
        ['Web of Echoes', 'movie', 2025, 8.7, 'usa', ['action','adventure'], ['geo_dub'], 'marvel', true, false, 0, 'ქსელი, რომელიც ყველაფერს აკავშირებს.'],
        ['Stormbreaker', 'movie', 2023, 8.0, 'usa', ['action','fantasy'], ['geo_sub'], 'marvel', false, false, 0, 'ჭექა-ქუხილის იარაღი ხელახლა იჭედება.'],
        ['Sentinel', 'series', 2025, 7.8, 'usa', ['action','sci-fi'], ['original'], 'marvel', false, false, 0, 'მცველები ფხიზლობენ.'],

        ['Crown & Country', 'series', 2024, 8.6, 'uk', ['drama'], ['geo_sub','eng'], 'netflix', false, false, 0, 'ტახტი და სამშობლო ერთ სასწორზე.'],
        ['Midnight Diner', 'series', 2025, 8.4, 'japan', ['drama','comedy'], ['geo_sub'], 'netflix', false, false, 12, 'შუაღამის სასადილო ისტორიებით სავსეა.'],
        ['Paper Streets', 'movie', 2023, 7.9, 'usa', ['crime','thriller'], ['geo_sub'], 'netflix', false, false, 0, 'ქუჩები, რომლებიც რუკაზე არ არსებობს.'],
        ['The Quiet Ones', 'movie', 2025, 8.2, 'uk', ['horror','mystery'], ['geo_sub'], 'netflix', false, false, 0, 'ჩუმები ყველაზე ხმამაღლა ლაპარაკობენ.'],

        ['Gotham Nights', 'series', 2024, 8.8, 'usa', ['action','crime'], ['geo_dub','geo_sub'], 'dc', true, false, 45, 'ბნელი ქალაქის ღამეები.'],
        ['Speed Force', 'movie', 2025, 8.1, 'usa', ['action','sci-fi'], ['geo_dub'], 'dc', false, false, 0, 'სიჩქარე, რომელიც დროს ამტვრევს.'],
        ['Deep Tide', 'movie', 2023, 7.7, 'usa', ['adventure','action'], ['geo_sub'], 'dc', false, false, 0, 'ოკეანის სიღრმის სამეფო.'],
        ['Amazon Steel', 'movie', 2025, 8.5, 'usa', ['action','fantasy'], ['geo_dub'], 'dc', false, false, 0, 'ფოლადის მეომარი ქალი.'],

        ['Lost Park', 'movie', 2024, 8.0, 'usa', ['adventure','family'], ['geo_dub'], 'universal', false, false, 0, 'დაკარგული პარკი ცოცხლდება.'],
        ['Fast Lane', 'movie', 2025, 7.6, 'germany', ['action'], ['geo_sub'], 'universal', false, false, 0, 'სწრაფი ზოლი არავის აცდის.'],
        ['Bright Minions', 'animation', 2023, 8.3, 'usa', ['family','comedy'], ['geo_dub'], 'universal', false, false, 0, 'პატარა ყვითელი დამხმარეები.'],
        ['Oppen Light', 'movie', 2024, 9.0, 'usa', ['drama','thriller'], ['geo_sub','eng'], 'universal', true, false, 0, 'სინათლე, რომელმაც სამყარო შეცვალა.'],
    ];

    $sample = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
    $out = [];
    $i = 1;
    foreach ($rows as $r) {
        [$title, $type, $year, $rating, $country, $genres, $dub, $collection, $trending, $featured, $progress, $desc] = $r;
        $seed = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
        $out[] = [
            'id'          => 'm' . $i,
            'title'       => $title,
            'title_en'    => $title,
            'type'        => $type,
            'year'        => $year,
            'rating'      => $rating,
            'country'     => $country,
            'genres'      => $genres,
            'dubbing'     => $dub,
            'collection'  => $collection,
            'duration'    => 90 + ($i * 3) % 70,
            'director'    => 'A. Director',
            'cast'        => ['Actor One', 'Actor Two'],
            'description' => $desc,
            'poster'      => "https://picsum.photos/seed/{$seed}-p/600/600",
            'backdrop'    => "https://picsum.photos/seed/{$seed}-b/1600/900",
            'video_url'   => $sample,
            'video_file'  => '',
            'trending'    => $trending,
            'featured'    => $featured,
            'recent'      => $progress > 0,
            'progress'    => $progress,
            'created_at'  => '2026-06-' . str_pad((string)(($i % 28) + 1), 2, '0', STR_PAD_LEFT),
        ];
        $i++;
    }
    return $out;
}

/* ===================================================================== */
function seed_scroll(): array
{
    $vids = [
        ['Echoes of Tomorrow', 2025, 'sci-fi', 8.7, 'BigBuckBunny', 'მომავლის ქალაქში დეტექტივი აღმოაჩენს, რომ მისი მოგონებები სხვისია.'],
        ['Crimson Harbor', 2024, 'drama', 8.2, 'ElephantsDream', 'პორტის ქალაქში ერთი ღამე ცვლის ყველაფერს.'],
        ['Neon Requiem', 2023, 'action', 7.9, 'ForBiggerBlazes', 'ნეონის ქალაქში ნაქირავები მკვლელი ბოლო დავალებას იღებს.'],
        ['The Last Cartographer', 2025, 'adventure', 9.0, 'ForBiggerEscapes', 'უკანასკნელი რუკის შემქმნელი ეძებს ადგილს, რომელიც არ არსებობს.'],
        ['Lumen', 2025, 'animation', 9.1, 'ForBiggerFun', 'პატარა სინათლის ნაპერწკალი ეძებს გზას სახლისკენ.'],
        ['Quiet Frontier', 2025, 'western', 8.4, 'ForBiggerJoyrides', 'მიტოვებულ სასაზღვრო ქალაქში მარტოხელა მცველი იცავს ყველაფერს.'],
    ];
    $out = [];
    $i = 1;
    foreach ($vids as $v) {
        [$title, $year, $genre, $rating, $file, $desc] = $v;
        $out[] = [
            'id'          => 's' . $i,
            'title'       => $title,
            'year'        => $year,
            'genre'       => $genre,
            'rating'      => $rating,
            'description' => $desc,
            'video_url'   => "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/{$file}.mp4",
            'video_file'  => '',
            'poster'      => "https://picsum.photos/seed/v-" . urlencode($title) . "/720/1280",
        ];
        $i++;
    }
    return $out;
}
