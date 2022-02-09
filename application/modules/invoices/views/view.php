<style>
    .special-side {
        border-left: none !important;
    }
</style>

<script src="/assets/responsive/js/invoiceModule.js"></script>
<script src="/assets/responsive/js/lrtrim.js"></script>
<script type="text/javascript">
    var item_tax_rates_preloaded = new Array();
    item_tax_rates_preloaded[0] = "0";

    <?php foreach ($tax_rates as $tax_rate) { ?>
    item_tax_rates_preloaded[<?= $tax_rate->tax_rate_id; ?>] = "<?= $tax_rate->tax_rate_percent;?>";
    <?php } ?>

    var currencySymbolPlacement = "<?= $this->Mdl_settings->setting('currency_symbol_placement'); ?>";

    function hide_pdf_button() {
        $(".btn_view_pdf,#btn_generate_pdf").hide();
    }

    function set_trigger_discount_item() {
        $('input[name=item_discount_percent]').change(function () {
            calculate_amounts(false);
            hide_pdf_button();
        });
    }

    $(function () {
        $('.btn_add_product').click(function (e) {
            e.preventDefault();
            hide_pdf_button();
            $('#modal-placeholder').load('/products/ajax/modal_product_lookups/' + Math.floor(Math.random() * 1000));
        });

        $(document).on('click', '.add-description', function (e) {
            $(this).next().toggleClass("hidden");
        });

        $('.btn_view_pdf').click(function (e) {
            e.preventDefault();
            window.open('<?= "/invoices/generate_pdf/{$invoice_id}"; ?>', '_blank');
        });

        <?php if (!$items) { ?>
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item_list').show().find('input[type=text]').filter(':first').focus();
            calculate_amounts();
            reload_amounts_on_change();
        <?php } ?>

        $('#btn_create_recurring,#btn_create_recurring').click(function () {
            $('#modal-placeholder').load('/invoices/ajax/modal_create_recurring', {invoice_id: <?= $invoice_id; ?>});
        });

        <?php
            if ($invoice->is_received == 0) {
                $invoiceAjaxUrl = '/invoices/ajax/modal_change_client';
                $clientName = $this->db->escape_str($invoice->client_name);
            } else {
                $invoiceAjaxUrl = '/invoices/ajax/modal_change_supplier';
                $clientName = $this->db->escape_str($invoice->supplier_name);
            }
        ?>

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load('<?=$invoiceAjaxUrl; ?>', {
                invoice_id: <?= $invoice_id; ?>,
                client_name: "<?= $clientName; ?>"
            });
        });

        $("#invoice_form").change(function () {
            $('#btn_save_invoice,#btn_save_invoice_2').removeClass("saved");
        });

        $('#btn_save_invoice,#btn_save_invoice_2').click(function () {
            if ($(this).hasClass("saved")) return false;
            var items = [];
            var item_order = 1;

            $('table tbody.item_list').each(function () {
                var row = {};
                var firstTextField = $(this).find('#tags');
                if (firstTextField.val() == '') {
                    firstTextField.closest('tbody').remove();
                    return;
                }

                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });

                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });

            $.post('/invoices/ajax/save', {
                    invoice_id: <?= $invoice_id; ?>,
                    is_received: $('#is_received').val(),
                    invoice_number: $('#invoice_number').val(),
                    invoice_date_created: $('#invoice_date_created').val(),
                    invoice_date_due: $('#invoice_date_due').val(),
                    invoice_status_id: $('#invoice_status_id').val(),
                    invoice_password: $('#invoice_password').val(),
                    items: JSON.stringify(items),
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    custom: $('input[name^=custom]').serializeArray(),
                    payment_method: $('#payment_method').val()
                },

                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location = "<?= site_url('invoices/view'); ?>/" + <?= $invoice_id; ?>+"";
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';

                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }

                        $('#invoice_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $('#btn_generate_pdf,#btn_generate_pdf_2').click(function () {
            window.open('<?= site_url('invoices/generate_pdf/' . $invoice_id); ?>', '_blank');
        });

        <?php if ($invoice->is_read_only != 1): ?>

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();

            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });

            return $helper;
        };

        $("#item_table").sortable({
            items: 'tbody',
            helper: fixHelper
        });

        $(document).ready(function () {
            if ($('#invoice_discount_percent').val().length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            }

            if ($('#invoice_discount_amount').val().length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            }
        });

        $('#invoice_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_percent').prop('disabled', true);
            } else {
                $('#invoice_discount_percent').prop('disabled', false);
            }
        });

        $('#invoice_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#invoice_discount_amount').prop('disabled', true);
            } else {
                $('#invoice_discount_amount').prop('disabled', false);
            }
        });

        set_trigger_discount_item();
        reload_amounts_on_change();

        <?php endif; ?>
    });


