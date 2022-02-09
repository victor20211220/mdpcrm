<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>mdpCRM - calndar</title>

    <link rel='stylesheet' href="/assets/calendar/fullcalendar/fullcalendar.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/calendar/colorpicker/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="/assets/calendar/datetimepicker/css/bootstrap-datetimepicker.min.css">

    <style>
        html, body {
            background: #FFFFFF;
            margin: 0;
            padding: 0;
            font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
            font-size: 13px;
        }

        #calendar {
            padding: 15px;
        }

        .pick-color {
            cursor: pointer;
            width: 30px;
        }
    </style>
</head>

<body>

<div id="calendar"></div>

<div id="modalCreate" class="modal">
    <div class="modal-dialog" role="document">
        <div id="modalCreateContent" class="modal-content"></div>
    </div>
</div>

<div id="modalUpdate" class="modal">
    <div class="modal-dialog" role="document">
        <div id="modalUpdateContent" class="modal-content"></div>
    </div>
</div>

<div id="modalSettings" class="modal">
    <div class="modal-dialog" role="document">
        <div id="modalSettingsContent" class="modal-content"></div>
    </div>
</div>

</body>

<script src="/public_html/bootstrap-email/jquery/jquery.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script src="/assets/calendar/fullcalendar/fullcalendar.min.js"></script>
<script src="/assets/calendar/fullcalendar/gcal.min.js"></script>
<script src="https://bootswatch.com/_vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/assets/calendar/colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="/assets/calendar/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

<script>
    $(function() {
        $('#calendar').fullCalendar({
            themeSystem: 'bootstrap4',
            googleCalendarApiKey: 'AIzaSyAYZ6zldUqgoymAdraUnQ-6_HfM0rtB6wI',
            weekends: true,
            height: 650,
            aspectRatio: 1.25,
            customButtons: {
                customCreateEvent: {
                    text: 'create event',
                    click: function() {
                        $('#modalCreateContent').html('').load('/calendar/create');
                        $('#modalCreate').modal({
                            backdrop: 'static'
                        });
                    }
                },
                customSettings: {
                    text: 'settings',
                    click: function () {
                        $('#modalSettingsContent').html('').load('/calendar/settings');
                        $('#modalSettings').modal({
                            backdrop: 'static'
                        });
                    }
                },
                customClose: {
                    text: 'close',
                    click: function () {
                        parent.$('#calendarModal').modal('hide');
                    }
                }
            },
            header: {
                left: 'today prev,next customCreateEvent customSettings',
                center: 'title',
                right: 'month,agendaWeek,timelineWeek,agendaDay,listMonth customClose'
            },
            eventSources: [
                {
                    url: '/calendar/events'
                },
                {
                    googleCalendarId: 'pecgb538m694p32snhn7u3fu1o@group.calendar.google.com',
                    className: 'gcal-event'
                }
            ],
            eventClick: function(event) {
                if (event.url) {
                    var w = 1100;
                    var h = 650;
                    var left = (screen.width/2)-(w/2);
                    var top = (screen.height/2)-(h/2);
                    window.open(event.url, '_blank', 'width='+w+',height='+h+',left='+left+',top='+top);
                } else {
                    $('#modalUpdateContent').html('').load('/calendar/update/' + event.id);
                    $('#modalUpdate').modal({
                        backdrop: 'static'
                    });
                }
            }
        });

        $('body').on('click', '#modalCreateSubmit', function() {
            $.ajax({
                url: '/calendar/create',
                method: 'post',
                data: $('#modalCreateUpdateForm').serialize(),
                success: function (response) {
                    $('#modalCreateContent').html(response);
                },
                error: function (response) {
                    alert('There is an error via requesting server');
                    console.log(response);
                }
            });
        });

        $('body').on('click', '#modalUpdateSubmit', function () {
            $.ajax({
                url: '/calendar/update/' + $(this).data('id'),
                method: 'post',
                data: $('#modalCreateUpdateForm').serialize(),
                success: function (response) {
                    $('#modalCreateContent').html(response);
                },
                error: function (response) {
                    alert('There is an error via requesting server');
                    console.log(response);
                }
            });
        });

        $('body').on('click', '#modalUpdateDelete', function () {
            $.ajax({
                url: '/calendar/delete/' + $(this).data('id'),
                method: 'get',
                success: function () {
                    window.location.reload();
                }
            });
        });
    });
</script>

</html>
