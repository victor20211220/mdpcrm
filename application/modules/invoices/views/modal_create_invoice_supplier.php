<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#create-invoice').modal('show');
        $('#create-invoice').on('shown', function () {
            $("#supplier_name").focus();
        });

        $().ready(function () {
            $("[name='supplier_name']").select2({
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                multiple: false,
                allowClear: true,
                placeholder: 'Search by supplier name or type new supplier and press enter ...',
                initSelection: function (element, callback) {

                    <?php if ($supplier_id){ ?>
                    var data = {
                        id: '<?= html_escape($supplier_id); ?>', text: '<?= html_escape($supplier_name); ?>
                        '};
                            callback(data);
                    <?php } ?>
                },
                    data: [
                        <?php
                        $i = 0;
                        foreach ($suppliers as $supplier) {
                            echo "{
                        id: '" . str_replace("'", "\'", $supplier->supplier_id) . "',
                        text: '" . str_replace("'", "\'", $supplier->supplier_name) . "',
                        supplier_reg_number: '" . str_replace("'", "\'", $supplier->supplier_reg_number) . "',
                        supplier_address_1: '" . str_replace("'", "\'", $supplier->supplier_address_1) . "',
                        supplier_vat_id: '" . str_replace("'", "\'", $supplier->supplier_vat_id) . "'
                        }";
                            if (($i + 1) != count($suppliers)) {
                                echo ',';
                            }
                            $i++;
                        }
                        ?>
                    ]
                });

            $("[name='supplier_name']").on("select2-selecting", function (e) {
                if (e.choice.supplier_vat_id == undefined) {
                    $('#supplier_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");
                    $('#supplier_id').val('-1');
                    $('#supplier_reg_number').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    $('#supplier_address_1').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    $('#supplier_vat_id').val('').prop('disabled', false).attr("placeholder", "Type value here");
                    $("#supplier_reg_number").focus();
                } else {
                    $('#supplier_id').val(e.choice.id);
                    $('#supplier_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");

                    if (e.choice.supplier_reg_number == '') {
                        $('#supplier_reg_number').val(e.choice.supplier_reg_number).prop('disabled', false);
                    }  else {
                        $('#supplier_reg_number').val(e.choice.supplier_reg_number).prop('disabled', true);
                    }

                    if (e.choice.supplier_address_1 == '') {
                        $('#supplier_address_1').val(e.choice.supplier_address_1).prop('disabled', false);
                    } else {
                        $('#supplier_address_1').val(e.choice.supplier_address_1).prop('disabled', true);
                    }

                    if (e.choice.supplier_vat_id == '') {
                        $('#supplier_vat_id').val(e.choice.supplier_vat_id).prop('disabled', false);
                    } else {
                        $('#supplier_vat_id').val(e.choice.supplier_vat_id).prop('disabled', true);
                    }
                }
            });

            if ($('#supplier_reg_number').val() == '') {
                $('#supplier_reg_number').prop('disabled', false).attr("placeholder", "Type value here");
            } else {
                $('#supplier_reg_number').prop('disabled', true).attr("placeholder", "");
            }

            if ($('#supplier_address_1').val() == '') {
                $('#supplier_address_1').prop('disabled', false).attr("placeholder", "Type value here");
            } else {
                $('#supplier_address_1').prop('disabled', true).attr("placeholder", "");
            }

            if ($('#supplier_vat_id').val() == '') {
                $('#supplier_vat_id').prop('disabled', false).attr("placeholder", "Type value here");
            } else {
                $('#supplier_vat_id').prop('disabled', true).attr("placeholder", "");
            }

            $('#supplier_name_input').prop('disabled', true).attr("placeholder", "");
            $("#supplier_name").focus();
        });

        // Creates the invoice
        $('#invoice_create_confirm').click(function () {
            $.post("/invoices/ajax/create", {
                    supplier_id: $('#supplier_id').val(),
                    supplier_name: $('#supplier_name').val(),
                    supplier_reg_number: $('#supplier_reg_number').val(),
                    supplier_address_1: $('#supplier_address_1').val(),
                    supplier_vat_id: $('#supplier_vat_id').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    invoice_time_created: '<?= date('H:i:s') ?>',
                    invoice_password: $('#invoice_password').val(),
                    user_id: '<?= $this->session->userdata('user_id'); ?>',
                    payment_method: $('#payment_method_id').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = '/invoices/view/' + response.invoice_id;
                    } else {
                        $('.form-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="create-invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">

    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('create_invoice'); ?></h3>
        </div>
        <div class="modal-body row">
            <input class="hidden" name='new_supplier_on_invoice' id="new_supplier_on_invoice" value='0'>
            <input class="hidden" name='supplier_id'
                   id="supplier_id" <?= $supplier_id ? html_escape($supplier_id) : null; ?>>
            <input class="hidden" id="payment_method_id"
                   value="<?= $this->Mdl_settings->setting('invoice_default_payment_method'); ?>">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="supplier_name"><?= lang('supplier'); ?></label>
                    <input type="text" name="supplier_name" id="supplier_name" class="form-control"
                           autofocus="autofocus"
                           value="<?= $supplier_name ? html_escape($supplier_name) : null; ?>"
                    />
                </div>
                <div class="form-group">
                    <label for="supplier_name_input"><?= lang('supplier_name'); ?></label>
                    <input type="text" name="supplier_name_input" id="supplier_name_input" class="form-control"
                        <?php
                        if ($supplier_name) {
                            echo 'value="' . html_escape($supplier_name) . '"';
                        }
                        ?>
                    />
                </div>
                <div class="form-group">
                    <label for="supplier_reg_number"><?= lang('supplier_reg_number'); ?></label>
                    <input type="text" name="supplier_reg_number" id="supplier_reg_number" class="form-control"
                        value="<?= $supplier_reg_number ? html_escape($supplier_reg_number) : null; ?>"
                    />
                </div>
                <div class="form-group">
                    <label for="supplier_address_1"><?= lang('street_address'); ?></label>
                    <input type="text" name="supplier_address_1" id="supplier_address_1" class="form-control"
                        value="<?= $supplier_address_1 ? html_escape($supplier_address_1) : null; ?>"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="supplier_vat_id"><?= lang('vat_id'); ?></label>
                    <input type="text" name="supplier_vat_id" id="supplier_vat_id" class="form-control"
                        value="<?= $supplier_vat_id ? html_escape($supplier_vat_id) : null; ?>"
                    />
                </div>
                <div class="form-group has-feedback">
                    <label><?= lang('invoice_date'); ?></label>

                    <div class="input-group">
                        <input name="invoice_date_created" id="invoice_date_created" class="form-control datepicker"
                               value="<?= date(date_format_setting()); ?>">
                        <label for="invoice_date_created" class="input-group-btn">
                            <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="invoice_password"><?= lang('invoice_password'); ?></label>
                    <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                           value="<?= $this->Mdl_settings->setting('invoice_pre_password') ?: ''; ?>"
                           style="margin: 0 auto;" autocomplete="off"
                    />
                </div>
                <div class="form-group">
                    <label><?= lang('invoice_group'); ?></label>

                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                        <option value=""></option>
                        <?php foreach ($invoice_groups as $invoice_group) : ?>
                            <option value="<?= $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->Mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>>
                                <?= $invoice_group->invoice_group_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="pull-left">
                <div class="btn-group">
                    <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                        <?= lang('cancel'); ?>
                    </button>
                    <button class="btn btn-success padder ajax-loader" id="invoice_create_confirm" type="button">
                        <?= lang('save'); ?>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
