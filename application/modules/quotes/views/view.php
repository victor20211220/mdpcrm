<style type="text/css">
    .btn-sm{
        background-color: #b2b2b2 !important;
        color: #FFF !important;
        border:none !important;
    }
    #btn_save_quote{
        background-color: #81c555 !important;
    }
    .btn-primary:hover {
    color: #26a9df !important;
    background: #fff !important;
    border: 1px solid #26a9df !important;
}
</style>
<style>
.input-group-addon {
    width: 1%;
    white-space: nowrap;
    vertical-align: middle;
    border-left: none !important;
}
.special-border {
  border-top-right-radius: 5px !important;
  border-bottom-right-radius: 5px !important;
}
</style>

<script src="/assets/responsive/js/lrtrim.js"></script>
<script src="/assets/responsive/js/invoiceModule.js"></script>

<script type="text/javascript">

    var item_tax_rates_preloaded = new Array();
    item_tax_rates_preloaded[0] = "0";
    <?php foreach ($tax_rates as $tax_rate) : ?>
    item_tax_rates_preloaded[<?= $tax_rate->tax_rate_id; ?>] = "<?= $tax_rate->tax_rate_percent;?>";
    <?php endforeach; ?>

    var currencySymbolPlacement = "<?= $this->Mdl_settings->setting('currency_symbol_placement'); ?>";

    function hide_pdf_button() {
        $(".btn_view_pdf,#btn_generate_pdf").hide();
    }

    function set_trigger_discount_item() {
        $('input[name=item_discount_percent]').change(function () {
            calculate_amounts();
            hide_pdf_button();
        });
    }

    $(function () {
        $('.btn_add_product').click(function (e) {
        	e.preventDefault();
            $('#modal-placeholder').load("<?= site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $(document).on('click', '.add-description', function (e) {
            $(this).next().toggleClass("hidden");
        });

        $('#quote_change_client').click(function () {
            $('#modal-placeholder').load("<?= site_url('quotes/ajax/modal_change_client'); ?>", {
                quote_id: <?= $quote_id; ?>,
                client_name: "<?= $this->db->escape_str($quote->client_name); ?>"
            });
        });

        <?php if (!$items) { ?>
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item_list').show();
        <?php } ?>

        $('#btn_generate_pdf').click(function () {
            window.open('<?= site_url('quotes/generate_pdf/' . $quote_id); ?>', '_blank');
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });

        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });

        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            helper: fixHelper,
            items: 'tbody'
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }

            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });

        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });

        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        set_trigger_discount_item();
        reload_amounts_on_change();
    });
</script>

<?= $modal_delete_quote; ?>
<?= $modal_add_quote_tax; ?>


