<style>
    .special-side {
        border-left: none !important;
    }
</style>

<script src="/assets/responsive/js/lrtrim.js"></script>
<script src="/assets/responsive/js/invoiceModule.js"></script>
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
            window.open('<?= "/guest/view/invoice/{$invoice->invoice_url_key}"; ?>', '_blank');
        });

        <?php if (!$items) { ?>
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item_list').show().find('input[type=text]').filter(':first').focus();
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

            var supplierName = $('#supplier_name').val();
            var supplierVat = $('#supplier_vat').val();
            var supplierRegNumber = $('#supplier_reg_number').val();
            var supplierPhone = $('#supplier_phone').val();
            var supplierAddress = $('#supplier_address').val();

            if (supplierName == '') {
                alert('Enter supplier name');
                return false;
            }
            if (supplierVat == '') {
                alert('Enter supplier vat number');
                return false;
            }

            if (supplierRegNumber == '') {
                alert('Enter suppplier registration number');
                return false;
            }

            if (supplierPhone == '') {
                alert('Enter supplier phone number');
                return false;
            }

            if (supplierAddress == '') {
                alert('Enter supplier address');
                return false;
            }

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

            $.ajax({
                url: '<?= "/guest/invoices/save/{$companyHash}/{$invoiceHash}"; ?>',
                method: 'post',
                data: {
                    invoice_id: <?= $invoice_id; ?>,
                    items: JSON.stringify(items),
                    supplier_id: $('#supplier_id').val(),
                    supplier_name: supplierName,
                    supplier_phone: supplierPhone,
                    supplier_vat: supplierVat,
                    supplier_reg_number: supplierRegNumber,
                    supplier_address: supplierAddress,
                    invoice_discount_amount: $('#invoice_discount_amount').val(),
                    invoice_discount_percent: $('#invoice_discount_percent').val(),
                    invoice_terms: $('#invoice_terms').val(),
                    payment_method: $('#payment_method').val()
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        window.location.reload();
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
    if ($this->config->item('disable_read_only') == TRUE) {
        $invoice->is_read_only = 0;
    }
?>

<div class="bg-light lter b-b wrapper-md" style="padding-top: 30px !important;">

    <div class='row' style="margin-bottom: 5px">
        <div class="col-sm-8 col-xs-12">
            <?php if ($this->Mdl_settings->setting('invoice_logo')) : ?>
            <img class="img img-responsive invoice_logo_custom" style="max-width: 200px; max-height: 100px" align="left"
                 src="<?= site_url('uploads/' . $this->Mdl_settings->setting('invoice_logo')); ?>"
            />
            <?php endif; ?>
        </div>
        <div class="col-sm-4 col-xs-12 text-right pull-right" style="margin-top: 10px !important">
            <ul class="nav nav-pills nav-sm">
                <?php if ($invoice->is_read_only != 1) : ?>
                    <?php if (count($items) > 0) : ?>
                        <li>
                            <a href="#" class="btn_view_pdf btn btn-sm btn-default">
                                <i class="fa fa-file-pdf-o fa-margin"></i>
                                <?= lang('download_pdf'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <li>
                <?php endif; ?>
                <?php if ($invoice->is_read_only != 1) : ?>
                    <a href="#" class="btn btn-sm btn-success ajax-loader<?= $saved ? ' saved' : ''; ?>"
                       id="btn_save_invoice">
                        <i class="fa fa-floppy-o"></i> <?= $saved ? lang('saved') : 'Save invoice' ?>
                    </a>
                <?php endif; ?>
                </li>
                <?php if ($invoice->invoice_is_recurring) : ?>
                    <li class='invoice-labels'>
                        <span class="label label-info"><?= lang('recurring'); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($invoice->is_read_only == 1) : ?>
                    <li class='invoice-labels'>
                        <span class="label label-danger">
                            <i class="fa fa-read-only"></i> <?= lang('read_only'); ?>
                        </span>
                    </li>
                <?php endif; ?>
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
                    <div class="col-xs-12 col-md-6">
                        <?php
                        //no matter if manually added just a normal invoice

                        if ($invoice_is_recieved == 0) { ?>
                        <div class="pull-left">
                                <?php if ($invoice->client_id != 0){ ?>
                                <a class="blueheader" href='/clients/view/<?= $invoice->client_id; ?>'>
                                    <?= $invoice->client_name; ?>
                                </a>
                                <?php if ($invoice->invoice_status_id == 1) : ?>
                                    <span id="invoice_change_client" class="fa fa-pencil cursor-pointer small green"
                                          data-toggle="tooltip" data-placement="bottom"
                                          title="<?= lang('change_client'); ?>">
                                    </span>
                                <?php endif; ?>
                            <br>
                            <span>
                     <?= ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                     <?= ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                     <?php
                     $address = array();
                     if ($invoice->client_city) { $address[] = $invoice->client_city; }
                     if ($invoice->client_state) { $address[] = $invoice->client_state; }
                     if ($invoice->client_zip) { $address[] = $invoice->client_zip; }
                     echo join(', ', $address);
                     ?>
                     <?= ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
                     </span>
                            <br><br>
                            <?php if ($invoice->client_phone) : ?>
                                <span>
                                    <strong>
                                        <?= lang('phone'); ?> :
                                    </strong>
                                    <?= $invoice->client_phone; ?>
                                </span><br>
                            <?php endif; ?>
                            <?php if ($invoice->client_email) : ?>
                                <span>
                                    <strong><?= lang('email'); ?> :</strong> <?= $invoice->client_email; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                        <?php if ($invoice->client_id == 0){ ?>
                        <?php if ($invoice->supplier_name): ?>
                            <?= $invoice->supplier_name; ?>
                            <input type="hidden" name="supplier_name" id="supplier_name" value="<?= $invoice->supplier_name; ?>">
                        <?php else: ?>
                            <div class="form-inline">
                                <input type="text" class="form-control" style="width: 225px" name="supplier_name" id="supplier_name" placeholder="Supplier name">
                                <input type="text" class="form-control" style="width: 228px;" name="supplier_reg_number" id="supplier_reg_number" placeholder="Registration number">
                            </div>
                            <div class="form-inline" style="margin-top: 8px;">
                                <input type="text" class="form-control" style="width: 225px;" name="supplier_address" id="supplier_address" placeholder="Supplier registration address">
                                <input type="text" class="form-control" style="width: 112px;" name="supplier_vat" id="supplier_vat" placeholder="VAT number">
                                <input type="text" class="form-control" style="width: 112px;" name="supplier_phone" id="supplier_phone" placeholder="Phone number">

                            </div>
                        <?php endif; ?>


                        <br>
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
                            <span>
                                <strong><?= lang('phone'); ?>:</strong> <?= $invoice->supplier_phone; ?>
                            </span><br>
                        <?php } ?>
                        <?php if ($invoice->supplier_email) { ?>
                            <span>
                                <strong><?= lang('email'); ?> :</strong> <?= $invoice->supplier_email; ?>
                            </span>
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
                        echo '<div class="pull-left">
                       <h2>
                          ' . $supplier_details['supplier_name'] . '
                       </h2>
                       <br>
                       <span><strong>' . lang('company_address') . '</strong>: ' . $supplier_details['supplier_address_1'] . '</span><br>
                       <span><strong>' . lang('company_code') . '</strong>: ' . $supplier_details['supplier_reg_number'] . '</span><br>
                       <span><strong>' . lang('company_vat') . '</strong>: ' . $supplier_details['supplier_vat_id'] . '</span><br>
                       <span><strong>' . lang('phone') . '</strong>: ' . $supplier_details['supplier_phone'] . '</span><br><br>
                    </div>';

                        ?>

                    <?php } ?>
                </div>
                <div class="col-xs-12 col-md-4 col-md-offset-1">
                    <div class="details-box">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <div class="invoice-properties">
                                    <label style="padding-right: 15px">
                                        <?= lang('invoice'); ?> #
                                    </label>
                                    <?= $invoice->invoice_number; ?>
                                </div>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="col-md-3"></div>
                            <div class="col-md-4" style="padding-right: 0px !important;">
                                <div class="invoice-properties">
                                    <label><?= lang('date'); ?></label><br>
                                    <?= date_from_mysql($invoice->invoice_date_created); ?>
                                </div>
                            </div>
                            <div class="col-md-5" style="padding-right: 0px !important;">
                                <label><?= lang('due_date'); ?></label><br>
                                <input name="invoice_date_due" id="invoice_date_due" class="form-control datepicker"
                                       value="<?= date_from_mysql($invoice->invoice_date_due); ?>"
                                    <?php if ($invoice->is_read_only == 1) {
                                        echo 'disabled="disabled"';

                                    } ?>
                                />
                            </div>
                            <div class="invoice-properties hidden">
                                <label><?= lang('invoice_password'); ?></label>
                                <input type="text" id="invoice_password"
                                       class="input-sm form-control"
                                       value="<?= $invoice->invoice_password; ?>"
                                    <?php if ($invoice->is_read_only == 1) {
                                        echo 'disabled="disabled"';
                                    } ?>
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <?php $this->layout->load_view('guest/guest_invoice_items'); ?>
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
