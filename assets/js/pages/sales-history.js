(function(){
  function initDT(){
    if (!window.jQuery || !$.fn.DataTable) return setTimeout(initDT, 100);
    if ($('#historyTable').length && !$.fn.DataTable.isDataTable('#historyTable')){
      $('#historyTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']]
      });
    }
  }
  document.addEventListener('DOMContentLoaded', initDT);
})();
