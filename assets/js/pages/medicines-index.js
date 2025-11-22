(function(){
  function initDT(){
    if (!window.jQuery || !$.fn.DataTable) return setTimeout(initDT, 100);
    if ($('#medicinesTable').length && !$.fn.DataTable.isDataTable('#medicinesTable')){
      $('#medicinesTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[1, 'asc']]
      });
    }
  }
  document.addEventListener('DOMContentLoaded', initDT);
})();
