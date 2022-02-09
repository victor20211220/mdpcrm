<script type="text/javascript">
    $(function () {
        $('#enter-payment').modal('show');

        $('#enter-payment').on('shown', function () {
            $('#payment_amount').focus();
        });

        $('#btn_modal_payment_submit').click(function () {
            $.post("/payments/ajax/add", {
                    invoice_id: $('#invoice_id').val(),
                    payment_amount: $('#payment_amount').val(),
                    payment_method_id: $('#payment_method_id').val(),
                    payment_date: $('#payment_date').val(),
                    payment_note: $('#payment_note').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?= $_SERVER['HTTP_REFERER']; ?>";
                    } else {
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>

<div id="enter-payment" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_enter_payment" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>
            <h3><?= lang('enter_payment'); ?></h3>
        </div>

        <div class="modal-body">
            <form>
                <input type="hidden" name="invoice_id" id="invoice_id" value="<?= $invoice_id; ?>">

                <div class="form-group">
                    <label for="payment_amount"><?= lang('amount'); ?></label>
                    <div class="controls">
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                               value="<?= (isset($invoice_balance) ? format_amount($invoice_balance):''); ?>"
                        />
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label class="payment_date"><?= lang('payment_date'); ?></label>
                    <div class="input-group">
                        <input name="payment_date" id="payment_date" class="form-control datepicker" value="<?= date(date_format_setting()); ?>">
                        <label for="payment_date" class="input-group-btn">
                            <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payment_method_id"><?= lang('payment_method'); ?></label>
                    <div class="controls">
                        <select name="payment_method_id" id="payment_method_id" class="form-control"
                            <?=(!empty($invoice_payment_method) ? 'disabled="disabled"' : ''); ?>>
                            <option value=""></option>
                            <?php foreach ($payment_methods as $payment_method) { ?>
                                <option value="<?= $payment_method->payment_method_id; ?>"
                                    <?php if (isset($invoice_payment_method)
                                            && $invoice_payment_method == $payment_method->payment_method_id) {
                                        echo 'selected="selected"';
                                    }?>>
                                    <?= $payment_method->payment_method_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payment_note"><?= lang('note'); ?></label>
                    <div class="controls">
                        <textarea name="payment_note" id="payment_note" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="btn_modal_payment_submit" type="button">
                    <i class="fa fa-check"></i>
                    <?= lang('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
