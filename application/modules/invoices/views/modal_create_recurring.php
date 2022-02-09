<script type="text/javascript">
    $(function () {
        $('#modal_create_recurring').modal('show');

        get_recur_start_date();

        $('#recur_frequency').change(function () {
            get_recur_start_date();
        });

        $('#create_recurring_confirm').click(function () {
            $.post("/invoices/ajax/create_recurring", {
                    invoice_id: <?= $invoice_id; ?>,
                    recur_start_date: $('#recur_start_date').val(),
                    recur_end_date: $('#recur_end_date').val(),
                    recur_frequency: $('#recur_frequency').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?= site_url('invoices/view'); ?>/<?= $invoice_id; ?>";
                    }
                    else {
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                }
            );
        });

        function get_recur_start_date() {
            $.post("/invoices/ajax/get_recur_start_date", {
                    invoice_date: $('#invoice_date_created').val(),
                    recur_frequency: $('#recur_frequency').val()
                },
                function (data) {
                    $('#recur_start_date').val(data);
                }
            );
        }
    });
</script>

<div id="modal_create_recurring" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="modal_create_recurring" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('create_recurring'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label><?= lang('every'); ?>: </label>

                <div class="controls">
                    <select name="recur_frequency" id="recur_frequency" class="form-control"
                            style="width: 150px;">
                        <?php foreach ($recur_frequencies as $key => $lang) : ?>
                            <option value="<?= $key; ?>"
                                    <?php if ($key == '1M') { ?>selected="selected"<?php } ?>>
                                <?= lang($lang); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label><?= lang('start_date'); ?>: </label>

                <div class="input-group">
                    <input name="recur_start_date" id="recur_start_date" class="form-control datepicker">
                    <label for="recur_start_date" class="input-group-btn">
                        <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                    </label>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label><?= lang('end_date'); ?> (<?= lang('optional'); ?>): </label>

                <div class="input-group">
                    <input name="recur_end_date" id="recur_end_date" class="form-control datepicker">
                    <label for="recur_end_date" class="input-group-btn">
                        <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                    </label>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder id="create_recurring_confirm" type="button">
                    <i class="fa fa-check"></i> <?= lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>
</div>