<div class="bg-light lter b-b wrapper-md"  style="margin-bottom: -20px !important">
 <div class='row'>
   <div style="margin-bottom:5px !important" class="col-sm-4 col-xs-12 custom-auto-width-submenu-slv">
  <h1 class="m-n font-thin h3"><span class='gray-custom-2'><strong><?= lang('quote'); ?></strong></span> <span class='gray-custom-1'>#<?= $quote -> quote_number; ?></span></h1>
  </div>
  <div style="margin-bottom:5px !important" class="col-sm-2 col-xs-6">
   <a class="btn btn-sm btn-success padder-h" href="<?= site_url('quotes/status/all'); ?>">
              <i class="fa fa-arrow-left"></i> <?= lang('back'); ?>
   </a>
  </div>

  <div class="col-sm-6 text-right pull-right">


      <ul class="nav nav-pills nav-sm">
            <li>
                <a class="btn btn-gray dropdown-toggle padder-h" data-toggle="dropdown" href="#">
                    <i class="fa fa-cog "></i> <?= lang('options'); ?>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#add-quote-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i>
                            <?= lang('add_quote_tax'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="btn_generate_pdf"
                           data-quote-id="<?= $quote_id; ?>">
                            <i class="fa fa-file-pdf-o fa-margin"></i>
                            <?= lang('download_pdf'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= site_url('mailer/quote/'.$quote->quote_id); ?>">
                            <i class="fa fa-send fa-margin"></i>
                            <?= lang('send_email'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="btn_quote_to_invoice"
                           data-quote-id="<?= $quote_id; ?>">
                            <i class="fa fa-refresh fa-margin"></i>
                            <?= lang('quote_to_invoice'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="btn_copy_quote"
                           data-quote-id="<?= $quote_id; ?>">
                            <i class="fa fa-copy fa-margin"></i>
                            <?= lang('copy_quote'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#delete-quote" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="btn btn-success create-quote padder-h" href="#">
                    <i class="fa fa-plus"></i> <?= lang('new'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="btn btn-success padder-h ajax-loader" id="btn_save_quotez_2">
                    <i class="fa fa-floppy-o"></i>
                    <?= lang('save'); ?>
                </a>
            </li>
      </ul>
      </div></div>
</div>


<div class="wrapper-md">
 <div class="panel panel-default">

<div class="panel-body">
<?= $this->layout->load_view('layout/alerts'); ?>

    <form id="quote_form">

        <div class="quote form-group">

            <div class="cf row">

                <div class="col-xs-12 col-md-5">
                    <div class="pull-left">

                        <h2 style="margin-top: -5px;">
                            <a class="blueheader"  href="<?= site_url('clients/view/' . $quote->client_id); ?>"><?= $quote->client_name; ?></a>
                            <?php if ($quote->quote_status_id == 1) : ?>
                                <span id="quote_change_client" class="fa fa-pencil cursor-pointer small"
                                      data-toggle="tooltip" data-placement="bottom"
                                      title="<?= lang('change_client'); ?>"></span>
                            <?php endif; ?>
                        </h2><br>
                        <span>
                            <?= ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                            <?= ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                            <?= ($quote->client_city) ? $quote->client_city : ''; ?>
                            <?= ($quote->client_state) ? $quote->client_state : ''; ?>
                            <?= ($quote->client_zip) ? $quote->client_zip : ''; ?>
                            <?= ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
                        </span>
                        <br><br>
                        <?php if ($quote->client_phone) : ?>
                            <span>
                                <strong><?= lang('phone'); ?> :</strong>
                                <?= $quote->client_phone; ?>
                            </span>
                            <br>
                        <?php endif; ?>
                        <?php if ($quote->client_email) : ?>
                            <span>
                                <strong><?= lang('email'); ?> :</strong>
                                <?= $quote->client_email; ?>
                            </span>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-5 pull-right">

                    <div class="details-box">

                        <div class="row">

                            <div class="col-xs-12 col-sm-6">

                                <div class="quote-properties">
                                    <label for="quote_number">
                                        <?= lang('quote'); ?> #
                                    </label>

                                    <div class="controls">
                                        <input type="text" id="quote_number" class="form-control" value="<?= $quote->quote_number; ?>">
                                    </div>
                                </div>

                                </div>
                             <div class="col-xs-12 col-sm-6">
                                  <div class="quote-properties">
                                    <label for="quote_status_id">
                                        <?= lang('status'); ?>
                                    </label>
                                    <select name="quote_status_id" id="quote_status_id"
                                            class="form-control">
                                        <?php foreach ($quote_statuses as $key => $status) { ?>
                                            <option value="<?= $key; ?>"
                                                    <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                <?= $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

	                        </div>

	                        <br>

	                        <div class="row">

                                <div class="col-sm-6">
                                    <div class="quote-properties has-feedback">
                                        <label for="quote_date_created">
                                            <?= lang('date'); ?>
                                        </label>

                                        <div class="input-group">
                                            <input name="quote_date_created" id="quote_date_created"
                                                   class="form-control datepicker"
                                                   value="<?= date_from_mysql($quote->quote_date_created); ?>">
                                            <label for="quote_date_created" class="input-group-btn">
                                                <span class="btn btn-default"><i
                                                            class="fa fa-calendar fa-fw"></i></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                <div class="quote-properties has-feedback">
                                    <label for="quote_date_expires">
                                        <?= lang('expires'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="quote_date_expires" id="quote_date_expires" class="form-control datepicker" value="<?= date_from_mysql($quote->quote_date_expires); ?>">
	                                    <label for="quote_date_expires" class="input-group-btn">
		                                    <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
	                                    </label>
                                    </div>
                                </div>

                                </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php $this->layout->load_view('quotes/partial_item_table'); ?>

        <hr/>

        <div class="row">
            <div class="col-xs-12 col-sm-4">

                <div class="form-group">
                    <label class="control-label"><?= lang('notes'); ?></label>
                    <textarea name="notes" id="notes" rows="3" class="input-sm form-control">
                        <?= $quote->notes; ?>
                    </textarea>
                </div>

            </div>
            <div class="col-xs-12 col-sm-4">

                <div class="form-group">
                    <label class="control-label"><?= lang('attachments'); ?></label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-gray fileinput-button padder-h">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('add_files'); ?></span>
                    </span>
                </div>
                <!-- dropzone -->
                <div id="actions" >
                    <div class='MrgTop10'>
                        <!-- The global file processing state -->
                    <span class="fileupload-process">
                        <div id="total-progress" class="progress progress-striped active" role="progressbar"
                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="opacity: 0;">
                            <div class="progress-bar progress-bar-success" style="width:0%;"
                                 data-dz-uploadprogress></div>
                        </div>
                    </span>
                    </div>

                    <div class="table table-striped" class="files" id="previews">

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

                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                                     aria-valuemax="100" aria-valuenow="0" style="opacity: 0;display:none">
                                    <div class="progress-bar progress-bar-success" style="..."
                                         data-dz-uploadprogress></div>
                                </div>
                            </div>
                            <div>
                                <button data-dz-remove class="btn btn-default delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?= lang('delete'); ?></span>
                                </button>
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
            <label class="control-label">
                <?= $custom_field->custom_field_label; ?>
            </label>
            <input type="text" class="form-control"
                   name="custom[<?= $custom_field->custom_field_column; ?>]"
                   id="<?= $custom_field->custom_field_column; ?>"
                   value="<?= html_escape($this->Mdl_quotes->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
        <?php } ?>

        <?php if ($quote->quote_status_id != 1) { ?>
            <p class="padded">
                <?= lang('guest_url'); ?>:
                <?= auto_link(site_url('guest/view/quote/' . $quote->quote_url_key)); ?>
            </p>
        <?php } ?>
  </div>
 </div>
</div>
 <script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: "<?= site_url('upload/upload_file/' . $quote->client_id. '/'.$quote->quote_url_key) ?>", // Set the url
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
            $.getJSON("<?= site_url('upload/upload_file/' . $quote->client_id. '/'.$quote->quote_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '/uploads/customer_files/' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                            '/assets/default/img/favicon.png');
                    }
                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '<?= base_url(); ?>assets/default/img/favicon.png');
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
            url: "<?= site_url('upload/delete_file/'.$quote->quote_url_key) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });
</script>
