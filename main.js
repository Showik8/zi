/* =========================================================================
   main.js — ყველა ინტერაქცია
   ========================================================================= */
(function () {
  "use strict";

  /* ---------- პატარა SVG იკონები ---------- */
  const starSVG = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5.9 21.4l1.4-6.8L2.2 9.9l6.9-.8L12 2z"/></svg>`;
  const playSVG = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>`;
  const $ = (s, ctx = document) => ctx.querySelector(s);

  /* =======================================================================
     1) HEADER — scroll ფონი + HAMBURGER მენიუ
     ======================================================================= */
  const header = $("#header");
  window.addEventListener("scroll", () => {
    header.classList.toggle("is-scrolled", window.scrollY > 30);
  });

  const hamburger = $("#hamburger");
  const navMenu = $("#navMenu");
  const navBackdrop = $("#navBackdrop");

  function toggleMenu(open) {
    const isOpen = open ?? !navMenu.classList.contains("is-open");
    navMenu.classList.toggle("is-open", isOpen);
    hamburger.classList.toggle("is-open", isOpen);
    navBackdrop.classList.toggle("is-open", isOpen);
    hamburger.setAttribute("aria-expanded", String(isOpen));
    document.body.style.overflow = isOpen ? "hidden" : "";
  }
  hamburger.addEventListener("click", () => toggleMenu());
  navBackdrop.addEventListener("click", () => toggleMenu(false));
  navMenu.querySelectorAll("a").forEach((a) =>
    a.addEventListener("click", () => toggleMenu(false))
  );

  /* =======================================================================
     ბარათის შაბლონები
     ======================================================================= */
  function cardHTML(item, opts = {}) {
    return `
      <article class="movie-card ${opts.recent ? "recent-card" : ""}">
        <div class="poster">
          ${opts.badge ? `<span class="collection-badge">${opts.badge}</span>` : ""}
          <span class="rating-chip">${starSVG}${item.rating.toFixed(1)}</span>
          <img src="${item.image}" alt="${item.title}" loading="lazy">
          <div class="play-overlay"><span>${playSVG}</span></div>
          ${opts.recent ? `<div class="progress" style="width:${item.progress}%"></div>` : ""}
        </div>
        <div class="meta">
          <h3>${item.title}</h3>
          <div class="sub">${opts.recent ? item.progress + "% ნანახი · " : ""}${item.year}</div>
        </div>
      </article>`;
  }

  function skeletonHTML() {
    return `<div class="skeleton-card">
      <div class="sk-poster sk-shimmer"></div>
      <div class="sk-line sk-shimmer"></div>
      <div class="sk-line short sk-shimmer"></div>
    </div>`;
  }

  /* =======================================================================
     2) HERO — სლაიდერი (მარცხ/მარჯ + dots + auto)
     ======================================================================= */
  (function initHero() {
    const slidesEl = $("#heroSlides");
    const dotsEl = $("#heroDots");
    let current = 0;
    let timer;

    slidesEl.innerHTML = heroMovies
      .map(
        (m, i) => `
      <div class="hero-slide ${i === 0 ? "is-active" : ""}">
        <div class="hero-bg" style="background-image:url('${m.backdrop}')"></div>
        <div class="container">
          <div class="hero-content">
            <span class="hero-tag">გამორჩეული</span>
            <h1 class="hero-title">${m.title}</h1>
            <div class="hero-meta">
              <span class="hero-rating">${starSVG}${m.rating}</span>
              <span class="pill">${m.year}</span>
              <span class="pill">${m.genre}</span>
            </div>
            <p class="hero-desc">${m.description}</p>
            <div class="hero-actions">
              <a class="btn-primary" href="scroll-movie.html">${playSVG} ყურება</a>
              <a class="btn-ghost" href="#">+ სიაში დამატება</a>
            </div>
          </div>
        </div>
      </div>`
      )
      .join("");

    dotsEl.innerHTML = heroMovies
      .map((_, i) => `<button class="dot ${i === 0 ? "is-active" : ""}" data-i="${i}" aria-label="სლაიდი ${i + 1}"></button>`)
      .join("");

    const slides = [...slidesEl.children];
    const dots = [...dotsEl.children];

    function go(i) {
      current = (i + slides.length) % slides.length;
      slides.forEach((s, k) => s.classList.toggle("is-active", k === current));
      dots.forEach((d, k) => d.classList.toggle("is-active", k === current));
      // bg zoom ანიმაციის რესტარტი
      const bg = slides[current].querySelector(".hero-bg");
      bg.style.animation = "none";
      void bg.offsetWidth;
      bg.style.animation = "";
    }
    function next() { go(current + 1); }
    function prev() { go(current - 1); }
    function restart() { clearInterval(timer); timer = setInterval(next, 6000); }

    $("#heroNext").addEventListener("click", () => { next(); restart(); });
    $("#heroPrev").addEventListener("click", () => { prev(); restart(); });
    dots.forEach((d) =>
      d.addEventListener("click", () => { go(+d.dataset.i); restart(); })
    );
    restart();
  })();

  /* =======================================================================
     უნივერსალური SLIDER (carousel + recently watched)
     ======================================================================= */
  function Slider({ viewport, track, prevBtn, nextBtn, dotsEl }) {
    let page = 0, pages = 1, step = 0, perPage = 1, maxOffset = 0;

    function measure() {
      const card = track.querySelector(".movie-card, .skeleton-card");
      if (!card) { pages = 1; return; }
      const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 18;
      step = card.getBoundingClientRect().width + gap;
      perPage = Math.max(1, Math.floor((viewport.clientWidth + gap) / step));
      pages = Math.max(1, Math.ceil(track.children.length / perPage));
      maxOffset = Math.max(0, track.scrollWidth - viewport.clientWidth);
      if (page > pages - 1) page = pages - 1;
      if (page < 0) page = 0;
    }
    function renderDots() {
      if (!dotsEl) return;
      dotsEl.innerHTML = "";
      for (let i = 0; i < pages; i++) {
        const d = document.createElement("button");
        d.className = "dot" + (i === page ? " is-active" : "");
        d.setAttribute("aria-label", `გვერდი ${i + 1}`);
        d.addEventListener("click", () => { page = i; apply(); });
        dotsEl.appendChild(d);
      }
    }
    function apply() {
      const offset = Math.min(page * perPage * step, maxOffset);
      track.style.transform = `translateX(${-offset}px)`;
      renderDots();
      [prevBtn, nextBtn].forEach((b) => (b.style.opacity = "1"));
      if (page <= 0) prevBtn.style.opacity = "0.4";
      if (page >= pages - 1) nextBtn.style.opacity = "0.4";
    }
    function go(dir) { page = Math.min(pages - 1, Math.max(0, page + dir)); apply(); }

    prevBtn.addEventListener("click", () => go(-1));
    nextBtn.addEventListener("click", () => go(1));
    let rT;
    window.addEventListener("resize", () => {
      clearTimeout(rT);
      rT = setTimeout(() => { measure(); apply(); }, 120);
    });

    return {
      refresh(reset = false) { if (reset) page = 0; measure(); apply(); },
    };
  }

  /* =======================================================================
     3) CAROUSEL — ფილტრი + skeleton loader
     ======================================================================= */
  (function initCarousel() {
    const track = $("#carouselTrack");
    const viewport = track.parentElement;
    const filtersEl = $("#carouselFilters");

    const slider = Slider({
      viewport,
      track,
      prevBtn: $("#carPrev"),
      nextBtn: $("#carNext"),
      dotsEl: $("#carouselDots"),
    });

    function render(filter) {
      const items =
        filter === "all"
          ? carouselItems
          : carouselItems.filter((x) => x.category === filter);

      // 1) skeleton loader
      track.style.transform = "translateX(0)";
      track.innerHTML = Array.from({ length: 6 }, skeletonHTML).join("");
      slider.refresh(true);

      // 2) რეალური item-ები მცირე დაყოვნებით
      setTimeout(() => {
        track.innerHTML = items.length
          ? items.map((x) => cardHTML(x)).join("")
          : `<p class="empty-note">ამ კატეგორიაში ფილმები ვერ მოიძებნა.</p>`;
        slider.refresh(true);
      }, 700);
    }

    filtersEl.addEventListener("click", (e) => {
      const btn = e.target.closest(".filter-btn");
      if (!btn) return;
      filtersEl.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      render(btn.dataset.filter);
    });

    render("all");
  })();

  /* =======================================================================
     4) COLLECTIONS — ფილტრით (Marvel / Netflix / DC / Universal)
     ======================================================================= */
  (function initCollections() {
    const grid = $("#collectionsGrid");
    const filtersEl = $("#collectionFilters");
    const labels = { marvel: "Marvel", netflix: "Netflix", dc: "DC", universal: "Universal" };

    function render(filter) {
      const items = collections.filter((x) => x.collection === filter);
      // მოკლე skeleton
      grid.innerHTML = Array.from({ length: 4 }, skeletonHTML).join("");
      setTimeout(() => {
        grid.innerHTML = items
          .map((x) => cardHTML(x, { badge: labels[x.collection] }))
          .join("");
      }, 550);
    }

    filtersEl.addEventListener("click", (e) => {
      const btn = e.target.closest(".filter-btn");
      if (!btn) return;
      filtersEl.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      render(btn.dataset.filter);
    });

    render("marvel");
  })();

  /* =======================================================================
     5) RECENTLY WATCHED — slider + dots + progress
     ======================================================================= */
  (function initRecent() {
    const track = $("#recentTrack");
    const viewport = track.parentElement;

    track.innerHTML = recentlyWatched
      .map((x) => cardHTML(x, { recent: true }))
      .join("");

    const slider = Slider({
      viewport,
      track,
      prevBtn: $("#recPrev"),
      nextBtn: $("#recNext"),
      dotsEl: $("#recentDots"),
    });
    slider.refresh(true);
  })();
})();
