<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Assign Task</title>

    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <script src="https://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <script type="text/javascript" src="https://www.projectflowapp.com/public/js/selectivizr-min.js"></script>
    <![endif]-->

    <script type="text/javascript">
        var siteUrl = '<?= base_url(); ?>';
    </script>


    <!--STYLESHEETS-->
    <link rel="stylesheet" href="/assets/responsive/libs/assets/font-awesome/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/assets/responsive/css/drag_css/style.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/responsive/css/extra_tasks.css">
    <link rel="stylesheet" type="text/css" href="/assets/responsive/css/drag_css/print.css" media="print">
    <link rel="stylesheet" type="text/css"
          href="/assets/responsive/libs/jquery/bootstrap/dist/css/bootstrap_custom.css?v=7">
    <link rel="stylesheet" type="text/css" href="/assets/responsive/css/app.css">
</head>

<style>
    .limit_any_move {
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        opacity: 0.8;
        position: absolute;
        z-index: 99999999;
    }

    .working {
        z-index: 3005;
        display: block;
    }

    body {
        background-color: #ffffff;
        clear: both;
        height: auto;
    }

    .message {
        top: -0px;
        left: 25px;
    }

    @media (min-width: 992px) {
        body {
            background-image: none;
        }
    }

    .project[data-people] {
        margin: 20px;
    }

    @media screen and (max-width: 1300px) {
        .project[data-people] {
            margin: 5px;
        }
    }

    .column.color1 > .head, .column.color2 > .head, .column.color3 > .head, .column.color4 > .head, .column.color5 > .head {
        height: 54px !important;
        line-height: 54px !important;
    }

    #modal-editform {
        margin: auto;
    }

    .input-group-btn {
        vertical-align: bottom;
    }

    .datepicker.dropdown-menu {
        z-index: 10000;
    }

    .panel-body {
        padding: 5px !important;
    }

    .modal-header .close {
        font-size: 2.3em;
    }

    a.btn, button.btn, input[type=submit].btn {
        font-family: "Roboto", sans-serif;
    }
</style>

<body style="cursor: auto;">

<script type="text/javascript">
    var _people = 'people';
    var _client = 'client';
    var _board = '22886';
    var _limit = '100';
</script>

<div id="Preloader" style="display: none;"></div>
<div id="Loading" style="display: none;"></div>

<header id="Header"></header>

<div class="message" style="display: none;">Successfully Moved!</div>
<div class='limit_any_move' style="display: none;">
    <div class="working"></div>
</div>

<div id="confirm" class="modal hide fade" role="dialog">
    <div class="modal-header"></div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <a class="btn btn-primary js-confirm" data-dismiss="modal" data-original-title="" title="">OK</a>
        <a class="btn" data-dismiss="modal" data-original-title="" title="">Cancel</a>
    </div>
</div>

<div id="edit" class="modal hide fade" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Edit Details</h3>
    </div>
    <div class="ajax"></div>
</div>

<div id="modal" class="menu modal hide fade" role="dialog" data-width="938">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>&nbsp;</h3>
    </div>
    <div class="ajax"></div>
</div>

