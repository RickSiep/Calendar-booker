// Vars to be used
let scripts = document.getElementsByTagName("script");
let src = scripts[scripts.length-1].src;
let directory = src.replace('calendar.js', '');
let calendarEl = document.getElementById('calendar');

if(calendarEl) {
    document.addEventListener('DOMContentLoaded', function() {
        let calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'dayGrid' ],
            displayEventTime: false,
            aspectRatio: 1.35,
            eventBackgroundColor: "#80a2c3",
            events: calendarScript.pluginsUrl + '/js/load-events.php',
        });
        calendar.render();
    });
}
