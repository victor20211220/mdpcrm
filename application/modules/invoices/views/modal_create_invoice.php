<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#create-invoice').modal('show');

        $('#create-invoice').on('shown', function () {
            $("#client_name").focus();
        });

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
                initSelection: function (element, callback) {

                    <?php if ($client_id){ ?>

                    var data = {
                        id: '<?= html_escape($client_id);?>',
                        text: '<?= html_escape($client_name);?>'
                    };
                    callback(data);

                    <?php } ?>
                },
                data: [
                    <?php
                    $i = 0;
                    foreach ($clients as $client) {
                        echo "{
                        id: '" . str_replace("'", "\'", $client->client_id) . "',
                        text: '" . str_replace("'", "\'", $client->client_name) . "',
                        client_reg_number: '" . str_replace("'", "\'", $client->client_reg_number) . "',
                        client_address_1: '" . str_replace("'", "\'", $client->client_address_1) . "',
                        client_vat_id: '" . str_replace("'", "\'", $client->client_vat_id) . "'
                        }";
                        if (($i + 1) != count($clients)) {
                            echo ',';
                        }
                        $i++;
                    }
                    ?>
                ]
            });

            $("[name='client_name']").on("select2-selecting", function (e) {

                if (e.choice.client_vat_id == undefined) {

                    //$('#new_client_on_invoice').val(1);

                    $('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");
                    ;
                    $('#client_id').val('-1');
                    $('#client_reg_number').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    ;
                    $('#client_address_1').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    ;
                    $('#client_vat_id').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    ;

                    $("#client_reg_number").focus();
                } else {
                    //$('#new_client_on_invoice').val(0);

                    $('#client_id').val(e.choice.id);
                    $('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");

                    if (e.choice.client_reg_number == '') $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', false).attr("placeholder", "Type value here");
                    else $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', true).attr("placeholder", "");

                    if (e.choice.client_address_1 == '') $('#client_address_1').val(e.choice.client_address_1).prop('disabled', false).attr("placeholder", "Type value here");
                    else $('#client_address_1').val(e.choice.client_address_1).prop('disabled', true).attr("placeholder", "");

                    if (e.choice.client_vat_id == '') $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', false).attr("placeholder", "Type value here");
                    else $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', true).attr("placeholder", "");

                }

            });

            if ($('#client_reg_number').val() == '') $('#client_reg_number').prop('disabled', false).attr("placeholder", "Type value here");
            else $('#client_reg_number').prop('disabled', true).attr("placeholder", "");

            if ($('#client_address_1').val() == '') $('#client_address_1').prop('disabled', false).attr("placeholder", "Type value here");
            else $('#client_address_1').prop('disabled', true).attr("placeholder", "");

            if ($('#client_vat_id').val() == '') $('#client_vat_id').prop('disabled', false).attr("placeholder", "Type value here");
            else $('#client_vat_id').prop('disabled', true).attr("placeholder", "");
            $('#client_name_input').prop('disabled', true).attr("placeholder", "");

            $("#client_name").focus();

        });

        // Creates the invoice
        $('#invoice_create_confirm').click(function () {
            // Posts the data to validate and create the invoice;
            // will create the new client if necessar
            $.post("<?= site_url('invoices/ajax/create'); ?>", {

                    client_id: $('#client_id').val(),
                    client_name: $('#client_name').val(),
                    client_reg_number: $('#client_reg_number').val(),
                    client_address_1: $('#client_address_1').val(),
                    client_vat_id: $('#client_vat_id').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_time_created: '<?= date('H:i:s') ?>',
                    invoice_password: $('#invoice_password').val(),
                    user_id: '<?= $this->session->userdata('user_id'); ?>',
                    payment_method: $('#payment_method_id').val()
                },
                function (data) {
                    console.log(data);
                    var response = JSON.parse(data);

                    if (response.success == '1') {
                        // The validation was successful and invoice was created
                        window.location = '/invoices/view/' + response.invoice_id + '/1';
                    } else {
                        // The validation was not successful
                        //$('.control-group').removeClass('has-error');
                        $('.form-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="create-invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close">
                <i class="fa fa-close"></i>
            </a>
            <h3><?= lang('create_invoice'); ?></h3>
        </div>
        <div class="modal-body row">
            <input class="hidden" name='new_client_on_invoice' id="new_client_on_invoice" value='0'>
            <input class="hidden" name='client_id' id="client_id" <?php if ($client_id) {
                echo 'value="' . html_escape($client_id) . '"';
            } ?>>
            <input class="hidden" id="payment_method_id"
                   value="<?= $this->Mdl_settings->setting('invoice_default_payment_method'); ?>">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="client_name"><?= lang('client'); ?></label>
                    <input type="text" name="client_name" id="client_name" class="form-control"
                           autofocus="autofocus"
                        <?php if ($client_name) {
                            echo 'value="' . html_escape($client_name) . '"';
                        } ?>>
                </div>
                <div class="form-group padder-v-xs">
                    <label for="client_name_input"><?= lang('client_name'); ?></label>
                    <input type="text" name="client_name_input" id="client_name_input" class="form-control"
                        <?php if ($client_name) {
                            echo 'value="' . html_escape($client_name) . '"';
                        } ?> >
                </div>
                <div class="form-group padder-v-xs">
                    <label for="client_reg_number"><?= lang('client_reg_number'); ?></label>
                    <input type="text" name="client_reg_number" id="client_reg_number" class="form-control"
                        <?php if ($client_reg_number) {
                            echo 'value="' . html_escape($client_reg_number) . '"';
                        } ?>>
                </div>
                <div class="form-group padder-v-xs">
                    <label for="client_address_1"><?= lang('street_address'); ?></label>
                    <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                        <?php if ($client_address_1) {
                            echo 'value="' . html_escape($client_address_1) . '"';
                        } ?>>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="client_vat_id"><?= lang('vat_id'); ?></label>
                    <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                        <?php if ($client_vat_id) {
                            echo 'value="' . html_escape($client_vat_id) . '"';
                        } ?>>
                </div>
                <div class="form-group padder-v-xs has-feedback" style="padding-top: 10px !important;">
                    <label><?= lang('invoice_date'); ?></label>

                    <div class="input-group">
                        <input name="invoice_date_created" id="invoice_date_created" class="form-control datepicker"
                               value="<?= date(date_format_setting()); ?>">
                        <label for="invoice_date_created" class="input-group-btn">
                            <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                        </label>
                    </div>
                </div>

                <div class="form-group padder-v-xs">
                    <label for="invoice_password"><?= lang('invoice_password'); ?></label>
                    <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                           value="<?php if ($this->Mdl_settings->setting('invoice_pre_password') == '') {
                               echo '';
                           } else {
                               echo $this->Mdl_settings->setting('invoice_pre_password');
                           } ?>" style="margin: 0 auto;" autocomplete="off">
                </div>
                <div class="form-group padder-v-xs">
                    <label><?= lang('invoice_group'); ?></label>

                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">

                        <?php foreach ($invoice_groups as $invoice_group) { ?>
                            <option value="<?= $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->Mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?= $invoice_group->invoice_group_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group padder-v">
                <div class="btn-group pull-right">
                    <button class="btn btn-gray padder" type="button" data-dismiss="modal"
                            style="margin-left: 15px;margin-right: 10px">
                        <?= lang('cancel'); ?>
                    </button>
                    <button class="btn btn-success padder btn-lg ajax-loader" id="invoice_create_confirm"
                            style="margin-right: 15px;"
                            type="button">
                        <?= lang('continue'); ?>
                    </button>

                </div>
            </div>
        </div>
    </form>

</div>
