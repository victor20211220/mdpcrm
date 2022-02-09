<?php foreach ($task as $t) { ?>
    <?php
    $dateArr = explode('-', $t['task_finish_date']);
    $date = implode('/', array_reverse($dateArr));
    ?>

    <link rel="stylesheet"
          href="/assets/responsive/libs/jquery/bootstrap/dist/css/bootstrap.css"
          type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/app.css" type="text/css"/>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="blueheader"><?= $t['task_name']; ?></h3>
            </div>
            <div class="modal-body">

                <form id="frmedit" class="modal-content" method="POST" role="form">
                    <div class="row modal-body" style="margin-left:0px !important;">
                        <input type="hidden" name="task_id" value="<?= $t['task_id']; ?>">
                        <div class="form-group">
                            <label for="">Task Name</label>
                            <input type="text" class="form-control" name="task_name" value="<?= $t['task_name']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="">Task Description</label>
                            <input type="text" class="form-control" name="task_description"
                                   value="<?= $t['task_description']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="">Client</label>
                            <select name="client_id" class="form-control">
                                <?php foreach ($clients as $c) { ?>
                                    <option value="<?= $c['client_id']; ?>" <?php if ($t['client_id'] == $c['client_id']) {
                                        echo "selected";
                                    } ?>>
                                        <?= $c['client_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="">Finish Date</label>
                            <div class="input-group">
                                <input name="task_finish_date" id="task_finish_date" class="form-control datepicker"
                                       value="<?= $date ?>">
                                <label for="task_finish_date" class="input-group-btn">
                                    <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                                </label>
                            </div>
                        </div>


                        <div class="col-md-6">

                            <p>Status</p>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" value="0" name="task_status"
                                               <?php if ($t['task_status'] == 0){ ?>checked="true"<?php } ?>>
                                        <i></i>
                                        <?= lang('not_assigned'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" value="1" name="task_status"
                                               <?php if ($t['task_status'] == 1){ ?>checked="true"<?php } ?>>
                                        <i></i>
                                        <?= lang('not_started'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" value="2" name="task_status"
                                               <?php if ($t['task_status'] == 2){ ?>checked="true"<?php } ?>>
                                        <i></i>
                                        <?= lang('in_progress'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" value="3" id="btncomplete" name="task_status"
                                               <?php if ($t['task_status'] == 3){ ?>checked="true"<?php } ?>>
                                        <i></i>
                                        <?= lang('complete'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="divtime" style="display: none;">
                            <label for="">Time</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="total_time">
                            </div>
                        </div>

                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="btnsub" class="btn btn-success">Save changes</button>
                    </div>

                    <?php foreach ($task_statuses as $key => $status) : ?>
                        <option value="<?= $key; ?>"
                            <?php if ($key == $t['task_status']) { ?>selected="selected"<?php } ?>><?= $status['label']; ?>
                        </option>
                    <?php endforeach; ?>
            </div>

        </div>
        </form>
    </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function ($) {
            $('.datepicker').datepicker({
                autoclose: true,
                format: 'mm/dd/yyyy',
                orientation: 'top right',
                showOn: "both",
                buttonImage: "/assets/responsive/img/calendar.png",
                buttonImageOnly: true,
                buttonText: "Select date",
            });
        });
    </script>
    <script type="text/javascript">
        $("#btncomplete").click(function () {
            $('#divtime').fadeIn('slow');
        })
    </script>
    <script>
        $("#btnsub").click(function () {
            dataString = $("#frmedit").serialize();
            $.ajax({
                type: "POST",
                url: "/tasks/edit_task_submit",
                data: dataString,
                success: function (data) {
                    if (data != 0) {
                        $('#modal-editform').modal("hide");
                        location.reload();
                        $('#btnbackmodal').css("background-color", "red");
                        alert('Task was edited !');

                    }
                },
                error: function (data) {
                }
            }).complete(function () {

                //location.reload();
            });

            return false;
        });
        $('button.btn.btn-default, .close').on('click', function () {
            $('#modal-editform').delay(50).html('');
        });
    </script>


<?php } ?>
