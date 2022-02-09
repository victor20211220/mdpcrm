<?php $this->layout->load_view('clients/jquery_client_lookup'); ?>

<script type="text/javascript">
    $(function () {
        $('#modal_copy_quote').modal('show');

        // Creates the quote
        $('#copy_quote_confirm').click(function () {
            $.post("<?= site_url('quotes/ajax/copy_quote'); ?>", {
                    quote_id: <?= $quote_id; ?>,
                    client_name: $('#client_name').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?= site_url('quotes/view'); ?>/" + response.quote_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                }
            );
        });
    });

</script>

<div id="modal_copy_quote" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="modal_copy_quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('copy_quote'); ?></h3>
        </div>
        <div class="modal-body">

            <input type="hidden" name="user_id" id="user_id" value="<?= $quote->user_id; ?>">

            <div class="form-group">
                <label for="client_name">
                    <?= lang('client'); ?>
                </label>

                <div class="controls">
                    <select class="form-control" name="client_name" id="client_name" required>
                        <?php foreach ($clients as $cli): ?>
                            <option value="<?= $cli['client_name']; ?>"><?= $cli['client_name']; ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="quote_date_created">
                    <?= lang('quote_date'); ?>
                </label>

                <div class="input-group">
                    <input name="quote_date_created" id="quote_date_created" class="form-control datepicker"
                           value="<?= date_from_mysql($quote->quote_date_created, true); ?>">
                    <label for="quote_date_created" class="input-group-btn">
                        <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="invoice_group_id">
                    <?= lang('invoice_group'); ?>
                </label>

                <div class="controls">
                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                        <option value=""></option>
                        <?php foreach ($invoice_groups as $invoice_group) { ?>
                            <option value="<?= $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->Mdl_settings->setting('default_quote_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?= $invoice_group->invoice_group_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="copy_quote_confirm" type="button">
                    <i class="fa fa-check"></i> <?= lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
