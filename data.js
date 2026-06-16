/* =========================================================================
   data.js — საიტის ყველა მონაცემი მასივებშია.
   სურათების შესაცვლელად უბრალოდ შეცვალე "image" / "backdrop" ბმულები
   (ახლა გამოყენებულია picsum.photos დემო-სურათები).
   ========================================================================= */

/* ---------- HERO სლაიდები ---------- */
const heroMovies = [
  {
    title: "Echoes of Tomorrow",
    year: 2025,
    genre: "Sci-Fi · Thriller",
    rating: 8.7,
    description:
      "მომავლის ქალაქში დეტექტივი აღმოაჩენს, რომ მისი მოგონებები სხვისია. დროსთან რბოლა იწყება, სანამ სიმართლე გვიან არ იქნება.",
    backdrop: "https://picsum.photos/seed/echoes/1600/900",
  },
  {
    title: "Crimson Harbor",
    year: 2024,
    genre: "Drama · Crime",
    rating: 8.2,
    description:
      "პორტის პატარა ქალაქში ერთი ღამე ცვლის ყველაფერს. ოჯახური საიდუმლოები და ძველი ვალები ზედაპირზე ამოდის.",
    backdrop: "https://picsum.photos/seed/harbor/1600/900",
  },
  {
    title: "The Last Cartographer",
    year: 2025,
    genre: "Adventure · Mystery",
    rating: 9.0,
    description:
      "უკანასკნელი რუკის შემქმნელი ეძებს ადგილს, რომელიც არცერთ რუკაზე არ არსებობს — და რომელიც, შესაძლოა, არც უნდა არსებობდეს.",
    backdrop: "https://picsum.photos/seed/cartographer/1600/900",
  },
  {
    title: "Neon Requiem",
    year: 2023,
    genre: "Action · Cyberpunk",
    rating: 7.9,
    description:
      "ნეონის ქალაქში ნაქირავები მკვლელი ბოლო დავალებას იღებს — საკუთარი წარსულის წაშლას ხელს ვერავინ უშლის.",
    backdrop: "https://picsum.photos/seed/neon/1600/900",
  },
  {
    title: "Quiet Frontier",
    year: 2025,
    genre: "Western · Drama",
    rating: 8.4,
    description:
      "მიტოვებულ სასაზღვრო ქალაქში მარტოხელა მცველი იცავს იმას, რაც ყველამ მიატოვა — და საკუთარ თავსაც.",
    backdrop: "https://picsum.photos/seed/frontier/1600/900",
  },
];

/* ---------- CAROUSEL (ფილტრით: ყველა / ფილმები / ეპიზოდები / ანიმაცია) ----------
   category: "movie" | "episode" | "animation"  */
const carouselItems = [
  { title: "Echoes of Tomorrow", year: 2025, rating: 8.7, category: "movie",     image: "https://picsum.photos/seed/c-echoes/500/750" },
  { title: "Crimson Harbor",     year: 2024, rating: 8.2, category: "movie",     image: "https://picsum.photos/seed/c-harbor/500/750" },
  { title: "Neon Requiem",       year: 2023, rating: 7.9, category: "movie",     image: "https://picsum.photos/seed/c-neon/500/750" },
  { title: "Quiet Frontier",     year: 2025, rating: 8.4, category: "movie",     image: "https://picsum.photos/seed/c-frontier/500/750" },

  { title: "Dark Tide · S2E4",   year: 2025, rating: 8.1, category: "episode",   image: "https://picsum.photos/seed/c-tide/500/750" },
  { title: "Hollow Crown · S1E8",year: 2024, rating: 8.6, category: "episode",   image: "https://picsum.photos/seed/c-crown/500/750" },
  { title: "Static · S3E1",      year: 2025, rating: 7.7, category: "episode",   image: "https://picsum.photos/seed/c-static/500/750" },
  { title: "The Vow · S1E6",     year: 2023, rating: 8.3, category: "episode",   image: "https://picsum.photos/seed/c-vow/500/750" },

  { title: "Starlight Kids",     year: 2025, rating: 8.9, category: "animation", image: "https://picsum.photos/seed/c-star/500/750" },
  { title: "Paper Dragons",      year: 2024, rating: 8.5, category: "animation", image: "https://picsum.photos/seed/c-dragon/500/750" },
  { title: "Lumen",              year: 2025, rating: 9.1, category: "animation", image: "https://picsum.photos/seed/c-lumen/500/750" },
  { title: "Tiny Galaxy",        year: 2023, rating: 8.0, category: "animation", image: "https://picsum.photos/seed/c-galaxy/500/750" },
];

/* ---------- COLLECTIONS (ფილტრით: Marvel / Netflix / DC / Universal) ----------
   collection: "marvel" | "netflix" | "dc" | "universal"  */
