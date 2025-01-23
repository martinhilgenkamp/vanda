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
      nowIndicator: true,
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
                      // Gather data to send via AJAX
                var data = {
                    id: info.event.id || "", // Event ID
                    start: info.event.start.toISOString(), // Start time in ISO format
                    stop: info.event.end ? info.event.end.toISOString() : "", // End time in ISO format
                    eventtitle: info.event.title || "", // Event title
                    resource: info.newResource ? info.newResource.id : "", // Resource ID if exists
                    oldresource: info.oldResource ? info.oldResource.id : "" // Resource ID if exists
                };

                // Make AJAX POST request
                $.ajax({
                    url: 'pages/workorder/moveworkorder.php', // The server endpoint
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        // Assuming the server responds with a success flag
                        if (response.success) {
                            Swal.fire({
                                title: "Succes!",
                                text: "De opdracht is succesvol verplaatst.",
                                icon: "success"
                            });
                        } else {
                            Swal.fire({
                                title: "Fout!",
                                text: response.message || "De opdracht kon niet worden verplaatst.",
                                icon: "error"
                            });
                            info.revert(); // Revert the event to its original position
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Fout!",
                            text: "Er is een fout opgetreden bij het verplaatsen van de opdracht.",
                            icon: "error"
                        });
                        info.revert(); // Revert the event to its original position
                    }
                });
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
                          var start = arg.start ? arg.start.toISOString() : null;
                          var stop = arg.end ? arg.end.toISOString() : null;
                          var id = arg.id ? arg.id : null;
                          var resources = arg.resource && arg.resource.id ? arg.resource.id : null;

                          console.log("Starting New Order");

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
      aspectRatio: 2,
      headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'resourceTimelineWeek,resourceTimelineMonth,listWeek'
      },
      initialView: 'resourceTimelineMonth',
      views: {
        resourceTimelineWeek: {
          buttonText: 'Week',
          duration: { weeks: 1 },
        },
        resourceTimelineMonth: {
          type: 'resourceTimeline',
          duration: { months: 1 },
          buttonText: 'Maand'
        },
        dayGridMonth: {
          buttonText: 'Maand'
        },
        listWeek: {
          buttonText: 'Lijst'
        }
      },
      slotDuration: "12:00:00",
      slotWidth: "500px",
      resourceAreaHeaderContent: 'Resources',
      resources: <?php echo $um->getResources(); ?>,
      resourceOrder: 'sortOrder',
      events: <?php echo $workorder->getWorkordersJson(); ?>
    });
    calendar.render();
  });



</script>

<div id='calendar'></div>