<div id="List" style="height: 819px; padding-top: 61px;">
    <div class="list_column ui-sortable row">
        <div id="status_id_000" class="column col-md-4 ">
            <header id="000" class="head header000">
                <h2><?= lang('not_assigned'); ?></h2>
                <ul class="meta"></ul>
            </header>

            <div id="000" class="list_project ui-sortable">

                <div class="panel-group" id="accordion">
                    <?php $i = 0; ?>
                    <?php foreach ($unassigned_tasks as $t) : ?>

                        <?php $i++; ?>
                        <article id="project_id_<?= $t->task_id; ?>" class="project" data-people="John Doe">
                            <section class="info">
                                <div class="panel panel-default taskpanel">
                                    <div class="panel-heading">
                                        <div class="pull-right">
                                            <a href="#" id="<?= $t->task_id; ?>" title="" class="pf-edit edit edittask"
                                               data-original-title="<?= lang('edit'); ?>">
                                                <i class="fa fa-pencil fa-margin"></i>
                                            </a>
                                        </div>
                                        <a data-toggle="collapse" data-parent="#accordion" class="tasktitle"
                                           href="#collapse<?= $t->task_id; ?>">
                                            <?= $t->task_name; ?>
                                        </a>
                                        <br>
                                        <p class="description">
                                            <?= $t->task_description; ?>
                                        </p>
                                    </div>
                                    <div id="collapse<?= $t->task_id; ?>" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <div class="description-wrapper">
                                                <i class="fa fa-clock-o"></i>
                                                <p class="description"><b>Deadline:</b>
                                                    <?= $t->task_finish_date; ?>
                                                </p>
                                            </div>

                                            <div class="description-wrapper">
                                                <i class="fa fa-adjust"></i>
                                                <p class="description"><b>Status:</b>
                                                    <?php
                                                    if ($t->task_status == 0) {
                                                        echo lang('not_assigned');
                                                    }
                                                    if ($t->task_status == 1) {
                                                        echo lang('not_started');
                                                    }
                                                    if ($t->task_status == 2) {
                                                        echo lang('in_progress');
                                                    }
                                                    if ($t->task_status == 3) {
                                                        echo lang('complete');
                                                    }
                                                    if ($t->task_status == 4) {
                                                        echo lang('pause');
                                                    }
                                                    ?>
                                                </p>
                                            </div>

                                            <div class="description-wrapper">
                                                <i class="fa fa-briefcase"></i>
                                                <p class="description"><b>Client:</b>
                                                    <?= get_client_name($t->client_id); ?>
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </section>
                        </article>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <?php

        $index_color = 1;

        foreach ($users as $user) { ?>

            <div id="status_id_<?= $user->user_id; ?>" class="column  col-md-4 color<?= $index_color; ?>">
                <header id="<?= $user->user_id; ?>" class="head project ">
                    <h2><?= $user->user_name; ?></h2>
                </header>

                <div id="<?= $user->user_id; ?>" class="list_project ui-sortable">
                    <div class="panel-group" id="accordion">
                        <?php $i = 0; ?>

                        <?php foreach ($user->tasks_assigned as $t) : ?>
                            <?php $i++; ?>

                            <article id="project_id_<?= $t->task_id; ?>" class="project" data-people="John Doe">
                                <section class="info">
                                    <div class="panel panel-default taskpanel">
                                        <div class="panel-heading">
                                            <div class="pull-right">
                                                <a href="#" id="<?= $t->task_id; ?>" title=""
                                                   class="pf-edit edit edittask"
                                                   data-original-title="<?= lang('edit'); ?>">
                                                    <i class="fa fa-pencil fa-margin"></i>
                                                </a>
                                            </div>
                                            <a data-toggle="collapse" data-parent="#accordion" class="tasktitle"
                                               id="<?= $t->task_id; ?>"
                                               href="#collapse<?= $t->task_id; ?>"
                                            >
                                                <?= $t->task_name; ?>
                                            </a>
                                            <br>
                                            <p class="description"><?= $t->task_description; ?></p>
                                        </div>
                                        <div id="collapse<?= $t->task_id; ?>"
                                             class="panel-collapse collapse in <?php /*if (true) echo "in";*/ ?>">
                                            <div class="panel-body">
                                                <div class="description-wrapper">
                                                    <i class="fa fa-clock-o"></i>
                                                    <p class="description">
                                                        <b>Deadline:</b>
                                                        <?= $t->task_finish_date; ?>
                                                    </p>
                                                </div>

                                                <div class="description-wrapper">
                                                    <i class="fa fa-adjust"></i>
                                                    <p class="description"><b>Status:</b>
                                                        <?php
                                                        if ($t->task_status == 0) {
                                                            echo lang('not_assigned');
                                                        }
                                                        if ($t->task_status == 1) {
                                                            echo lang('not_started');
                                                        }
                                                        if ($t->task_status == 2) {
                                                            echo lang('in_progress');
                                                        }
                                                        if ($t->task_status == 3) {
                                                            echo lang('complete');
                                                        }
                                                        if ($t->task_status == 4) {
                                                            echo lang('pause');
                                                        }
                                                        ?>
                                                    </p>
                                                </div>

                                                <div class="description-wrapper">
                                                    <i class="fa fa-briefcase"></i>
                                                    <p class="description"><b>Client:</b>
                                                        <?= get_client_name($t->client_id); ?>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </article>


                        <?php endforeach; ?>
                    </div>
                </div>

            </div>


            <?php $index_color++;
        } ?>


    </div>

</div>

<div class="fade modal-placeholder" id="modal-editform" style="margin: auto;"></div>

<script type="text/javascript" src="/assets/responsive/js/drag_drop/vendor.min.js"></script>
<script type="text/javascript" src="/assets/responsive/js/drag_drop/boot.min.js"></script>
<script type="text/javascript" src="/assets/responsive/js/drag_drop/app.js"></script>

<script type="text/javascript">
    $(document).on("click", '.tasktitle', function () {
        id = $(this).attr("id");
        $('.panel-collapse.in').collapse('hide');
    });

    $('.edittask').click(function () {
        id = $(this).attr("id");

        $.ajax({
            type: "POST",
            url: "/tasks/edit_task",
            data: "id_task=" + id,
            success: function (data) {
                if (data != 0) {
                    $('#modal-editform').modal("show");
                    $('#modal-editform').html(data);
                }
            },
            error: function (data) {
            }
        }).complete(function () {
            //location.reload();
        });

        return false;
    });
</script>

</body>
</html>
