<?php $this->layout->load_view('suppliers/jquery_supplier_lookup'); ?>

<script type="text/javascript">
    	$(function () {
        // Display the create invoice modal
        $('#change-supplier').modal('show');

        $('#change-supplier').on('shown', function () {
            $("#supplier_name").focus();
        });

        $().ready(function () {
            $("[name='supplier_name']").select2({allowClear: true});
            $("#supplier_name").focus();
        });

        // Creates the invoice
        $('#supplier_change_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new supplier if necessary
            $.post("<?php echo site_url('invoices/ajax/change_supplier'); ?>
				", {
				supplier_name: $('#supplier_name').val(),
				invoice_id: $('#invoice_id').val()

				},
				function (data) {
				console.log(data);
				var response = JSON.parse(data);
				if (response.success == '1') {
				// The validation was successful and invoice was created
				window.location = "
<?php echo site_url('invoices/view'); ?>
							/" + response.invoice_id;
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

<div id="change-supplier" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('change_supplier'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <select name="supplier_name" id="supplier_name" class="input-sm form-control" autofocus>
                    <option></option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->supplier_name; ?>"
                                <?php if ($supplier_name == $supplier->supplier_name) { ?>selected="selected"<?php } ?>
                            > <?php echo $supplier->supplier_name; ?>     </option>

                    <?php } ?>
                </select>
            </div>

            <input class="hidden" id="invoice_id" value="<?php echo $invoice_id; ?>">

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="supplier_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
