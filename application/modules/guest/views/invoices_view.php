<script type="text/javascript">

    	$(function () {
        $('#btn_generate_pdf').click(function () {
            window.location = '<?= site_url('invoices/generate_pdf/' . $invoice_id); ?>
			';
			});
			});

</script>

<div id="headerbar">
    <h1><?= lang('invoice'); ?> #<?= $invoice->invoice_number; ?></h1>

    <div class="pull-right">
        <?php if ($invoice->invoice_status_id == 4) { ?>
            <span class="btn btn-success btn-sm disabled">
                <i class="fa fa-check"></i>
                <?= lang('paid')?>
            </span>
        <?php } ?>
        <a href="<?= site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
           class="btn btn-default btn-sm" id="btn_generate_pdf"
           data-invoice-id="<?= $invoice_id; ?>"
           data-invoice-balance="<?= $invoice->invoice_balance; ?>">
            <i class="fa fa-file-pdf-o"></i> <?= lang('download_pdf'); ?>
        </a>
    </div>

</div>

<?= $this->layout->load_view('layout/alerts'); ?>

<div id="content">

    <form id="invoice_form" class="form-horizontal">

        <div class="invoice">

            <div class="row">

                <div class="col-xs-12 col-md-9">
                    <div class="pull-left">

                        <h3><?= $invoice->client_name; ?></h3>

					<span>
						<?=($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                        <?=($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                        <?=($invoice->client_city) ? $invoice->client_city : ''; ?>
                        <?=($invoice->client_state) ? $invoice->client_state : ''; ?>
                        <?=($invoice->client_zip) ? $invoice->client_zip : ''; ?>
                        <?=($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
					</span>
                        <br><br>

                        <?php if ($invoice->client_phone) { ?>
                            <span>
                            <strong><?= lang('phone'); ?>:</strong>
                                <?= $invoice->client_phone; ?>
                        </span><br>
                        <?php } ?>

                        <?php if ($invoice->client_email) { ?>
                            <span>
                            <strong><?= lang('email'); ?>:</strong>
                                <?= $invoice->client_email; ?>
                        </span>
                        <?php } ?>

                    </div>
                </div>

                <div class="col-xs-12 col-md-3">
                    <div class="panel panel-default panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><?= lang('invoice'); ?> #</td>
                                <td><?= $invoice->invoice_number; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('date'); ?></td>
                                <td><?= date_from_mysql($invoice->invoice_date_created); ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('due_date'); ?></td>
                                <td><?= date_from_mysql($invoice->invoice_date_due); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <br/>
            <div class="table-responsive">
                <table id="item_table" class="items table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th><?= lang('item'); ?> / <?= lang('description'); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <?php
                    $i = 1;
                    foreach ($items as $item) { ?>
                        <tbody class="item">
                        <tr>
                            <td rowspan="2" style="max-width: 20px;" class="text-center">
                                <?= $i;
                                $i++;
 ?>
                            </td>
                            <td><?= $item->item_name; ?></td>
                            <td>
                                <span class="pull-left"><?= lang('quantity'); ?></span>
                                <span class="pull-right amount"><?= $item->item_quantity; ?></span>
                            </td>
                            <td>
                                <span class="pull-left"><?= lang('item_discount'); ?></span>
                                <span class="pull-right amount">
                                    <?= format_currency($item->item_discount); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?= lang('subtotal'); ?></span>
                                <span class="pull-right amount">
                                    <?= format_currency($item->item_subtotal); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><?= nl2br($item->item_description); ?></td>
                            <td>
                                <span class="pull-left"><?= lang('price'); ?></span>
                                <span class="pull-right amount">
                                    <?= format_currency($item->item_price); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?= lang('tax'); ?></span>
                                <span class="pull-right amount">
                                    <?= format_currency($item->item_tax_total); ?>
                                </span>
                            </td>
                            <td>
                                <span class="pull-left"><?= lang('total'); ?></span>
                                <span class="pull-right amount">
                                    <?= format_currency($item->item_total); ?>
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    <?php } ?>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="text-right"><?= lang('subtotal'); ?></th>
                        <th class="text-right"><?= lang('item_tax'); ?></th>
                        <th class="text-right"><?= lang('invoice_tax'); ?></th>
                        <th class="text-right"><?= lang('discount'); ?></th>
                        <th class="text-right"><?= lang('total'); ?></th>
                        <th class="text-right"><?= lang('paid'); ?></th>
                        <th class="text-right"><?= lang('balance'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="amount"><?= format_currency($invoice->invoice_item_subtotal); ?></td>
                        <td class="amount"><?= format_currency($invoice->invoice_item_tax_total); ?></td>
                        <td class="amount">
                            <?php if ($invoice_tax_rates) {
                                foreach ($invoice_tax_rates as $invoice_tax_rate) { ?>
                                    <?= $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent; ?>%:
                                    <?= format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?><br>
                                <?php }
                                        } else {
                                        echo format_currency('0');
                                        }
 ?>
                        </td>
                        <td class="amount"><?php
                        if ($invoice->invoice_discount_amount == floatval(0))
                        {
                            echo $invoice->invoice_discount_percent . '%';
                        }
                        else
                        {
                            echo format_currency($invoice->invoice_discount_amount);
                        }
                            ?>
                        </td>
                        <td class="amount"><?= format_currency($invoice->invoice_total); ?></td>
                        <td class="amount"><?= format_currency($invoice->invoice_paid); ?></td>
                        <td class="amount"><?= format_currency($invoice->invoice_balance); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($invoice->invoice_terms): ?>
            <p>
                <strong><?= lang('invoice_terms'); ?></strong><br/>
                <?= nl2br($invoice->invoice_terms); ?>
            </p>
            <?php endif; ?>

        </div>

    </form>

</div>
