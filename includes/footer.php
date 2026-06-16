<?php
/* includes/footer.php — საიტის ქვედა ნაწილი */
$bu = base_url();
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= e($bu) ?>/index.php" class="brand">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 7h20M2 17h20M7 2v20M17 2v20" stroke-linecap="round" /></svg>
          CINEM<span class="brand-dot">A</span>TA
        </a>
        <p>ფილმები, სერიალები და მოკლე მონაკვეთები ერთ სივრცეში. ნახე, აღმოაჩინე და გააგრძელე ყურება ნებისმიერ მოწყობილობაზე.</p>
        <div class="footer-social">
          <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" /></svg></a>
          <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="4" /><path d="M10 9l5 3-5 3V9z" fill="currentColor" stroke="none" /></svg></a>
          <a href="#" aria-label="X"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3h3l-7 8 8 10h-6l-5-6-5 6H1l8-9L1 3h6l4 5 6-5z" /></svg></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>ნახვა</h4>
        <a href="<?= e($bu) ?>/filter.php">ფილმები</a>
        <a href="<?= e($bu) ?>/filter.php?type=episode">ეპიზოდები</a>
        <a href="<?= e($bu) ?>/filter.php?type=animation">ანიმაცია</a>
        <a href="<?= e($bu) ?>/scroll-movie.php">Scroll Movie</a>
      </div>
      <div class="footer-col">
        <h4>კოლექციები</h4>
        <a href="<?= e($bu) ?>/filter.php?collections[]=marvel">Marvel</a>
        <a href="<?= e($bu) ?>/filter.php?collections[]=netflix">Netflix</a>
        <a href="<?= e($bu) ?>/filter.php?collections[]=dc">DC</a>
        <a href="<?= e($bu) ?>/filter.php?collections[]=universal">Universal</a>
      </div>
      <div class="footer-col">
        <h4>კომპანია</h4>
        <a href="#">ჩვენ შესახებ</a>
        <a href="#">დახმარება</a>
        <a href="#">კონფიდენციალურობა</a>
        <a href="<?= e($bu) ?>/admin/">ადმინ პანელი</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> CINEMATA. ყველა უფლება დაცულია.</span>
      <span>დამზადებულია ❤️-ით კინოს მოყვარულთათვის</span>
    </div>
  </div>
</footer>
