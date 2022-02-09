<?php $this->layout->load_view('clients/jquery_client_lookup'); ?>

<script type="text/javascript">
    	$(function () {
        // Display the create quote modal
        $('#modal_copy_invoice').modal('show');


     $().ready(function () {
        $("[name='client_name']").select2({
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                multiple: false,
                allowClear: true,
                placeholder: 'Search by client name or type new client and press enter ...',
                initSelection: function(element, callback) {

                	<?php if (isset($client_id)){ ?>

			        						var data = {id: '<?php echo html_escape($client_id); ?>', text: '<?php echo html_escape($client_name); ?>
	'};
	callback(data);

					<?php } ?>
			    						},
                data: [
                    <?php
                    $i = 0;
                    foreach ($clients as $client)
                    {
                        echo "{
                        id: '" . str_replace("'", "\'", $client->client_id) . "',
                        text: '" . str_replace("'", "\'", $client->client_name) . "',
                        client_reg_number: '" . str_replace("'", "\'", $client->client_reg_number) . "',
                        client_address_1: '" . str_replace("'", "\'", $client->client_address_1) . "',
                        client_vat_id: '" . str_replace("'", "\'", $client->client_vat_id) . "'
                        }";
                        if (($i + 1) != count($clients))
                            echo ',';
                        $i++;
                    }
                    ?>
                						]
            });

            $("[name='client_name']").on("select2-selecting", function (e) {

            	if(e.choice.client_vat_id == undefined){

            		    //$('#new_client_on_invoice').val(1);

            		    $('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");;
	            		$('#client_id').val('-1');
	            		$('#client_reg_number').val('').prop('disabled', false).attr("placeholder", "Type value here");;
	            		$('#client_address_1').val('').prop('disabled', false).attr("placeholder", "Type value here");;
	            		$('#client_vat_id').val('').prop('disabled', false).attr("placeholder", "Type value here");;

            		    $("#client_reg_number").focus();


            	}else{
            		    //$('#new_client_on_invoice').val(0);

            		    $('#client_id').val(e.choice.id);


	            		$('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");

	            		if(e.choice.client_reg_number=='') $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', false).attr("placeholder", "Type value here");
	            									 else $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', true).attr("placeholder", "");

	            		if(e.choice.client_address_1=='') $('#client_address_1').val(e.choice.client_address_1).prop('disabled', false).attr("placeholder", "Type value here");
	            									 else $('#client_address_1').val(e.choice.client_address_1).prop('disabled', true).attr("placeholder", "");

	            		if(e.choice.client_vat_id=='') $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', false).attr("placeholder", "Type value here");
	            									 else $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', true).attr("placeholder", "");



            	}

            	});

		  if($('#client_reg_number').val()=='') $('#client_reg_number').prop('disabled', false).attr("placeholder", "Type value here");
	            						   else $('#client_reg_number').prop('disabled', true).attr("placeholder", "");

	      if($('#client_address_1').val()=='') $('#client_address_1').prop('disabled', false).attr("placeholder", "Type value here");
	            									 else $('#client_address_1').prop('disabled', true).attr("placeholder", "");

	      if($('#client_vat_id').val()=='') $('#client_vat_id').prop('disabled', false).attr("placeholder", "Type value here");
	            									 else $('#client_vat_id').prop('disabled', true).attr("placeholder", "");


            $('#client_name_input').prop('disabled', true).attr("placeholder", "");

            $("#client_name").focus();

        // Creates the invoice
        $('#copy_invoice_confirm').click(function () {
            $.post("<?php echo site_url('invoices/ajax/copy_invoice'); ?>
			", {
			invoice_id:
 <?php echo $invoice_id; ?>
				, client_id: $('#client_id').val(),
				client_name: $('#client_name').val(),
				client_reg_number: $('#client_reg_number').val(),
				client_address_1: $('#client_address_1').val(),
				client_vat_id: $('#client_vat_id').val(),

				invoice_date_created: $('#invoice_date_created').val(),
				invoice_group_id: $('#invoice_group_id').val(),
				invoice_password: $('#invoice_password').val(),
				invoice_time_created: '
<?php echo date('H:i:s') ?>
						',
						user_id: $('#user_id').val(),
						payment_method: $('#payment_method_id').val()
						},
						function (data) {
						var response = JSON.parse(data);
						if (response.success == '1') {
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
						});
</script>

<div id="modal_copy_invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-4 col-md-offset-4"
     role="dialog" aria-labelledby="modal_copy_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('copy_invoice'); ?></h3>
        </div>
        <div class="modal-body">

            <input class="hidden" name='new_client_on_invoice' id="new_client_on_invoice" value='0'>
            <input class="hidden" name='client_id' id="client_id" <?php
                if ($invoice->client_id)
                    echo 'value="' . html_escape($invoice->client_id) . '"';
 ?>>
            <input class="hidden" id="payment_method_id"
                   value="<?php echo $this->Mdl_settings->setting('invoice_default_payment_method'); ?>">

            <input type="hidden" name="user_id" id="user_id" class="form-control"
                   value="<?php echo $invoice->user_id; ?>">

			<div class="form-group">
                <label for="client_name"><?php echo lang('client'); ?></label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                       autofocus="autofocus"
                    <?php
                        if (isset($client_name))
                            echo 'value="' . html_escape($client_name) . '"';
 ?>>
            </div>
            <div class="form-group">
                <label for="client_name_input"><?php echo lang('client_name'); ?></label>
                <input type="text" name="client_name_input" id="client_name_input" class="form-control"
                       <?php
                    if (isset($client_name))
                        echo 'value="' . html_escape($client_name) . '"';
 ?> >
            </div>
            <div class="form-group">
                <label for="client_reg_number"><?php echo lang('client_reg_number'); ?></label>
                <input type="text" name="client_reg_number" id="client_reg_number" class="form-control"
                       <?php
                    if (isset($client_reg_number))
                        echo 'value="' . html_escape($client_reg_number) . '"';
 ?>>
            </div>
            <div class="form-group">
                <label for="client_address_1"><?php echo lang('street_address'); ?></label>
                <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                       <?php
                    if (isset($client_address_1))
                        echo 'value="' . html_escape($client_address_1) . '"';
 ?>>
            </div>
            <div class="form-group">
                <label for="client_vat_id"><?php echo lang('vat_id'); ?></label>
                <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                       <?php
                    if (isset($client_vat_id))
                        echo 'value="' . html_escape($client_vat_id) . '"';
 ?>>
            </div>

            <div class="form-group has-feedback">
                <label for="invoice_date_created"><?php echo lang('invoice_date'); ?>: </label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created" class="form-control datepicker" value="<?php echo date_from_mysql(date('Y-m-d', time()), TRUE) ?>">
	                <label for="invoice_date_created" class="input-group-btn">
		                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
	                </label>
                </div>
            </div>

            <div class="form-group">
                <label for="invoice_password"><?php echo lang('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php
                    if ($this->Mdl_settings->setting('invoice_pre_password') == '')
                    {
                        echo '';
                    }
                    else
                    {
                        echo $this->Mdl_settings->setting('invoice_pre_password');
                    }
 ?>" style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id"><?php echo lang('invoice_group'); ?>: </label>

                <div>
                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                        <option value=""></option>
                        <?php foreach ($invoice_groups as $invoice_group) { ?>
                            <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->Mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?php echo $invoice_group->invoice_group_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder" id="copy_invoice_confirm" type="button">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
