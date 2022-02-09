<script type="text/javascript">
    $(function () {
        $('#save_supplier_note').click(function () {
            $.post("<?= site_url('suppliers/ajax/save_supplier_note'); ?>",
                {
                    supplier_id: $('#supplier_id').val(),
                    supplier_note: $('#supplier_note').val()
                }, function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful
                        $('.control-group').removeClass('error');
                        $('#supplier_note').val('');

                        $('#notes_list').load("<?= site_url('suppliers/ajax/load_supplier_notes'); ?>",
                            {
                                supplier_id: <?= $supplier->supplier_id; ?>
                            });
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('error');
                        }
                    }
                });
        });

    });
</script>

<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">
      <i class="fa fa-book"></i> <?= $supplier->supplier_name; ?>
      <small style="padding-left: 15px">
          <?= $user->user_name; ?>
      </small>
  </h1>
</div>
<div class="wrapper-md">
  <div class="tab-container">

      <ul class="nav nav-tabs" role="tablist">
          <li class="active">
              <a href="#details" data-toggle="tab"><?= lang('details'); ?>
                  <span class="badge badge-sm m-l-xs"></span>
              </a>
          </li>
          <li>
              <a href="#expenses" data-toggle="tab"><?= lang('expenses'); ?>
                  <span class="badge bg-primary badge-sm m-l-xs"></span>
              </a>
          </li>
          <li>
              <a href="#notes" data-toggle="tab"><?= lang('notes'); ?>
                  <span class="badge bg-primary badge-sm m-l-xs"></span>
              </a>
          </li>
       </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="details">
            <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8" style="padding-bottom: 25px">
                    <h3><?= $supplier->supplier_name; ?></h3>

                    <p>
                        <?= ($supplier->supplier_reg_number) ? $supplier->supplier_reg_number . '<br>' : ''; ?>
                        <?= ($supplier->supplier_address_1) ? $supplier->supplier_address_1 . '<br>' : ''; ?>
                        <?= ($supplier->supplier_address_2) ? $supplier->supplier_address_2 . '<br>' : ''; ?>
                        <?= ($supplier->supplier_city) ? $supplier->supplier_city : ''; ?>
                        <?= ($supplier->supplier_state) ? $supplier->supplier_state : ''; ?>
                        <?= ($supplier->supplier_zip) ? $supplier->supplier_zip : ''; ?>
                        <?= ($supplier->supplier_country) ? '<br>' . $supplier->supplier_country : ''; ?>
                    </p>

                    <p style="padding-top: 20px">
                        Created: <?= $supplier->supplier_date_created; ?>
                    </p>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                    <table class="table table-condensed table-bordered">
                        <tr>
                            <td>
                                <b><?= lang('total_billed'); ?></b>
                            </td>
                            <td class="td-amount">
                                <?= format_currency($supplier->supplier_invoice_total); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b><?= lang('total_paid'); ?></b>
                            </td>
                            <td class="td-amount">
                                <?= format_currency($supplier->supplier_invoice_paid); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b><?= lang('total_balance'); ?></b>
                            </td>
                            <td class="td-amount">
                                <?= format_currency($supplier->supplier_invoice_balance); ?>
                            </td>
                        </tr>
                    </table>
                </div>

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h4><?= lang('contact_information'); ?></h4>
                    <br/>
                    <table class="table table-condensed table-striped">
                        <?php if ($supplier->supplier_email) { ?>
                            <tr>
                                <td><?= lang('email'); ?></td>
                                <td><?= auto_link($supplier->supplier_email, 'email'); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($supplier->supplier_phone) { ?>
                            <tr>
                                <td><?= lang('phone'); ?></td>
                                <td><?= $supplier->supplier_phone; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($supplier->supplier_mobile) { ?>
                            <tr>
                                <td><?= lang('mobile'); ?></td>
                                <td><?= $supplier->supplier_mobile; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($supplier->supplier_fax) { ?>
                            <tr>
                                <td><?= lang('fax'); ?></td>
                                <td><?= $supplier->supplier_fax; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($supplier->supplier_web) { ?>
                            <tr>
                                <td><?= lang('web'); ?></td>
                                <td><?= auto_link($supplier->supplier_web, 'url', TRUE); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="col-xs-12 col-md-6">
                    <h4><?= lang('tax_information'); ?></h4>
                    <br/>
                    <table class="table table-condensed table-striped">
                        <?php if ($supplier->supplier_vat_id) { ?>
                            <tr>
                                <td><?= lang('vat_id'); ?></td>
                                <td><?= $supplier->supplier_vat_id; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($supplier->supplier_tax_code) { ?>
                            <tr>
                                <td><?= lang('tax_code'); ?></td>
                                <td><?= $supplier->supplier_tax_code; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h4><?= lang('financial_details'); ?></h4>
                    <br/>
                    <table class="table table-condensed table-striped">
                        <tr>
                                <td><?= lang('swift_code'); ?></td>
                                <td><?= $supplier->supplier_swift; ?></td>
                        </tr>
                        <tr>
                                <td><?= lang('iban_code'); ?></td>
                                <td><?= $supplier->supplier_iban; ?></td>
                            </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h4><?= lang('custom_fields'); ?></h4>
                    <br/>
                    <table class="table table-condensed table-striped">
                        <?php foreach ($custom_fields as $custom_field) { ?>
                            <tr>
                                <td><?= $custom_field->custom_field_label ?></td>
                                <td><?= $custom_field->value_data; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <hr/>
        </div>
        <div class="tab-pane" id="expenses">
          <?= $invoice_table; ?>
            <br clear="all">
        </div>
        <div class="tab-pane" id="notes">
                <h4><?= lang('notes'); ?></h4>
                <br/>

                <div id="notes_list">
                    <?= $partial_notes; ?>
                </div>
                <div class="panel panel-default panel-body">
                    <form class="row">
                        <div class="col-xs-12 col-md-10">
                            <input type="hidden" name="supplier_id" id="supplier_id"
                                   value="<?= $supplier->supplier_id; ?>">
                            <textarea id="supplier_note" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-xs-12 col-md-2 text-center">
                            <input type="button" id="save_supplier_note" class="btn btn-default btn-block"
                                   value="<?= lang('add_notes'); ?>">
                        </div>
                    </form>
                </div>
        </div>
    </div>
  </div>
</div>
