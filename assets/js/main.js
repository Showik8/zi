/* =========================================================================
   main.js — მთავარი გვერდის ინტერაქცია
   ========================================================================= */
(function () {
  "use strict";
  const $ = (s, ctx = document) => ctx.querySelector(s);
  const $$ = (s, ctx = document) => [...ctx.querySelectorAll(s)];
  const C = window.Cinemata;

  /* ---------- HEADER scroll + hamburger ---------- */
  const header = $("#header");
  if (header) {
    window.addEventListener("scroll", () => header.classList.toggle("is-scrolled", window.scrollY > 30));
  }
  const hamburger = $("#hamburger");
  const navMenu = $("#navMenu");
  const navBackdrop = $("#navBackdrop");
  function toggleMenu(open) {
    if (!navMenu) return;
    const isOpen = open ?? !navMenu.classList.contains("is-open");
    navMenu.classList.toggle("is-open", isOpen);
    hamburger.classList.toggle("is-open", isOpen);
    navBackdrop.classList.toggle("is-open", isOpen);
    hamburger.setAttribute("aria-expanded", String(isOpen));
    document.body.style.overflow = isOpen ? "hidden" : "";
  }
  if (hamburger) hamburger.addEventListener("click", () => toggleMenu());
  if (navBackdrop) navBackdrop.addEventListener("click", () => toggleMenu(false));
  if (navMenu) navMenu.querySelectorAll("a").forEach((a) => a.addEventListener("click", () => toggleMenu(false)));

  /* ---------- HERO სლაიდერი + dots ---------- */
  (function initHero() {
    const slidesEl = $("#heroSlides");
    const dotsEl = $("#heroDots");
    if (!slidesEl) return;
    const slides = [...slidesEl.children];
    if (slides.length <= 1) return;
    let current = 0, timer;

    if (dotsEl) {
      dotsEl.innerHTML = slides
        .map((_, i) => `<button class="dot ${i === 0 ? "is-active" : ""}" data-i="${i}" aria-label="სლაიდი ${i + 1}"></button>`)
        .join("");
    }
    const dots = dotsEl ? [...dotsEl.children] : [];

    function go(i) {
      current = (i + slides.length) % slides.length;
      slides.forEach((s, k) => s.classList.toggle("is-active", k === current));
      dots.forEach((d, k) => d.classList.toggle("is-active", k === current));
      const bg = slides[current].querySelector(".hero-bg");
      if (bg) { bg.style.animation = "none"; void bg.offsetWidth; bg.style.animation = ""; }
    }
    const next = () => go(current + 1);
    const prev = () => go(current - 1);
    const restart = () => { clearInterval(timer); timer = setInterval(next, 6000); };

    const nb = $("#heroNext"), pb = $("#heroPrev");
    if (nb) nb.addEventListener("click", () => { next(); restart(); });
    if (pb) pb.addEventListener("click", () => { prev(); restart(); });
    dots.forEach((d) => d.addEventListener("click", () => { go(+d.dataset.i); restart(); }));
    restart();
  })();

  /* ---------- უნივერსალური SLIDER (recently watched) ---------- */
  function Slider({ viewport, track, prevBtn, nextBtn, dotsEl }) {
    let page = 0, pages = 1, step = 0, perPage = 1, maxOffset = 0;
    function measure() {
      const card = track.querySelector(".card, .skeleton-card");
      if (!card) { pages = 1; return; }
      const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 18;
      step = card.getBoundingClientRect().width + gap;
      perPage = Math.max(1, Math.floor((viewport.clientWidth + gap) / step));
      pages = Math.max(1, Math.ceil(track.children.length / perPage));
      maxOffset = Math.max(0, track.scrollWidth - viewport.clientWidth);
      page = Math.min(Math.max(0, page), pages - 1);
    }
    function renderDots() {
      if (!dotsEl) return;
      dotsEl.innerHTML = "";
      for (let i = 0; i < pages; i++) {
        const d = document.createElement("button");
        d.className = "dot" + (i === page ? " is-active" : "");
        d.addEventListener("click", () => { page = i; apply(); });
        dotsEl.appendChild(d);
      }
    }
    function apply() {
      const offset = Math.min(page * perPage * step, maxOffset);
      track.style.transform = `translateX(${-offset}px)`;
      renderDots();
      [prevBtn, nextBtn].forEach((b) => b && (b.style.opacity = "1"));
      if (prevBtn && page <= 0) prevBtn.style.opacity = "0.4";
      if (nextBtn && page >= pages - 1) nextBtn.style.opacity = "0.4";
    }
    function go(dir) { page = Math.min(pages - 1, Math.max(0, page + dir)); apply(); }
    if (prevBtn) prevBtn.addEventListener("click", () => go(-1));
    if (nextBtn) nextBtn.addEventListener("click", () => go(1));
    let rT;
    window.addEventListener("resize", () => { clearTimeout(rT); rT = setTimeout(() => { measure(); apply(); }, 120); });
    return { refresh(reset = false) { if (reset) page = 0; measure(); apply(); } };
  }

  (function initRecent() {
    const track = $("#recentTrack");
    if (!track) return;
    const viewport = track.parentElement;
    const slider = Slider({ viewport, track, prevBtn: $("#recPrev"), nextBtn: $("#recNext"), dotsEl: $("#recentDots") });
    slider.refresh(true);
    // სურათების ჩატვირთვისას ხელახალი გაზომვა
    window.addEventListener("load", () => slider.refresh());
  })();

  /* ---------- COLLECTIONS — AJAX ფილტრი ---------- */
  (function initCollections() {
    const grid = $("#collectionsGrid");
    const filtersEl = $("#collectionFilters");
    if (!grid || !filtersEl) return;
    const endpoint = grid.dataset.endpoint;

    async function load(collection) {
      grid.innerHTML = Array.from({ length: 8 }, C.skeleton).join("");
      try {
        const url = endpoint + "?per_page=12&collections[]=" + encodeURIComponent(collection);
        const res = await fetch(url);
        const data = await res.json();
        const label = (data.labels.collections && data.labels.collections[collection]) || "";
        grid.innerHTML = data.items.length
          ? data.items.map((it) => C.cardHTML(it, data.labels, { base: data.baseUrl, badge: label })).join("")
          : '<p class="empty-note">ამ კოლექციაში ფილმები ვერ მოიძებნა.</p>';
      } catch (e) {
        grid.innerHTML = '<p class="empty-note">ჩატვირთვა ვერ მოხერხდა.</p>';
      }
    }

    filtersEl.addEventListener("click", (e) => {
      const btn = e.target.closest(".filter-btn");
      if (!btn) return;
      filtersEl.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      load(btn.dataset.filter);
    });

    load(grid.dataset.initial || (filtersEl.querySelector(".filter-btn") || {}).dataset?.filter || "");
  })();
})();
