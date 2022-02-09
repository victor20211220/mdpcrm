<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>mdpCRM EMAIL center</title>

    <link href="/public_html/bootstrap-email/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Mukta+Malar:400,700" rel="stylesheet">

    <style>
        body {
            font-family: 'Mukta Malar', sans-serif;
            font-size: 13px;
            color: #545b62 !important;
        }

        .fa-xxs {
            font-size: .70em;
        }

        .email-control-row {
            height: 72px !important;
            padding-top: 14px !important;
        }

        .email-control-btn {
            width: 90px;
        }

        .email-boxes-row {
            border-top: 5px solid #F2F2F2
        }

        .email-send-message-btn {
            width: 150px;
        }

        .email-nav-btn {
            background: #FFFFFF;
            border-color: #FFFFFF
        }

        .email-nav-btn:hover {
            background: #F2F2F2;
            border-color: #FFFFFF;
        }

        .email-item-table {
            background: #FFFFFF;
            width: 100%;
            height: 100%;
            vertical-align: top;
            border-left: 10px solid #F2F2F2;
            border-right: 10px solid #f2f2f2;
            border-top: 5px solid #f2f2f2;
            border-bottom: 10px solid #f2f2f2;
        }

        .email-item-row {
            background: #F2F2F2;
            vertical-align: top;
        }

        .email-item-head-tr {
            height: 50px;
        }

        .email-item-body-tr {
            height: 40px;
            border-top: 2px solid #F2F2F2;
            border-bottom: 2px solid #F2F2F2;
        }

        .email-item-body-tr:hover {
            cursor: pointer;
            height: 40px;
            border-top: 2px solid #F2F2F2;
            border-bottom: 2px solid #F2F2F2;
            background: #FAFAFA;
        }

        .email-item-col-check {
            width: 42px;
        }

        .email-item-col-subject {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .email-item-col-star {
            width: 42px;
        }

        .email-item-col-from {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .email-item-col-date {
            width: 125px
        }

        .email-item-col-size {
            width: 75px
        }

        .email-item-col-flag {
            width: 42px
        }

        #email-alert {
            width: 100%;
            margin: 5px 10px -10px 10px
        }
    </style>
</head>

<body>

<div class="container-fluid" style="border-bottom: 5px solid #F1F2F3">
    <div class="row text-center">
        <div class="float-left" style="width: 80%">
            <h5 class="text-left pt-2 pl-3">
                mdpCRM email center panel
            </h5>
        </div>
        <div class="float-right" style="width: 20%; vertical-align: middle; text-align: right">
            <span class="fas fa-times fa-lg pt-3 pr-3" style="color: #545b62; cursor: pointer" onclick="parent.$('#myModal').modal('hide');"></span>
        </div>

    </div>
</div>

<div class="container-fluid pl-3 pr-3 pt-1 pb-0">
    <div class="row email-control-row">
        <div class="col-2 text-center" style="vertical-align: center">
            <button class="btn btn-sm btn-success email-compose-button" style="width: 170px">
                <span class="fas fa-pencil fa-xxs"></span>
                Compose
            </button>
        </div>
        <div class="col-10">
            <div class="row">
                <div class="col-7 pl-2">
                    <button class="btn btn-sm btn-light email-control-btn mr-1 ml-1 email-refresh-button">
                        <span class="fas fa-sync fa-xxs mr-1 ml-2"></span>
                        Refresh
                    </button>
                    <button class="btn btn-sm btn-light email-control-btn mr-1 email-reply-button">
                        <span class="fas fa-reply fa-xxs mr-1"></span>
                        Reply
                    </button>
                    <button class="btn btn-sm btn-light email-control-btn mr-1 email-reply-button">
                        <span class="fas fa-reply-all fa-xxs mr-1"></span>
                        Reply all
                    </button>
                    <button class="btn btn-sm btn-light email-control-btn mr-1 email-forward-button">
                        <span class="fas fa-reply fa-rotate-180 fa-xxs mr-1"></span>
                        Forward
                    </button>
                    <button class="btn btn-sm btn-light email-control-btn mr-1 email-delete-button">
                        <span class="fas fa-trash-alt fa-xxs mr-1"></span>
                        Delete
                    </button>
                    <button class="btn btn-sm btn-light email-control-btn email-flag-button">
                        <span class="fas fa-check-circle fa-xxs mr-1"></span>
                        Mark
                    </button>
                </div>
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        <div class="input-group-append">
                            <button id="emailSearchMailbox" class="btn btn-outline-primary dropdown-toggle" type="button" data-id="0" data-toggle="dropdown" style="width: 115px !important;">
                                All mailboxes
                            </button>
                            <div class="dropdown-menu" style="width: 415px; font-size: .875rem !important;">
                                <?= $boxesDrop; ?>
                            </div>
                        </div>
                        <input id="email-input-search" type="text" class="form-control" style="border-right: none" placeholder="Search something here"/>
                        <div class="input-group-append">
                            <div class="input-group-text" style="background: #FFFFFF; border-left: none">
                                <i class="fas fa-search fa-xxs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row email-boxes-row">
        <div class="col-2">

            <div class="mt-2"></div>
            <?= $boxesMenu; ?>

        </div>
        <div class="col-10">
            <div class="row email-item-row">
                <?= $mails; ?>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap core JavaScript -->
<script src="/public_html/bootstrap-email/jquery/jquery.min.js"></script>
<script src="/public_html/bootstrap-email/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $('body').on('click', '.email-nav-btn', function () {
            $('#alert').html('');
            showMailbox($(this).data('id'));
        }
    );

    $('body').on('click', '.email-refresh-button', function () {
            var boxId = parseInt($('#email-box-id').html());
            var boxName = $('#email-box-name').html();
            if (isNaN(boxId) == false && boxId > 0) {
                $('#email-alert').html('<div class="alert alert-primary">Refreshing ' + boxName + ' mailbox</div>');
            } else {
                boxId = 0;
                $('#email-alert').html('<div class="alert alert-primary">Refreshing all mailboxes</div>');
            }

            $.ajax({
                type: "post",
                url: "email/refresh_email/" + boxId,
                success: function (response) {
                    obj = JSON.parse(response);
                    $('#email-alert').html('<div class="alert alert-' + obj.status + '"><span class="icons icons-alert-' + obj.status + '"></span>' + obj.message + '</div>');
                    $('#mailBody').html(obj.mailBody);

                    setTimeout(function(){
                        $('#email-alert').html('');
                    }, 3000);
                },
                error: function () {
                    $("#email-alert").html('<div class="alert alert-danger">Error appeared while updating mailboxes</div>');
                }
            });
        }
    );

    $('body').on('dblclick', '.email-item-body-tr', function () {
        $('#email-alert').html('<div class="alert alert-primary">Featching email message</div>');
        $.ajax({
            type: 'post',
            url: '/email/details/' + $(this).attr("data-id"),
            success: function (response) {
                $('.email-item-row').html(response);
            },
            error: function () {
                $('#email-alert').html('<div class="alert alert-danger">Error appeared while fetching message</div>');
            }
        });
    });

    /**
     * Compose message
     */
    $('body').on('click', '.email-compose-button', function () {
        $.ajax({
            url: '/email/compose',
            type: 'post',
            success: function (response) {
                $('.email-item-row').html(response);
            }
        });
    });

    $('body').on('click', '.email-send-message-btn', function () {
        $.ajax({
            url: '/email/send_email',
            type: 'post',
            data: {
                email_to: $('#send-email-to').val(),
                subject: $('#send-email-subject').val(),
                message: $('#send-email-message').val()
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.status == 'success') {
                    $('#success-send-email-tbody').show();
                    $('#standart-send-email-tbody').hide();
                } else {
                    $('#email-alert').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });

    /**
     * Reply message trigger
     */
    $('body').on('click', '.email-reply-button', function () {
        var messageId = $('#email-message-id').html();
        var arr = new Array();

        if (!messageId) {
            $('input.email-item-checkbox:checked').each(function(idx, value) {
                arr.push(parseInt($(value).data('id')));
            });

            if (arr.length == 1) {
                messageId = arr[0];
            }
        }

        if (!messageId) {
            if (arr.length > 1) {
                alert('Select only one message to reply');
            } else {
                alert('Select message to reply');
            }
        } else {
            composeEmail(messageId, 'reply');
        }
    });

    $('body').on('click', '.email-forward-button', function() {
        var messageId = $('#email-message-id').html();
        var arr = new Array();

        if (messageId) {
            composeEmail(messageId, 'forward');
        }
    });

    /**
     * Delete message trigger
     */
    $('body').on('click', '.email-delete-button', function () {
        var messageId = $('#email-message-id').html();
        var arr = new Array();

        if (!messageId) {
            $('input.email-item-checkbox:checked').each(function(idx, value) {
                arr.push(parseInt($(value).data('id')));
            });

            messageId = arr.join(',');
        }

        if (!messageId) {
            alert('Select message to delete');
        } else {
            $.ajax({
                url: '/email/delete/' + messageId,
                type: 'post',
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        $('#email-alert').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function(){
                            showMailbox(response.box_id);
                        }, 3000);
                    }
                }
            });
        }
    });

    /**
     * Flag message trigger
     */
    $('body').on('click', '.email-flag-button', function() {
        var messageId = $('#email-message-id').html();
        var arr = new Array();

        if (!messageId) {
            $('input.email-item-checkbox:checked').each(function(idx, value) {
                arr.push(parseInt($(value).data('id')));
            });

            messageId = arr.join(',');
        }

        if (!messageId) {
            alert('Select message to flag');
        } else {
            $.ajax({
                url: '/email/flag/' + messageId,
                type: 'post',
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        $('#email-alert').html('<div class="alert alert-success">Flag was set to message(s)</div>');
                        setTimeout(function(){
                            if (!$('#email-message-id').html()) {
                                showMailbox(response.box_id);
                            } else {
                                $('#email-alert').html();
                            }
                        }, 3000);
                    }
                }
            });
        }
    });

    $('body').on('click', '.email-menu-dropdown-item', function () {
        var boxId = $(this).data('id');
        var boxName = $(this).data('name');

        $('.email-menu-dropdown-item').each(function (idx, obj) {
            $(obj).removeClass('active');
        });

        $(this).addClass('active');
        $('#emailSearchMailbox').html(boxName);
        $('#emailSearchMailbox').data('id', boxId);
    });

    /**
     * Show mailbox
     * @param id
     */
    function showMailbox(id) {
        $.ajax({
            type: "post",
            url: "/email/box_emails/" + id,
            success: function (response) {
                $('.email-item-row').html(response);
            }
        });
    }

    /**
     * Mail search
     */
    $('#email-input-search').on('keyup', function () {
        console.log(event);

        var text = $('#email-input-search').val();
        var boxId = $('#emailSearchMailbox').data('id');

        if (event instanceof KeyboardEvent) {
            console.log('KeyboardEvent');
        }

        setTimeout(function () {
            if (
                text.length == 0 ||
                text != $('#email-input-search').val() ||
                boxId != $('#emailSearchMailbox').data('id')
            ) {
                return false;
            }

            $.ajax({
                type: 'post',
                url: '/email/search',
                data: {
                    box_id: boxId,
                    text: text
                },
                success: function (response) {
                    $('.email-item-row').html(response);
                }
            });
        }, 500);
    });

    /**
     * Compose / reply / forward
     * @param messageId
     * @param action
     */
    function composeEmail(messageId, action) {
        $.ajax({
            url: '/email/compose',
            type: 'post',
            data: {
                message_id: messageId,
                action: action
            },
            success: function (response) {
                $('.email-item-row').html(response);
            }
        });
    }
</script>

</body>

</html>
