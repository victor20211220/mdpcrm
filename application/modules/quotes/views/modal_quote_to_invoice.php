<script type="text/javascript">
    $(function () {
        // Display the create quote modal
        $('#modal_quote_to_invoice').modal('show');

        // Creates the invoice
        $('#quote_to_invoice_confirm').click(function () {
            $.post("<?= site_url('quotes/ajax/quote_to_invoice'); ?>", {
                    quote_id: <?= $quote_id; ?>,
                    client_name: $('#client_name').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    console.log(response);
                    if (response.success == '1') {
                        window.location = "<?= site_url('invoices/view'); ?>/" + response.invoice_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="modal_quote_to_invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-4 col-md-offset-4"
     role="dialog" aria-labelledby="modal_quote_to_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('quote_to_invoice'); ?></h3>
        </div>
        <div class="modal-body">

            <input type="hidden" name="client_name" id="client_name"
                   value="<?= $quote->client_name; ?>">
            <input type="hidden" name="user_id" id="user_id"
                   value="<?= $quote->user_id; ?>">

            <div class="form-group has-feedback">
                <label for="invoice_date_created">
                    <?= lang('invoice_date'); ?>
                </label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created" class="form-control datepicker">
                    <label for="invoice_date_created" class="input-group-btn">
                        <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="invoice_password"><?= lang('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php if ($this->Mdl_settings->setting('invoice_pre_password') == '') {
                           echo '';
                       } else {
                           echo $this->Mdl_settings->setting('invoice_pre_password');
                       } ?>" style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id">
                    <?= lang('invoice_group'); ?>
                </label>
                <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                    <?php foreach ($invoice_groups as $invoice_group): ?>
                        <option value="<?= $invoice_group->invoice_group_id; ?>"
                                <?php if ($this->Mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>>
                            <?= $invoice_group->invoice_group_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="quote_to_invoice_confirm" type="button">
                    <i class="fa fa-check"></i> <?= lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
