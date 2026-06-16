/* =========================================================================
   card.js — საერთო ბარათის შაბლონი (იყენებს main.js და filter.js)
   PHP-ის render_card()-ის იდენტური მარკაპი.
   ========================================================================= */
(function (w) {
  "use strict";
  const STAR = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.8 5.9 21.4l1.4-6.8L2.2 9.9l6.9-.8L12 2z"/></svg>';
  const PLAY = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4l14 8-14 8V4z"/></svg>';

  function mediaUrl(path, base) {
    path = path || "";
    if (!path) return "";
    if (/^(https?:)?\/\//i.test(path) || path.indexOf("data:") === 0) return path;
    return (base || "") + "/" + String(path).replace(/^\/+/, "");
  }

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
  }

  /* item, labels (api-დან), opts: {base, badge, recent} */
  function cardHTML(item, labels, opts) {
    opts = opts || {};
    const base = opts.base || "";
    const typeLabel = (labels && labels.types && labels.types[item.type]) || "";
    const rating = (parseFloat(item.rating) || 0).toFixed(1);
    const poster = mediaUrl(item.poster, base);
    const href = base + "/movie.php?id=" + encodeURIComponent(item.id || "");
    let badge = "";
    if (opts.badge) badge = '<span class="card-badge">' + esc(opts.badge) + "</span>";

    let progress = "";
    let sub = esc(item.year) + (typeLabel ? " · " + esc(typeLabel) : "");
    if (opts.recent && parseInt(item.progress, 10) > 0) {
      const p = parseInt(item.progress, 10);
      progress = '<div class="card-progress"><span style="width:' + p + '%"></span></div>';
      sub = p + "% ნანახი · " + esc(item.year);
    }

    return (
      '<a class="card" href="' + esc(href) + '">' +
        '<div class="card-poster">' +
          badge +
          '<span class="card-rating">' + STAR + rating + "</span>" +
          '<img loading="lazy" src="' + esc(poster) + '" alt="' + esc(item.title) + '">' +
          '<div class="card-play"><span>' + PLAY + "</span></div>" +
          progress +
        "</div>" +
        '<div class="card-meta"><h3>' + esc(item.title) + "</h3>" +
        '<div class="card-sub">' + sub + "</div></div>" +
      "</a>"
    );
  }

  function skeleton() {
    return '<div class="skeleton-card"><div class="sk-poster sk-shimmer"></div><div class="sk-line sk-shimmer"></div><div class="sk-line short sk-shimmer"></div></div>';
  }

  w.Cinemata = { cardHTML, skeleton, mediaUrl, esc };
})(window);