const collections = [
  { title: "Iron Pulse",        year: 2024, rating: 8.3, collection: "marvel",    image: "https://picsum.photos/seed/m-iron/500/750" },
  { title: "Web of Echoes",     year: 2025, rating: 8.7, collection: "marvel",    image: "https://picsum.photos/seed/m-web/500/750" },
  { title: "Stormbreaker",      year: 2023, rating: 8.0, collection: "marvel",    image: "https://picsum.photos/seed/m-storm/500/750" },
  { title: "Sentinel",          year: 2025, rating: 7.8, collection: "marvel",    image: "https://picsum.photos/seed/m-sentinel/500/750" },

  { title: "Crown & Country",   year: 2024, rating: 8.6, collection: "netflix",   image: "https://picsum.photos/seed/n-crown/500/750" },
  { title: "Midnight Diner",    year: 2025, rating: 8.4, collection: "netflix",   image: "https://picsum.photos/seed/n-diner/500/750" },
  { title: "Paper Streets",     year: 2023, rating: 7.9, collection: "netflix",   image: "https://picsum.photos/seed/n-paper/500/750" },
  { title: "The Quiet Ones",    year: 2025, rating: 8.2, collection: "netflix",   image: "https://picsum.photos/seed/n-quiet/500/750" },

  { title: "Gotham Nights",     year: 2024, rating: 8.8, collection: "dc",        image: "https://picsum.photos/seed/d-gotham/500/750" },
  { title: "Speed Force",       year: 2025, rating: 8.1, collection: "dc",        image: "https://picsum.photos/seed/d-speed/500/750" },
  { title: "Deep Tide",         year: 2023, rating: 7.7, collection: "dc",        image: "https://picsum.photos/seed/d-tide/500/750" },
  { title: "Amazon Steel",      year: 2025, rating: 8.5, collection: "dc",        image: "https://picsum.photos/seed/d-steel/500/750" },

  { title: "Lost Park",         year: 2024, rating: 8.0, collection: "universal", image: "https://picsum.photos/seed/u-park/500/750" },
  { title: "Fast Lane",         year: 2025, rating: 7.6, collection: "universal", image: "https://picsum.photos/seed/u-fast/500/750" },
  { title: "Bright Minions",    year: 2023, rating: 8.3, collection: "universal", image: "https://picsum.photos/seed/u-minions/500/750" },
  { title: "Oppen Light",       year: 2024, rating: 9.0, collection: "universal", image: "https://picsum.photos/seed/u-light/500/750" },
];

/* ---------- ბოლოს ნანახი ფილმები ---------- */
const recentlyWatched = [
  { title: "Echoes of Tomorrow", year: 2025, rating: 8.7, progress: 72, image: "https://picsum.photos/seed/r-echoes/500/750" },
  { title: "Gotham Nights",      year: 2024, rating: 8.8, progress: 45, image: "https://picsum.photos/seed/r-gotham/500/750" },
  { title: "Lumen",              year: 2025, rating: 9.1, progress: 90, image: "https://picsum.photos/seed/r-lumen/500/750" },
  { title: "Crimson Harbor",     year: 2024, rating: 8.2, progress: 30, image: "https://picsum.photos/seed/r-harbor/500/750" },
  { title: "Midnight Diner",     year: 2025, rating: 8.4, progress: 12, image: "https://picsum.photos/seed/r-diner/500/750" },
  { title: "Neon Requiem",       year: 2023, rating: 7.9, progress: 60, image: "https://picsum.photos/seed/r-neon/500/750" },
  { title: "Paper Dragons",      year: 2024, rating: 8.5, progress: 85, image: "https://picsum.photos/seed/r-dragon/500/750" },
  { title: "Quiet Frontier",     year: 2025, rating: 8.4, progress: 25, image: "https://picsum.photos/seed/r-frontier/500/750" },
];

/* ---------- SCROLL MOVIE — TikTok-style ვიდეოების მასივი ----------
   video: ფილმის მონაკვეთის ბმული (შეცვალე შენი ფაილებით)  */
const scrollVideos = [
  {
    title: "Echoes of Tomorrow",
    year: 2025,
    genre: "Sci-Fi · Thriller",
    rating: 8.7,
    description:
      "მომავლის ქალაქში დეტექტივი აღმოაჩენს, რომ მისი მოგონებები სხვისია. ეს არის ფინალური სცენის მონაკვეთი.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4",
  },
  {
    title: "Crimson Harbor",
    year: 2024,
    genre: "Drama · Crime",
    rating: 8.2,
    description:
      "პორტის ქალაქში ერთი ღამე ცვლის ყველაფერს. ოჯახური საიდუმლოები ზედაპირზე ამოდის.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4",
  },
  {
    title: "Neon Requiem",
    year: 2023,
    genre: "Action · Cyberpunk",
    rating: 7.9,
    description:
      "ნეონის ქალაქში ნაქირავები მკვლელი ბოლო დავალებას იღებს — წარსულის წაშლა ადვილი არ არის.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4",
  },
  {
    title: "The Last Cartographer",
    year: 2025,
    genre: "Adventure · Mystery",
    rating: 9.0,
    description:
      "უკანასკნელი რუკის შემქმნელი ეძებს ადგილს, რომელიც არცერთ რუკაზე არ არსებობს.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4",
  },
  {
    title: "Lumen",
    year: 2025,
    genre: "Animation · Family",
    rating: 9.1,
    description:
      "პატარა სინათლის ნაპერწკალი ეძებს გზას სახლისკენ უსასრულო სიბნელეში. ვიზუალურად განსაცვიფრებელი ანიმაცია.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4",
  },
  {
    title: "Quiet Frontier",
    year: 2025,
    genre: "Western · Drama",
    rating: 8.4,
    description:
      "მიტოვებულ სასაზღვრო ქალაქში მარტოხელა მცველი იცავს იმას, რაც ყველამ მიატოვა.",
    video: "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4",
  },
];
