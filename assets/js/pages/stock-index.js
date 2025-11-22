(function(){
  function initDT(){
    if (!window.jQuery || !$.fn.DataTable) return setTimeout(initDT, 100);
    if ($('#stockLogsTable').length && !$.fn.DataTable.isDataTable('#stockLogsTable')){
      $('#stockLogsTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        order: [[0, 'desc']]
      });
    }
  }
  document.addEventListener('DOMContentLoaded', initDT);
})();
