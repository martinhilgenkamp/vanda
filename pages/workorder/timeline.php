<?php

date_default_timezone_set("Europe/Amsterdam");
require_once('inc/class/class.workorder.php');
require_once('inc/class/class.user.php');




$workorder = new Workorder();
$um = new UserManager();


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
      editable: true, // enable draggable events
      eventClick: function(info) {
        Swal.fire({
                  title: "Opdracht bewerken?",
                  text: "wil je deze opdracht Bewerken?",
                  icon: "question",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Bewerk",
                  cancelButtonText: "Annuleer"
              }).then((result) => {
              if (result.isConfirmed) {
               window.location.href = 'index.php?page=workorder/editworkorder&id=' + info.event.id;
              } else {
                info.revert();
              }
            });
        },
        eventDrop: function(info) {
    Swal.fire({
        title: "Opdracht verplaatsen?",
        text: "Wil je deze opdracht verplaatsen?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Verplaats"
    }).then((result) => {
        if (result.isConfirmed) {
            var start = info.event.start.toISOString(); // Convert to ISO string
            var stop = info.event.end ? info.event.end.toISOString() : ""; // Handle cases where `end` might be null
            var resource = info.resource ? info.resource.id : ""; // Check if resource exists
            var title = info.event.title || ""; // Event title
            var id = info.event.id || ""; // Additional properties if any

            // Create a form dynamically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?page=workorder/moveworkorder';

            // Add hidden inputs
            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;
            form.appendChild(inputId);

            var inputStart = document.createElement('input');
            inputStart.type = 'hidden';
            inputStart.name = 'start';
            inputStart.value = start;
            form.appendChild(inputStart);

            var inputStop = document.createElement('input');
            inputStop.type = 'hidden';
            inputStop.name = 'stop';
            inputStop.value = stop;
            form.appendChild(inputStop);

            var inputTitle = document.createElement('input');
            inputTitle.type = 'hidden';
            inputTitle.name = 'eventtitle';
            inputTitle.value = title;
            form.appendChild(inputTitle);

            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        } else {
            info.revert(); // Revert the event back to its original position
        }
    });
},
      selectable: true,
      select: function (arg) {
              Swal.fire({
                  html: '<div class="mb-7">Nieuwe Opdracht inplannen?</div><div class="fw-bolder mb-5">Omschrijving:</div><input type="text" class="form-control" name="event_name" />',
                  icon: "question",
                  showCancelButton: true,
                  confirmButtonText: "Maak aan",
                  cancelButtonText: "Anuleer",
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
              }).then(function (result) {
                  if (result.value) {
                      var title = document.querySelector('input[name="event_name"]').value;
                      if (title) {
                          var start = arg.start.toISOString();
                          var stop = arg.end.toISOString();
                          var id = arg.end.toISOString();
                          var resources = arg.resource.id;

                          // Create a form dynamically
                          var form = document.createElement('form');
                          form.method = 'POST';
                          form.action = 'index.php?page=workorder/editworkorder';

                          // Add hidden inputs
                          var inputStart = document.createElement('input');
                          inputStart.type = 'hidden';
                          inputStart.name = 'start';
                          inputStart.value = start;
                          form.appendChild(inputStart);

                          var inputStop = document.createElement('input');
                          inputStop.type = 'hidden';
                          inputStop.name = 'stop';
                          inputStop.value = stop;
                          form.appendChild(inputStop);

                          var inputTitle = document.createElement('input');
                          inputTitle.type = 'hidden';
                          inputTitle.name = 'eventtitle';
                          inputTitle.value = title;
                          form.appendChild(inputTitle);

                          var inputresources = document.createElement('input');
                          inputresources.type = 'hidden';
                          inputresources.name = 'resources';
                          inputresources.value = resources;
                          form.appendChild(inputresources);


                          // Append form to body and submit
                          document.body.appendChild(form);
                          form.submit();
                      } else {
                          Swal.fire({
                              text: "Opdrachtnummer is verplicht",
                              icon: "error",
                              buttonsStyling: true,
                              confirmButtonText: "Ik snap het",
                              confirmButtonColor: "#3085d6"
                          });
                      }
                  
                  
                    } else if (result.dismiss === 'cancel') {
                      Swal.fire({
                          text: "Opdrach is niet aangemaakt!",
                          icon: "error",
                          buttonsStyling: true,
                          confirmButtonText: "Sluiten",
                          confirmButtonColor: "#3085d6"
                      });
                  }
              });
            },
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
      resourceAreaHeaderContent: 'Resources',
      resources: <?php echo $um->getResources(); ?>,
      resourceOrder: 'sortOrder',
      events: <?php echo $workorder->getWorkordersJson(); ?>
    });

    calendar.render();
  });



</script>

<div id='calendar'></div>

