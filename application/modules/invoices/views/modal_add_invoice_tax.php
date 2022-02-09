<script type="text/javascript">
    $(function () {
        $('#invoice_tax_submit').click(function () {
                $.post("<?= site_url('invoices/ajax/save_invoice_tax_rate'); ?>
                ", {
                invoice_id: <?= $invoice_id; ?>,
                tax_rate_id: $('#tax_rate_id').val(),
                    include_item_tax
            :
                $('#include_item_tax').val()
            },
            function (data) {
                var response = JSON.parse(data);
                if (response.success == 1) {
                    window.location = "<?= site_url('invoices/view'); ?>/" +<?= $invoice_id; ?>
                    ;
                }
            });
    });
    })
    ;
</script>

<div id="add-invoice-tax" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="add-invoice-tax" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('add_invoice_tax'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="tax_rate_id"><?= lang('invoice_tax_rate'); ?>: </label>

                <div class="controls">
                    <select name="tax_rate_id" id="tax_rate_id" class="form-control">
                        <option value="0"><?= lang('none'); ?></option>
                        <?php foreach ($tax_rates as $tax_rate) : ?>
                            <option value="<?= $tax_rate->tax_rate_id; ?>"><?= $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="include_item_tax"><?= lang('tax_rate_placement'); ?></label>

                <div class="controls">
                    <select name="include_item_tax" id="include_item_tax" class="form-control">
                        <option value="0"><?= lang('apply_before_item_tax'); ?></option>
                        <option value="1"><?= lang('apply_after_item_tax'); ?></option>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="invoice_tax_submit" type="button">
                    <i class="fa fa-check"></i> <?= lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>
</div>
