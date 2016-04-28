;(function () {
  document.addEventListener("DOMContentLoaded", function(event) {
    setTimeout(function () {
      var el = document.getElementById('loading-overlay');

      if (!el) {
        return false;
      }

      el.setAttribute('class', el.getAttribute('class')+' animated fadeOut');

      setTimeout(function () {
        el.remove();
      }, 1000);
    }, 1000);
  });
})();
