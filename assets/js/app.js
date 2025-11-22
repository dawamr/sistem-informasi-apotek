// Global app scripts
(function(){
  // Enable Bootstrap Tooltips & Popovers globally
  document.addEventListener('DOMContentLoaded', function(){
    if (window.bootstrap) {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (el) { try { new bootstrap.Tooltip(el); } catch(e) {} });

      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      popoverTriggerList.forEach(function (el) { try { new bootstrap.Popover(el); } catch(e) {} });
    }
  });
})();