</script>
<style>
    .input-group-addon {
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
        border-left: none !important;
    }
</style>

<?php
    echo $modal_delete_invoice;
    echo $modal_add_invoice_tax;

    if ($this->config->item('disable_read_only') == TRUE) {
        $invoice->is_read_only = 0;
    }
?>

<div class="bg-light lter b-b wrapper-md">
    <div class='row' style="margin-bottom: 10px">
        <div class="col-sm-8 col-xs-12" style="margin-top: 10px !important">
            <h1 class="m-n font-thin h3">
                <span class='gray-custom-2'><strong><?= lang('invoice'); ?></strong></span>
                <span class='gray-custom-1'>#<?= $invoice->invoice_number; ?></span>
            </h1>
        </div>
        <div class="col-sm-4 col-xs-12 text-right pull-right" style="margin-top: 10px !important">
            <ul class="nav nav-pills nav-sm">
                <?php if ($invoice_is_recieved == false) { ?>
                    <li>
                        <a class="btn btn-gray dropdown-toggle btn-sm padder-h" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog "></i>       <?= lang('options'); ?></i>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($invoice->is_read_only != 1 && 1 == 2) : ?>
                                <li>
                                    <a href="#add-invoice-tax" data-toggle="modal">
                                        <i class="fa fa-plus fa-margin"></i> <?= lang('add_invoice_tax'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="#" id="btn_create_credit" data-invoice-id="<?= $invoice_id; ?>">
                                    <i class="fa fa-minus fa-margin"></i> <?= lang('create_credit_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="invoice-add-payment"
                                   data-invoice-id="<?= $invoice_id; ?>"
                                   data-invoice-balance="<?= $invoice->invoice_balance; ?>"
                                   data-invoice-payment-method="<?= $invoice->payment_method; ?>"
                                >
                                    <i class="fa fa-credit-card fa-margin"></i>
                                    <?= lang('enter_payment'); ?>
                                </a>
                            </li>
                            <?php if (count($items) > 0) { ?>
                                <li>
                                    <a href="#" id="btn_generate_pdf"
                                       data-invoice-id="<?= $invoice_id; ?>">
                                        <i class="fa fa-file-pdf-o fa-margin"></i>
                                        <?= lang('download_pdf'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href='/mailer/invoice/<?= $invoice->invoice_id; ?>'>
                                    <i class="fa fa-send fa-margin"></i>
                                    <?= lang('send_email'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" id="btn_create_recurring"
                                   data-invoice-id="<?= $invoice_id; ?>">
                                    <i class="fa fa-repeat fa-margin"></i>
                                    <?= lang('create_recurring'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" id="btn_copy_invoice"
                                   data-invoice-id="<?= $invoice_id; ?>">
                                    <i class="fa fa-copy fa-margin"></i>
                                    <?= lang('copy_invoice'); ?>
                                </a>
                            </li>
                            <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === TRUE && $invoice->is_read_only != 1)) { ?>
                                <li>
                                    <a href="#delete-invoice" data-toggle="modal">
                                        <i class="fa fa-trash-o fa-margin"></i>
                                        <?= lang('delete'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($invoice->is_read_only != 1) { ?>
                    <?php if (count($items) > 0) { ?>
                        <li>
                            <a href="#" class="btn_view_pdf btn btn-sm btn-gray padder-h">
                                <i class="fa fa-file-pdf-o fa-margin"></i>
                                <?= lang('download_pdf'); ?>
                            </a>
                        </li>
                    <?php } ?>
                <li>
                    <?php }
                    //if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {

                    if ($invoice->is_read_only != 1) { ?>
                        <a href="#" class="btn btn-sm btn-success padder-h ajax-loader<?= $saved ? ' saved' : ''; ?>"
                           id="btn_save_invoice">
                            <i class="fa fa-floppy-o"></i> <?= $saved ? lang('saved') : lang('save'); ?>
                        </a>
                    <?php } ?>
                </li>
                <?php if ($invoice->invoice_is_recurring) { ?>
                    <li class='invoice-labels'>

                        <span class="label label-info"><?= lang('recurring'); ?></span>

                    </li>
                <?php } ?>
                <?php if ($invoice->is_read_only == 1) { ?>
                    <li class='invoice-labels'>

               <span class="label label-danger">
               <i class="fa fa-read-only"></i> <?= lang('read_only'); ?>
               </span>

                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <?= $this->layout->load_view('layout/alerts'); ?>
        <form id="invoice_form">
            <input type='hidden' id='is_received' name='is_received' value='<?= $invoice->is_received; ?>'>
            <div class="invoice">
                <div class="form-group cf row">
                    <div class="col-xs-12 col-md-5">
                        <?php
                        //no matter if manually added just a normal invoice

                        if ($invoice_is_recieved == 0) { ?>
                        <div class="pull-left">
                            <h2>
                                <?php if ($invoice->client_id != 0){ ?>
                                <a class="blueheader" href='/clients/view/<?= $invoice->client_id; ?>'>
                                    <?= $invoice->client_name; ?>
                                </a>
                                <?php if ($invoice->invoice_status_id == 1) { ?>
                                    <span id="invoice_change_client" class="fa fa-pencil cursor-pointer small green"
                                          data-toggle="tooltip" data-placement="bottom"
                                          title="<?= lang('change_client'); ?>"></span>
                                <?php } ?>
                            </h2>
                            <br>
                            <span>
                     <?= ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                     <?= ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                     <?php
                     $address = array();
                     if ($invoice->client_city)
                         $address[] = $invoice->client_city;
                     if ($invoice->client_state)
                         $address[] = $invoice->client_state;
                     if ($invoice->client_zip)
                         $address[] = $invoice->client_zip;

                     echo join(', ', $address);
                     ?>
                     <?= ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
                     </span>
                            <br><br>
                            <?php if ($invoice->client_phone) { ?>
                                <span><strong><?= lang('phone'); ?>
                                        :</strong> <?= $invoice->client_phone; ?></span><br>
                            <?php } ?>
                            <?php if ($invoice->client_email) { ?>
                                <span><strong><?= lang('email'); ?>
                                        :</strong> <?= $invoice->client_email; ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                        <?php if ($invoice->client_id == 0){ ?>
                        <a href='/clients/view/<?= $invoice->supplier_id; ?>'>
                            <?= $invoice->supplier_name; ?>
                        </a>
                        <?php if ($invoice->invoice_status_id == 1) { ?>
                            <span id="invoice_change_client" class="fa fa-pencil cursor-pointer small"
                                  data-toggle="tooltip" data-placement="bottom"
                                  title="<?= lang('change_supplier'); ?>"></span>
                        <?php } ?>
                        </h2><br>
                        <span>
                  <?= ($invoice->supplier_address_1) ? $invoice->supplier_address_1 . '<br>' : ''; ?>
                  <?= ($invoice->supplier_address_2) ? $invoice->supplier_address_2 . '<br>' : ''; ?>
                  <?= ($invoice->supplier_city) ? $invoice->supplier_city : ''; ?>
                  <?= ($invoice->supplier_state) ? $invoice->supplier_state : ''; ?>
                  <?= ($invoice->supplier_zip) ? $invoice->supplier_zip : ''; ?>
                  <?= ($invoice->supplier_country) ? '<br>' . $invoice->supplier_country : ''; ?>
                  </span>
                        <br><br>
                        <?php if ($invoice->supplier_phone) { ?>
                            <span><strong><?= lang('phone'); ?>
                                    :</strong> <?= $invoice->supplier_phone; ?></span><br>
                        <?php } ?>
                        <?php if ($invoice->supplier_email) { ?>
                            <span><strong><?= lang('email'); ?>
                                    :</strong> <?= $invoice->supplier_email; ?></span>
                        <?php } ?>
                    </div>
                    <div class="pull-left">
                        <h2>
                            <?= $sender_company['company_name']; ?>
                        </h2>
                        <br>
                        <span>
                  <?= ($sender_company['company_address']) ? $sender_company['company_address'] . '<br>' : ''; ?>
                  <?= ($sender_company['company_code']) ? $sender_company['company_code'] . '<br>' : ''; ?>
                  <?= ($sender_company['company_vatregnumber']) ? $sender_company['company_vatregnumber'] . '<br>' : ''; ?>
                  <?= ($sender_company['company_iban']) ? $sender_company['company_iban'] . '<br>' : ''; ?>
                  </span>
                        <br>
                    </div>
                    <?php }
                    }
                        else {
                            $supplier_details = $this->db->get_where('ip_suppliers', array('supplier_id' => $invoice->supplier_id))->row_array();
                            // print_r($supplier_details);
                            $long_addr = "city-state-zip not available.";
                            if(!empty($supplier_details['supplier_city'].$supplier_details['supplier_stat'].$supplier_details['supplier_zip'])){
                                $long_address = array();
                                $long_address[] = $supplier_details['supplier_city'];
                                $long_address[] = $supplier_details['supplier_state'];
                                $long_address[] = $supplier_details['supplier_zip'];
                                $long_addr = implode(", " , $long_address);
                            }
                            echo '<div class="pull-left">
                           <h2>
                              ' . $supplier_details['supplier_name'] . '
                           </h2>
                           <br>
                           <span>
                           ' . $supplier_details['supplier_address_1'] . ' ' . $supplier_details['supplier_address_2'] . '<br />
                           ' . $long_addr . '<br />
                           ' . $supplier_details['supplier_country'] . '
                           </span>
                           <br>
                           <br><br>
    
                           <span><strong>' . lang('phone') . ':</strong> ' . $supplier_details['supplier_phone'] . '</span><br>
                           <span><strong>' . lang('email') . '
                           :</strong> ' . $supplier_details['supplier_email'] . '</span>
                            </div>';

                        } ?>
                </div>
                <div class="col-xs-12 col-md-6 col-md-offset-1">
                    <div class="details-box">
                        <div class="row">
                            <?php if ($invoice->invoice_sign == -1) { ?>
                                <!--
                        <div class="col-xs-12">

                        <span class="label label-warning">

                            <i class="fa fa-credit-invoice"></i>&nbsp;

                            <?= lang('credit_invoice_for_invoice') . ' ';
                                echo anchor('/invoices/view/' . $invoice->creditinvoice_parent_id,

                                    $invoice->creditinvoice_parent_id) ?>

                        </span>

                        </div>
                        -->
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="invoice-properties">
                                    <label><?= lang('invoice'); ?> #</label>
                                    <input type="text" id="invoice_number"
                                           class="input-sm form-control"
                                           value="<?= $invoice->invoice_number; ?>"
                                        <?php if ($invoice->is_read_only == 1) {
                                            echo 'disabled="disabled"';

                                        } ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="invoice-properties">
                                    <label><?= lang('payment_method'); ?> <span
                                                class="small">(<?= lang('optional'); ?>)</span></label>
                                    <select name="payment_method" id="payment_method" class="form-control input-sm"
                                        <?php if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                            echo 'disabled="disabled"';

                                        } ?>>
                                        <option value=""><?= lang('select_payment_method'); ?></option>
                                        <?php foreach ($payment_methods as $payment_method) { ?>
                                            <option <?php if ($invoice->payment_method == $payment_method->payment_method_id) echo "selected" ?>
                                                    value="<?= $payment_method->payment_method_id; ?>">
                                                <?= $payment_method->payment_method_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-12 col-sm-4" style="padding-right: 0px !important;">
                                <div class="invoice-properties">
                                    <label><?= lang('date'); ?></label>
                                    <div class="input-group">
                                        <input name="invoice_date_created" id="invoice_date_created"
                                               class="form-control datepicker"
                                               value="<?= date_from_mysql($invoice->invoice_date_created); ?>"
                                            <?php if ($invoice->is_read_only == 1) {
                                                echo 'disabled="disabled"';

                                            } ?>
                                        />
                                        <label for="invoice_date_created" class="input-group-btn">
                                            <span class="btn btn-default special-side"><i
                                                        class="fa fa-calendar fa-fw"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" style="padding-right: 0px !important;">
                                <label><?= lang('due_date'); ?></label>
                                <div class="input-group">
                                    <input name="invoice_date_due" id="invoice_date_due" class="form-control datepicker"
                                           value="<?= date_from_mysql($invoice->invoice_date_due); ?>"
                                        <?php if ($invoice->is_read_only == 1) {
                                            echo 'disabled="disabled"';

                                        } ?>
                                    />
                                    <label for="invoice_date_due" class="input-group-btn">
                                        <span class="btn btn-default special-side"><i class="fa fa-calendar fa-fw"></i></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="invoice-properties">
                                    <label><?= lang('status');
                                        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
                                            echo ' <span class="small">(' . lang('can_be_changed') . ')</span>';
                                        }
                                        ?>
                                    </label>
                                    <select name="invoice_status_id" id="invoice_status_id"
                                            class="form-control"
                                        <?php if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                            echo 'disabled="disabled"';

                                        } ?>>
                                        <?php foreach ($invoice_statuses as $key => $status) { ?>
                                            <option value="<?= $key; ?>"
                                                    <?php if ($key == $invoice->invoice_status_id) { ?>selected="selected"<?php } ?>>
                                                <?= $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="invoice-properties hidden">
                                <label><?= lang('invoice_password'); ?></label>
                                <input type="text" id="invoice_password"
                                       class="input-sm form-control"
                                       value="<?= $invoice->invoice_password; ?>"
                                    <?php if ($invoice->is_read_only == 1) {
                                        echo 'disabled="disabled"';

                                    } ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <?php $this->layout->load_view('invoices/partial_item_table'); ?>
    <hr/>
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            <label><?= lang('invoice_terms'); ?></label>
            <textarea id="invoice_terms" name="invoice_terms" class="form-control" rows="3"
                <?php if ($invoice->is_read_only == 1) {
                    echo 'disabled="disabled"';

                } ?>
            ><?= $invoice->invoice_terms; ?></textarea>
        </div>
        <div class="col-xs-12 col-sm-4 hidden">
            <label class="control-label"><?= lang('attachments'); ?></label>
            <br/>
            <!-- The fileinput-button span is used to style the file input field as button -->
            <?php if ($invoice_is_recieved == false) { ?>
                <span class="btn btn-default fileinput-button">
   <i class="fa fa-plus"></i>
   <span><?= lang('add_files'); ?></span>
   </span>
            <?php } ?>
            <!-- dropzone -->
            <div id="actions">
                <div class='MrgTop10'>
                    <!-- The global file processing state -->
                    <span class="fileupload-process">
   <div id="total-progress" class="progress progress-striped active"
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="0"
        style="opacity: 0;">
   <div class="progress-bar progress-bar-success" style="width:0%;"
        data-dz-uploadprogress></div>
   </div>
   </span>
                </div>
                <div id="previews" class="table table-condensed table-striped files">
                    <div id="template" class="file-row">
                        <!-- This is used as the file preview template -->
                        <div>
                            <span class="preview"><img data-dz-thumbnail/></span>
                        </div>
                        <div>
                            <p class="name" data-dz-name></p>
                            <strong class="error text-danger" data-dz-errormessage></strong>
                        </div>
                        <div>
                            <p class="size" data-dz-size></p>
                            <div class="progress progress-striped active" role="progressbar"
                                 aria-valuemin="0"
                                 aria-valuemax="100"
                                 aria-valuenow="0"
                                 style="opacity: 0;display:none"
                            >
                                <div class="progress-bar progress-bar-success" style="..."
                                     data-dz-uploadprogress></div>
                            </div>
                        </div>
                        <div>
                            <?php if ($invoice->is_read_only != 1) { ?>
                                <button data-dz-remove class="btn btn-default btn-sm delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?= lang('delete'); ?></span>
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- stop dropzone -->
        </div>
    </div>
    <?php if ($custom_fields): ?>
        <h4 class="no-margin"><?= lang('custom_fields'); ?></h4>
    <?php endif; ?>
    <?php foreach ($custom_fields as $custom_field) { ?>
        <label><?= $custom_field->custom_field_label; ?></label>
        <input type="text" class="form-control"
               name="custom[<?= $custom_field->custom_field_column; ?>]"
               id="<?= $custom_field->custom_field_column; ?>"
               value="<?= html_escape($this->Mdl_invoices->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>"
            <?php if ($invoice->is_read_only == 1) {
                echo 'disabled="disabled"';

            } ?>>
    <?php } ?>
    <?php if ($invoice->invoice_status_id != 1) { ?>
        <p class="padded">
            <?= lang('guest_url'); ?>:
            <?= auto_link(site_url('guest/view/invoice/' . $invoice->invoice_url_key)); ?>
        </p>
    <?php } ?>
</div>
</form>
</div>
</div>

<script>
    // Get the template HTML and remove it from the document

    var previewNode = document.querySelector("#template");
    previewNode.id = "";

    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: '<?= "upload/upload_file/{$invoice->client_id}/{$invoice->invoice_url_key}" ?>', // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?= site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, '/uploads/customer_files/' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, '/assets/default/img/favicon.png');
                    }

                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });


    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '/assets/default/img/favicon.png');
    });


    // Update the total progress bar

    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });


    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1";
    });


    // Hide the total progress bar when nothing's uploading anymore

    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0";
    });


    myDropzone.on("removedfile", function (file) {
        $.ajax({
            url: "upload/delete_file/<?= $invoice->invoice_url_key; ?>",
            type: "POST",
            data: {'name': file.name}
        });

    });
</script>

<style type="text/css">
    .small {
        font-size: 10px !important;
        color: #81c555 !important;
    }
</style>
