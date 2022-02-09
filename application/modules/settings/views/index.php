<link rel="stylesheet" href="/assets/responsive/css/image-picker.css" type="text/css" />
<script src="/assets/responsive/js/image-picker.min.js"></script>
<script type="text/javascript">
    $().ready(function () {
        $('#btn-submit').click(function () {
            $('#form-settings').submit();
        });

        $("[name='settings[default_country]']").select2({allowClear: true});
        $("[name='settings[pdf_invoice_template]']").imagepicker({'show_label': false});
        $("[name='settings[pdf_invoice_template_paid]']").imagepicker({'show_label': true});
        $("[name='settings[pdf_invoice_template_overdue]']").imagepicker({'show_label': true});
        $("[name='settings[pdf_quote_template]']").imagepicker({'show_label': true});
    });
</script>

<div class=" lter wrapper-md">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class=" font-thin h3"><i class="fa fa-cog"></i> <?= lang('settings');?></h1>
        </div>
        <div class="col-sm-6 text-right pull-right row">
            <ul class="nav nav-pills nav-sm custom-right-submenu-slv">
                <div class="but-wrapper">
                    <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1"> Cancel</button>
                    <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success" value="1"> Save</button>
                </div>
            </ul>
        </div>
    </div>
</div>

<form method="post" id="form-settings" enctype="multipart/form-data" >
    <div class="">  <?= $this->layout->load_view('layout/alerts'); ?>
        <div class="tab-container">
            <ul class="nav nav-tabs" role="tablist">
              <li class="active">
                  <a href="#company_information" data-toggle="tab"><?= lang('company_information'); ?>
                      <span class="badge bg-primary badge-sm m-l-xs"></span>
                  </a>
              </li>
                <li>
                    <a href="#general" data-toggle="tab"><?= lang('general'); ?>
                        <span class="badge badge-sm m-l-xs"></span>
                    </a>
                </li>
                <li>
                    <a href="#invoices" data-toggle="tab"><?= lang('invoices'); ?>
                        <span class="badge bg-danger badge-sm m-l-xs"></span>
                    </a>
                </li>
                <li>
                    <a href="#quotes" data-toggle="tab"><?= lang('quotes'); ?>
                        <span class="badge bg-primary badge-sm m-l-xs"></span>
                    </a>
                </li>
                <li>
                    <a href="#taxes" data-toggle="tab"><?= lang('taxes'); ?>
                        <span class="badge bg-primary badge-sm m-l-xs"></span>
                    </a>
                </li>
                <li>
                    <a href="#email" data-toggle="tab"><?= lang('email'); ?>
                        <span class="badge bg-primary badge-sm m-l-xs"></span>
                    </a>
                </li>
                <li>
                    <a href="#merchant_account" data-toggle="tab"><?= lang('merchant_account'); ?>
                        <span class="badge bg-primary badge-sm m-l-xs"></span>
                    </a>
                </li>


            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="general">
                    <?php $this->layout->load_view('settings/partial_settings_general'); ?>
                </div>
                <div class="tab-pane" id="invoices">
                    <?php $this->layout->load_view('settings/partial_settings_invoices'); ?>
                </div>
                <div class="tab-pane" id="quotes">
                    <?php $this->layout->load_view('settings/partial_settings_quotes'); ?>
                </div>
                <div class="tab-pane" id="taxes">
                	<?php $this->layout->load_view('settings/partial_settings_taxes'); ?>
                </div>
                <div class="tab-pane" id="email">
                    <?php $this->layout->load_view('settings/partial_settings_email'); ?>
                </div>
                <div class="tab-pane" id="merchant_account">
                	<?php $this->layout->load_view('settings/partial_settings_merchant'); ?>
                </div>
                <div class="tab-pane active" id="company_information">
                	<?php $this->layout->load_view('settings/partial_settings_company_information'); ?>
                </div>
            </div>
        </div>
    </div>
</form>
