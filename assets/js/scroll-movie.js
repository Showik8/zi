/* =========================================================================
   scroll-movie.js — reels ლოგიკა (DOM უკვე დარენდერებულია PHP-დან)
   ========================================================================= */
(function () {
  "use strict";
  const playSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>';
  const pauseSVG = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>';
  const volOn = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a9 9 0 0 1 0 14"/></svg>';
  const volOff = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M22 9l-6 6M16 9l6 6"/></svg>';

  const reelsEl = document.getElementById("reels");
  if (!reelsEl) return;
  const reels = [...reelsEl.querySelectorAll(".reel[data-index]")];
  let isMuted = true;

  function applyMute() {
    reels.forEach((reel) => {
      const main = reel.querySelector(".reel-video");
      if (!main) return;
      main.muted = isMuted;
      const ico = reel.querySelector(".rail-btn.mute .ico");
      const btn = reel.querySelector(".rail-btn.mute");
      if (ico) ico.innerHTML = isMuted ? volOff : volOn;
      if (btn) btn.classList.toggle("is-on", !isMuted);
    });
  }

  function setActive(reel) {
    reels.forEach((r) => {
      const main = r.querySelector(".reel-video");
      const blur = r.querySelector(".reel-blur");
      if (!main) return;
      if (r === reel) {
        main.muted = isMuted;
        main.play().catch(() => {});
        if (blur) blur.play().catch(() => {});
        r.classList.remove("is-paused");
      } else {
        main.pause();
        if (blur) blur.pause();
      }
    });
  }

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

  reels.forEach((reel) => {
    const main = reel.querySelector(".reel-video");
    const blur = reel.querySelector(".reel-blur");
    const bar = reel.querySelector(".reel-progress span");
    const badge = reel.querySelector(".reel-pausebadge");
    if (!main) return;

    const tap = reel.querySelector(".reel-tap");
    if (tap) tap.addEventListener("click", () => {
      if (main.paused) {
        main.play().catch(() => {});
        if (blur) blur.play().catch(() => {});
        reel.classList.remove("is-paused");
        if (badge) badge.innerHTML = playSVG;
      } else {
        main.pause();
        if (blur) blur.pause();
        reel.classList.add("is-paused");
        if (badge) badge.innerHTML = pauseSVG;
      }
    });

    main.addEventListener("timeupdate", () => {
      if (main.duration && bar) bar.style.width = (main.currentTime / main.duration) * 100 + "%";
    });

    const muteBtn = reel.querySelector(".rail-btn.mute");
    if (muteBtn) muteBtn.addEventListener("click", () => {
      isMuted = !isMuted;
      applyMute();
      if (!main.paused) main.play().catch(() => {});
    });

    const likeBtn = reel.querySelector(".rail-btn.like");
    if (likeBtn) likeBtn.addEventListener("click", (e) => e.currentTarget.classList.toggle("is-on"));
  });

  applyMute();
  if (reels[0]) setActive(reels[0]);

  window.addEventListener("keydown", (e) => {
    if (e.key !== "ArrowDown" && e.key !== "ArrowUp") return;
    const h = window.innerHeight;
    const idx = Math.round(reelsEl.scrollTop / h);
    const next = e.key === "ArrowDown" ? idx + 1 : idx - 1;
    if (reels[next]) reelsEl.scrollTo({ top: next * h, behavior: "smooth" });
  });
})();
