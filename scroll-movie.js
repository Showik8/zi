/* =========================================================================
   scroll-movie.js — TikTok-style reels ლოგიკა
   ========================================================================= */
(function () {
  "use strict";

  const starSVG = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5.9 21.4l1.4-6.8L2.2 9.9l6.9-.8L12 2z"/></svg>`;
  const playSVG = `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>`;
  const pauseSVG = `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>`;
  const heartSVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.4-9C1 9 2.5 5.5 6 5.5c2 0 3.2 1.2 4 2.3.8-1.1 2-2.3 4-2.3 3.5 0 5 3.5 3.4 6.5C19 16.4 12 21 12 21z"/></svg>`;
  const commentSVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a8 8 0 0 1-11.5 7.2L3 21l1.8-6.5A8 8 0 1 1 21 12z"/></svg>`;
  const shareSVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v14"/></svg>`;
  const volOn = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a9 9 0 0 1 0 14"/></svg>`;
  const volOff = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M22 9l-6 6M16 9l6 6"/></svg>`;
  const chevronSVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>`;

  const reelsEl = document.getElementById("reels");
  let isMuted = true; // autoplay-ისთვის თავიდან გაჩუმებული

  const poster = (t) => `https://picsum.photos/seed/v-${encodeURIComponent(t)}/720/1280`;

  /* ---------- reel-ების რენდერი მასივიდან ---------- */
  reelsEl.innerHTML = scrollVideos
    .map(
      (v, i) => `
    <section class="reel" data-index="${i}">
      <video class="reel-blur" muted loop playsinline preload="none" poster="${poster(v.title)}" src="${v.video}"></video>
      <video class="reel-video" loop playsinline preload="none" muted poster="${poster(v.title)}" src="${v.video}"></video>
      <div class="reel-scrim"></div>
      <button class="reel-tap" aria-label="დაპაუზება / გაშვება"></button>
      <div class="reel-pausebadge">${playSVG}</div>

      <div class="reel-info">
        <div class="badges">
          <span class="reel-rating">${starSVG}${v.rating.toFixed(1)}</span>
          <span class="chip">${v.year}</span>
          <span class="chip">${v.genre}</span>
        </div>
        <h2>${v.title}</h2>
        <p>${v.description}</p>
        <a class="watch" href="#">${playSVG} სრულად ნახვა</a>
      </div>

      <div class="reel-rail">
        <button class="rail-btn like" aria-label="მოწონება"><span class="ico">${heartSVG}</span><span class="lbl">${(12 + i * 3)}K</span></button>
        <button class="rail-btn" aria-label="კომენტარი"><span class="ico">${commentSVG}</span><span class="lbl">${(1 + i)}.2K</span></button>
        <button class="rail-btn" aria-label="გაზიარება"><span class="ico">${shareSVG}</span><span class="lbl">გაზიარება</span></button>
        <button class="rail-btn mute" aria-label="ხმა"><span class="ico">${volOff}</span><span class="lbl">ხმა</span></button>
      </div>

      <div class="reel-progress"><span></span></div>
      ${i === 0 ? `<div class="scroll-hint">დაასქროლე ${chevronSVG}</div>` : ""}
    </section>`
    )
    .join("");

  const reels = [...reelsEl.querySelectorAll(".reel")];

  /* ---------- ხმის გადართვა ყველა reel-ზე ---------- */
  function applyMute() {
    reels.forEach((reel) => {
      const main = reel.querySelector(".reel-video");
      main.muted = isMuted;
      const ico = reel.querySelector(".rail-btn.mute .ico");
      const btn = reel.querySelector(".rail-btn.mute");
      ico.innerHTML = isMuted ? volOff : volOn;
      btn.classList.toggle("is-on", !isMuted);
    });
  }

  /* ---------- აქტიური reel-ის გაშვება, დანარჩენების პაუზა ---------- */
  function setActive(reel) {
    reels.forEach((r) => {
      const main = r.querySelector(".reel-video");
      const blur = r.querySelector(".reel-blur");
      if (r === reel) {
        main.muted = isMuted;
        main.play().catch(() => {});
        blur.play().catch(() => {});
        r.classList.remove("is-paused");
      } else {
        main.pause();
        blur.pause();
      }
    });
  }

  /* ---------- IntersectionObserver — რომელი reel ჩანს ეკრანზე ---------- */
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) {
          setActive(entry.target);
          const hint = entry.target.querySelector(".scroll-hint");
          if (hint && entry.target.dataset.index !== "0") hint.remove();
        }
      });
    },
    { threshold: [0, 0.6, 1] }
  );
  reels.forEach((r) => io.observe(r));

  /* ---------- თითოეული reel-ის კონტროლები ---------- */
  reels.forEach((reel) => {
    const main = reel.querySelector(".reel-video");
    const blur = reel.querySelector(".reel-blur");
    const bar = reel.querySelector(".reel-progress span");
    const badge = reel.querySelector(".reel-pausebadge");

    // tap → play/pause
    reel.querySelector(".reel-tap").addEventListener("click", () => {
      if (main.paused) {
        main.play().catch(() => {});
        blur.play().catch(() => {});
        reel.classList.remove("is-paused");
        badge.innerHTML = playSVG;
      } else {
        main.pause();
        blur.pause();
        reel.classList.add("is-paused");
        badge.innerHTML = pauseSVG;
      }
    });

    // პროგრესი
    main.addEventListener("timeupdate", () => {
      if (main.duration) bar.style.width = (main.currentTime / main.duration) * 100 + "%";
    });

    // ხმის ღილაკი
    reel.querySelector(".rail-btn.mute").addEventListener("click", () => {
      isMuted = !isMuted;
      applyMute();
      if (!main.paused) main.play().catch(() => {});
    });

    // მოწონება
    reel.querySelector(".rail-btn.like").addEventListener("click", (e) => {
      e.currentTarget.classList.toggle("is-on");
    });
  });

  applyMute();

  // პირველი reel-ის გაშვება ჩატვირთვისთანავე
  if (reels[0]) setActive(reels[0]);

  // კლავიატურით ნავიგაცია (↑ / ↓)
  window.addEventListener("keydown", (e) => {
    if (e.key !== "ArrowDown" && e.key !== "ArrowUp") return;
    const h = window.innerHeight;
    const idx = Math.round(reelsEl.scrollTop / h);
    const next = e.key === "ArrowDown" ? idx + 1 : idx - 1;
    if (reels[next]) reelsEl.scrollTo({ top: next * h, behavior: "smooth" });
  });
})();
