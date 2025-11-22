(function(){
  function initCalendar(){
    if (!window.FullCalendar || !document.getElementById('calendar')) return setTimeout(initCalendar, 100);
    var el = document.getElementById('calendar');
    var params = new URLSearchParams(window.location.search);
    var month = params.get('month');
    var initialDate = month ? (month + '-01') : undefined;

    var calendar = new FullCalendar.Calendar(el, {
      initialView: 'dayGridMonth',
      initialDate: initialDate,
      height: 'auto',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,dayGridDay'
      },
      navLinks: true,
      selectable: false,
      events: function(fetchInfo, success, failure){
        var url = (window.BASE_URL || '/') + 'shifts/events';
        var qs = '?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr);
        fetch(url + qs)
          .then(function(r){ return r.json(); })
          .then(success)
          .catch(failure);
      }
    });
    calendar.render();
  }
  document.addEventListener('DOMContentLoaded', initCalendar);
})();
