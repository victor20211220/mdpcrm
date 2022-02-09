<script type="text/javascript">
    $(function () {
        $('#save_client_note').click(function () {
            $.post("/clients/ajax/save_client_note", {
                client_id: $('#client_id').val(),
                client_note: $('#client_note').val()
            }, function (data) {
                var response = JSON.parse(data);
                if (response.success == '1') {
                    $('.control-group').removeClass('error');
                    $('#client_note').val('');
                    $('#notes_list').load("/clients/ajax/load_client_notes", {
                        client_id:<?= $client->client_id; ?>
                    });
                } else {
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
    <h1 class="m-n font-thin h3 right">
        <i class="icon-icon-clients"></i> <?= $client->client_name; ?>
        <small style="padding-left: 15px">
            <?= $user->user_name; ?>
        </small>
    </h1>
</div>
<div>
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="active">
                <a href="#tab1" data-toggle="tab">
                    <?= lang('details'); ?>
                </a>
            </li>
            <li>
                <a href="#tab2" data-toggle="tab">
                    <?= lang('quotes'); ?>
                </a>
            </li>
            <li>
                <a href="#tab3" data-toggle="tab">
                    <?= lang('invoices'); ?>
                </a>
            </li>
            <li>
                <a href="#tab4" data-toggle="tab">
                    <?= lang('payments'); ?>
                </a>
            </li>
            <li>
                <a href="#tab6" data-toggle="tab">
                    <?= lang('tasks'); ?>
                </a>
            </li>
            <li>
                <a href="#tab5" data-toggle="tab">
                    <?= lang('notes'); ?>
                    <?php if ($client_notes): ?>
                    <b class="badge badge-sm bg-danger pull-right m-l-xs">
                        <?= count($client_notes); ?>
                    </b>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="#tab7" data-toggle="tab">
                    <?= lang('emails'); ?>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab1">

                <?php $this->layout->load_view('layout/alerts'); ?> <br>

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8" style="width: 35%">
                        <h3 class="no-top-margin"><?= $client->client_name; ?></h3>
                        <p>
                            <?= ($client->client_reg_number) ? $client->client_reg_number . '<br>' : ''; ?>
                            <?= ($client->client_address_1) ? $client->client_address_1 . '<br>' : ''; ?>
                            <?= ($client->client_address_2) ? $client->client_address_2 . '<br>' : ''; ?>
                            <?= ($client->client_city) ? $client->client_city . ', ' : ''; ?>
                            <?= ($client->client_state) ? $client->client_state . ', ' : ''; ?>
                            <?= ($client->client_zip) ? $client->client_zip . ', ' : ''; ?>
                            <?= ($client->client_country) ? '<br>' . $client->client_country : ''; ?>
                        </p>
                        <p style="padding-top: 60px">
                            Created: <?= $client->client_date_created; ?>
                        </p>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4" style="float: left; width: 30%;">
                        <table class="table table-condensed table-bordered">
                            <tr>
                                <td><b><?= lang('total_billed'); ?></b></td>
                                <td class="td-amount">
                                    <?= format_currency($client->client_invoice_total); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b><?= lang('total_paid'); ?></b></td>
                                <td class="td-amount">
                                    <?= format_currency($client->client_invoice_paid); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b><?= lang('total_balance'); ?></b></td>
                                <td class="td-amount">
                                    <?= format_currency($client->client_invoice_balance); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4" style="float: left; width: 35%; text-align: center">
                        <?php $address = $client->client_address_1 . ", " . $client->client_address_2; ?>
                        <img border="0" src="https://maps.googleapis.com/maps/api/staticmap?center=<?= $address; ?>&zoom=13&size=375x200&maptype=roadmap&markers=size:mid%7Ccolor:green%7C<?= $address; ?>\&key=AIzaSyCQbXcoxcqu_ZmEQtSZmaqC5pQ0nqSXOKA">
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <h4><?= lang('contact_information'); ?></h4><br/>
                        <table class="table table-condensed table-striped">
                            <?php if ($client->client_email) : ?>
                                <tr>
                                    <td><?= lang('email'); ?></td>
                                    <td><?= auto_link($client->client_email, 'email'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->client_phone) : ?>
                                <tr>
                                    <td><?= lang('phone'); ?></td>
                                    <td><?= $client->client_phone; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->client_mobile) : ?>
                                <tr>
                                    <td><?= lang('mobile'); ?></td>
                                    <td><?= $client->client_mobile; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->client_fax) : ?>
                                <tr>
                                    <td><?= lang('fax'); ?></td>
                                    <td><?= $client->client_fax; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->client_web) : ?>
                                <tr>
                                    <td><?= lang('web'); ?></td>
                                    <td><?= auto_link($client->client_web, 'url', true); ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <h4><?= lang('tax_information'); ?></h4><br/>
                        <table class="table table-condensed table-striped">
                            <?php if ($client->client_vat_id) : ?>
                                <tr>
                                    <td><?= lang('vat_id'); ?></td>
                                    <td><?= $client->client_vat_id; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->client_tax_code) : ?>
                                <tr>
                                    <td><?= lang('tax_code'); ?></td>
                                    <td><?= $client->client_tax_code; ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <h4><?= lang('financial_details'); ?></h4><br/>
                        <table class="table table-condensed table-striped">
                            <tr>
                                <td><?= lang('swift_code'); ?></td>
                                <td><?= $client->client_swift; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('iban_code'); ?></td>
                                <td><?= $client->client_iban; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6"><h4><?= lang('custom_fields'); ?></h4><br/>
                        <table class="table table-condensed table-striped">
                            <?php foreach ($custom_fields as $custom_field) : ?>
                                <tr>
                                    <td><?= $custom_field->custom_field_label ?></td>
                                    <td><?= $custom_field->value_data; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab2">
                <div class="container">
                    <?= $quote_table; ?>
                </div>
            </div>

            <div class="tab-pane" id="tab3">
                <div class="container">
                    <?= $invoice_table; ?>
                </div>
            </div>

            <div class="tab-pane" id="tab4">
                <div class="container">
                    <?= $payment_table; ?>
                </div>
            </div>

            <div class="tab-pane" id="tab5"><h4><?= lang('notes'); ?></h4><br>
                <div id="notes_list"><?= $partial_notes; ?></div>
                <form class="row">
                    <div class="col-xs-8 col-md-4">
                        <input type="hidden" name="client_id" id="client_id" value="<?= $client->client_id; ?>">
                        <textarea id="client_note" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="col-xs-4 col-md-2 text-center">
                        <input type="button" id="save_client_note" class="btn btn-sm btn-success" value="<?= lang('add_notes'); ?>">
                    </div>
                </form>
            </div>

            <div class="tab-pane" id="tab6">
                <br>
                <?= $tasks_table; ?>
            </div>

            <div class="tab-pane" id="tab7">
                <br>
                <div class="tab-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#tab11" data-toggle="tab">
                                <?= lang('inbox'); ?> <span class="badge badge-sm m-l-xs"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#tab21" data-toggle="tab">
                                <?= lang('outbox'); ?> <span class="badge bg-danger badge-sm m-l-xs"></span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab11">
                            <div class="container">
                                <table class="table table-inbox table-hover" id="mailsTable">
                                    <thead>
                                    <tr>
                                        <th class="view-message">SUBJECT</th>
                                        <th class="inbox-small-cells"><i class="fa fa-star"></i></th>
                                        <th class="view-message">DATE</th>
                                        <th class="view-message">SIZE</th>
                                        <th class="inbox-small-cells"><i class="fa fa-flag"></i></th>
                                        <th class="inbox-small-cells"><i class="fa fa-paperclip"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody id="mailBody">
                                    <?php foreach ($inbox as $i) { echo $i; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab21">
                            <div class="container">
                                <table class="table table-inbox table-hover" id="mailsTable">
                                    <thead>
                                    <tr>
                                        <th class="view-message">SUBJECT</th>
                                        <th class="inbox-small-cells"><i class="fa fa-star"></i></th>
                                        <th class="view-message">DATE</th>
                                        <th class="view-message">SIZE</th>
                                        <th class="inbox-small-cells"><i class="fa fa-flag"></i></th>
                                        <th class="inbox-small-cells"><i class="fa fa-paperclip"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody id="mailBody">
                                    <?php foreach ($outbox as $i) { echo $i; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
