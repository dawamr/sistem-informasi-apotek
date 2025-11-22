(function(){
  function initDT(){
    if (!window.jQuery || !$.fn.DataTable) return setTimeout(initDT, 100);
    if ($('#shiftsTable').length && !$.fn.DataTable.isDataTable('#shiftsTable')){
      $('#shiftsTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'asc']]
      });
    }
  }
  document.addEventListener('DOMContentLoaded', initDT);
})();
