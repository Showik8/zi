    </main>
  </div>
  <div class="admin-backdrop" id="adminBackdrop"></div>
  <script>
    (function () {
      var burger = document.getElementById('adminBurger');
      var side = document.getElementById('adminSide');
      var bd = document.getElementById('adminBackdrop');
      function toggle(open){ side.classList.toggle('is-open', open); bd.classList.toggle('is-open', open); }
      if (burger) burger.addEventListener('click', function(){ toggle(!side.classList.contains('is-open')); });
      if (bd) bd.addEventListener('click', function(){ toggle(false); });

      // ვიდეო/სურათის წყაროს გადართვა (url <-> file)
      document.querySelectorAll('[data-srcswitch]').forEach(function(group){
        var radios = group.querySelectorAll('input[type=radio]');
        var panels = group.querySelectorAll('[data-panel]');
        function sync(){
          var val = group.querySelector('input[type=radio]:checked');
          val = val ? val.value : '';
          panels.forEach(function(p){ p.hidden = (p.getAttribute('data-panel') !== val); });
        }
        radios.forEach(function(r){ r.addEventListener('change', sync); });
        sync();
      });

      // ფაილის არჩევის სახელის ჩვენება
      document.querySelectorAll('input[type=file]').forEach(function(inp){
        inp.addEventListener('change', function(){
          var lbl = inp.parentElement.querySelector('.file-name');
          if (lbl) lbl.textContent = inp.files.length ? inp.files[0].name : 'ფაილი არ არის არჩეული';
        });
      });
    })();
  </script>
</body>
</html>
