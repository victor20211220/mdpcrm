<script type="text/javascript">
    $(function () {
        $('#assign-task').modal('show');
        $('.modal-content').css('height', $(window).height() * 0.95);
        $('.sweet_modal').css('height', ($('.modal-content').height() - ($('.modal-header').outerHeight() + $('.modal-footer').outerHeight())) * 0.9);
    });
</script>

<style>
    .modal-top-15-px {
        top: 15px;
    }
</style>


<div id="assign-task" class="modal modal-top-15-px col-xs-12"
     role="dialog" aria-labelledby="modal_create_task" aria-hidden="true">

    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" id="closeAssign" class="close">
                <i class="fa fa-close"></i>
            </a>
            <h3>
                <?= lang('assign_tasks'); ?>
                <button class="btn btn-success createtask" type="button">
                    <i class="fa fa-plus"></i> <?= lang('create_task'); ?>
                </button>
            </h3>
        </div>

        <div class="modal-body row">
            <div class="col-md-12">

                <iframe class='sweet_modal' id="sweet_modal" src="/tasks/assign_tasks_frame"
                        style="top:0px; left:0px; bottom:0px; right:0px; width:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:1;">
                    Your browser doesn't support iframes
                </iframe>
            </div>
        </div>

        <div class="modal-footer" style="text-align: left;">
            <button class="btn btn-empty" type="button" id="closeAssignz" data-dismiss="modal">
                <i class="fa fa-reply"></i> <?= lang('back'); ?>
            </button>
        </div>
    </form>
</div>


<div class="modal" id="modal-createtask"></div>

<script type="text/javascript">
    $('.createtask').click(function () {

        $.ajax({
            type: "POST",
            url: "/tasks/md_create_task",
            data: "",
            success: function (data) {
                if (data != 0) {
                    $('#modal-createtask').html(data);
                    $('#assign-task').modal("hide");
                    $('#modal-createtask').modal("show");
                }
            },
            error: function (data) {
            }
        }).complete(function () {
            $(".datepicker").datepicker({
                format: '<?= date_format_datepicker(); ?>',
                language: '<?= lang('cldr'); ?>',
                weekStart: '<?= $this->Mdl_settings->setting('first_day_of_week'); ?>',
                showOn: "both",
                buttonImage: "/assets/responsive/img/calendar.png",
                buttonImageOnly: true,
                buttonText: "Select date"
            });
        });

        return false;
    });
</script>
