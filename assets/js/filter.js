/* =========================================================================
   filter.js — ფილტრი + ძიება + infinite scroll + lazy loading
   ========================================================================= */
(function () {
  "use strict";
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const C = window.Cinemata;

  const page = $("#filterPage");
  const endpoint = page.dataset.endpoint;
  const base = page.dataset.base;

  const form = $("#filterForm");
  const grid = $("#resultsGrid");
  const loader = $("#resultsLoader");
  const endEl = $("#resultsEnd");
  const sentinel = $("#sentinel");
  const countEl = $("#resultsCount");
  const searchInput = $("#searchInput");
  const sortSelect = $("#sortSelect");
  const resultsPane = $(".filter-results");

  let state = { page: 1, hasMore: true, loading: false, labels: null };
  const PER_PAGE = 12;

  /* ---------- query აწყობა ფორმიდან ---------- */
  function buildQuery(pageNum) {
    const params = new URLSearchParams();
    const data = new FormData(form);
    // checkbox ჯგუფები
    for (const [k, v] of data.entries()) {
      if (k.endsWith("[]")) params.append(k, v);
    }
    // "ყველა" ტიპი — არ ვაგზავნით კონკრეტულ ტიპებს
    if ($("#typeAll").checked) {
      // წავშალოთ types[]
      [...params.keys()].forEach((k) => { if (k === "types[]") params.delete(k); });
    }
    // sliders
    params.set("rating_min", form.rating_min.value);
    params.set("rating_max", form.rating_max.value);
    params.set("year_min", form.year_min.value);
    params.set("year_max", form.year_max.value);
    // search + sort + paging
    if (searchInput.value.trim()) params.set("q", searchInput.value.trim());
    params.set("sort", sortSelect.value);
    params.set("page", pageNum);
    params.set("per_page", PER_PAGE);
    return params.toString();
  }

  /* ---------- ჩატვირთვა ---------- */
  async function load(reset) {
    if (state.loading) return;
    if (reset) {
      state.page = 1;
      state.hasMore = true;
      grid.innerHTML = "";
      endEl.hidden = true;
    }
    if (!state.hasMore) return;

    state.loading = true;
    loader.hidden = false;
    try {
      const res = await fetch(endpoint + "?" + buildQuery(state.page));
      const data = await res.json();
      state.labels = data.labels;

      if (reset) {
        countEl.textContent = data.total + " შედეგი";
      }
      if (state.page === 1 && data.items.length === 0) {
        grid.innerHTML = '<p class="empty-note">შედეგი ვერ მოიძებნა. შეცვალე ფილტრი ან ძიება.</p>';
      } else {
        appendItems(data.items, data.labels);
      }
      state.hasMore = data.hasMore;
      state.page += 1;
      endEl.hidden = state.hasMore || data.total === 0;
    } catch (e) {
      countEl.textContent = "ჩატვირთვის შეცდომა";
    } finally {
      state.loading = false;
      loader.hidden = true;
    }
  }

  /* ---------- ბარათების დამატება (lazy mount ფრაგმენტებად) ---------- */
  function appendItems(items, labels) {
    const frag = document.createDocumentFragment();
    items.forEach((it) => {
      const label = (labels.collections && labels.collections[it.collection]) || "";
      const wrap = document.createElement("div");
      wrap.innerHTML = C.cardHTML(it, labels, { base, badge: label });
      const el = wrap.firstChild;
      frag.appendChild(el);
    });
    grid.appendChild(frag);
  }

  /* ---------- infinite scroll (IntersectionObserver) ---------- */
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((en) => {
        if (en.isIntersecting && state.hasMore && !state.loading) load(false);
      });
    },
    { root: resultsPane, rootMargin: "400px 0px" }
  );
  io.observe(sentinel);

  /* ---------- debounce ---------- */
  function debounce(fn, ms) {
    let t;
    return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
  }
  const reload = debounce(() => load(true), 350);

  /* ---------- ფილტრის მოვლენები ---------- */
  searchInput.addEventListener("input", reload);
  sortSelect.addEventListener("change", () => load(true));

  // "ყველა" და კონკრეტული ტიპები ერთმანეთს გამორიცხავენ
  const typeAll = $("#typeAll");
  const typeBoxes = $$('input[name="types[]"]:not(#typeAll)');
  typeAll.addEventListener("change", () => {
    if (typeAll.checked) typeBoxes.forEach((b) => (b.checked = false));
    reload();
  });
  typeBoxes.forEach((b) =>
    b.addEventListener("change", () => {
      if (b.checked) typeAll.checked = false;
      if (!typeBoxes.some((x) => x.checked)) typeAll.checked = true;
      reload();
    })
  );

  // სხვა checkbox-ები
  $$('input[type="checkbox"]', form).forEach((cb) => {
    if (cb.name === "types[]") return;
    cb.addEventListener("change", reload);
  });

  /* ---------- dual range sliders ---------- */
  function setupRange(name, outId, fmt) {
    const wrap = $(`.frange[data-range="${name}"]`);
    const lo = wrap.children[0];
    const hi = wrap.children[1];
    const out = $("#" + outId);
    function clamp() {
      let a = parseFloat(lo.value), b = parseFloat(hi.value);
      if (a > b) { [a, b] = [b, a]; lo.value = a; hi.value = b; }
      out.textContent = fmt(a) + " – " + fmt(b);
    }
    lo.addEventListener("input", () => { clamp(); });
    hi.addEventListener("input", () => { clamp(); });
    lo.addEventListener("change", () => load(true));
    hi.addEventListener("change", () => load(true));
    clamp();
  }
  setupRange("rating", "ratingOut", (v) => Number(v).toFixed(1));
  setupRange("year", "yearOut", (v) => String(Math.round(v)));

  /* ---------- apply / reset ---------- */
  $("#applyBtn").addEventListener("click", () => { load(true); closeSidebar(); });
  $("#resetBtn").addEventListener("click", () => {
    form.reset();
    typeAll.checked = true;
    typeBoxes.forEach((b) => (b.checked = false));
    searchInput.value = "";
    // sliders reset (მნიშვნელობები defaultValue-ზე + ლეიბლების განახლება)
    $$('.frange input[type="range"]').forEach((r) => (r.value = r.defaultValue));
    $("#ratingOut").textContent = Number(form.rating_min.value).toFixed(1) + " – " + Number(form.rating_max.value).toFixed(1);
    $("#yearOut").textContent = form.year_min.value + " – " + form.year_max.value;
    load(true);
  });

  /* ---------- მობილური sidebar ---------- */
  const sidebar = $("#filterSidebar");
  const backdrop = $("#sidebarBackdrop");
  function openSidebar() { sidebar.classList.add("is-open"); backdrop.classList.add("is-open"); }
  function closeSidebar() { sidebar.classList.remove("is-open"); backdrop.classList.remove("is-open"); }
  $("#mobToggle").addEventListener("click", openSidebar);
  backdrop.addEventListener("click", closeSidebar);

  /* ---------- პირველი ჩატვირთვა ---------- */
  load(true);
})();
