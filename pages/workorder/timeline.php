<?php

date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');
$db = new DB();
$workorder = new Workorder($db);


?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get the current date
    let now = new Date();
    // Format the date as "YYYY-MM-DD"
    let formattedDate = now.toISOString().slice(0, 10);
    
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'nl',  
      initialDate: formattedDate,
      editable: false, // enable draggable events
      selectable: true,
      aspectRatio: 3.6,
      headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'resourceTimelineDay,resourceTimelineThreeDays,timeGridWeek,dayGridMonth,listWeek'
      },
      initialView: 'resourceTimelineDay',
      views: {
        resourceTimelineThreeDays: {
          type: 'resourceTimeline',
          duration: { days: 7 },
          buttonText: '7 dagen'
        }
      },
      resourceAreaHeaderContent: 'Machines',
      resources: [
        { id: '1', title: 'Machine 1' },
        { id: '2', title: 'Machine 2', eventColor: 'green' },
        { id: '3', title: 'Machine 3', eventColor: 'orange' },
        { id: '4', title: 'Machine 4', eventColor: 'red' },
        { id: '5', title: 'Machine 5' },
        { id: '6', title: 'Machine 6', eventColor: 'red' },
        { id: '7', title: 'Machine 7' },
        { id: '8', title: 'Machine 8' },
        { id: '9', title: 'Machine 9' }
      ],
      events: <?php echo $workorder->getWorkordersJson(); ?>
    });

    calendar.render();
  });

</script>


<div id='calendar'></div>