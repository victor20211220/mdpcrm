<div id="headerbar">
    <h1><?= lang('quote'); ?> #<?= $quote->quote_number; ?></h1>

    <div class="pull-right btn-group">

        <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
            <a href="<?= site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
               class="btn btn-success btn-sm">
                <i class="fa fa-check"></i>
                <?= lang('approve_this_quote'); ?>
            </a>
            <a href="<?= site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
               class="btn btn-default btn-sm">
                <i class="fa fa-times-circle"></i>
                <?= lang('reject_this_quote'); ?>
            </a>
        <?php } elseif ($quote->quote_status_id == 4) { ?>
            <a href="#" class="btn btn-success btn-sm disabled">
                <i class="fa fa-check"></i>
                <?= lang('quote_approved'); ?>
            </a>
        <?php } elseif ($quote->quote_status_id == 5) { ?>
            <a href="#" class="btn btn-default btn-sm disabled">
                <i class="fa fa-times-circle"></i>
                <?= lang('quote_rejected'); ?>
            </a>
        <?php } ?>

        <a href="<?= site_url('guest/quotes/generate_pdf/' . $quote_id); ?>"
           class="btn btn-default btn-sm" id="btn_generate_pdf">
            <i class="fa fa-file-pdf-o"></i> <?= lang('download_pdf'); ?>
        </a>
    </div>

</div>

<?= $this->layout->load_view('layout/alerts'); ?>

<div id="content">

    <div class="quote">

        <div class="row">

            <div class="col-xs-12 col-md-9">

                <h2><?= $quote->client_name; ?></h2><br>
                <span>
                    <?=($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                    <?=($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                    <?=($quote->client_city) ? $quote->client_city : ''; ?>
                    <?=($quote->client_state) ? $quote->client_state : ''; ?>
                    <?=($quote->client_zip) ? $quote->client_zip : ''; ?>
                    <?=($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
                </span>
                <br><br>
                <?php if ($quote->client_phone) { ?>
                    <span><strong><?= lang('phone'); ?>:</strong> <?= $quote->client_phone; ?></span><br>
                <?php } ?>
                <?php if ($quote->client_email) { ?>
                    <span><strong><?= lang('email'); ?>:</strong> <?= $quote->client_email; ?></span>
                <?php } ?>

            </div>

            <div class="col-xs-12 col-md-3">
                <div class="panel panel-default panel-body text-right">
                    <table class="table table-condensed">
                        <tr>
                            <td><?= lang('quote'); ?> #</td>
                            <td><?= $quote->quote_number; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('date'); ?></td>
                            <td><?= date_from_mysql($quote->quote_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('due_date'); ?></td>
                            <td><?= date_from_mysql($quote->quote_date_expires); ?></td>
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
                    <tbody>
                    <tr>
                        <td rowspan="2" style="max-width: 20px;" class="text-center">
                            <?= $i; $i++; ?>
                        </td>
                        <td><?= $item->item_name; ?></td>
                        <td>
                            <span class="pull-left"><?= lang('quantity'); ?></span>
                            <span class="pull-right amount"><?= $item->item_quantity; ?></span>
                        </td>
                        <td>
                            <span class="pull-left"><?= lang('discount'); ?></span>
                            <span class="pull-right amount"><?= format_currency($item->item_discount); ?></span>
                        </td>
                        <td>
                            <span class="pull-left"><?= lang('subtotal'); ?></span>
                            <span class="pull-right amount"><?= format_currency($item->item_subtotal); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><?= $item->item_description; ?></td>
                        <td>
                            <span class="pull-left"><?= lang('price'); ?></span>
                            <span class="pull-right amount"><?php format_amount($item->item_price); ?></span>
                        </td>
                        <td>
                            <span class="pull-left"><?= lang('tax'); ?></span>
                            <span class="pull-right amount"><?= format_amount($item->item_tax_total); ?></span>
                        </td>
                        <td>
                            <span class="pull-left"><?= lang('total'); ?></span>
                            <span class="pull-right amount"><?= format_currency($item->item_total); ?></span>
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
                    <th class="text-right"><?= lang('quote_tax'); ?></th>
                    <th class="text-right"><?= lang('discount'); ?></th>
                    <th class="text-right"><?= lang('total'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="amount"><?= format_currency($quote->quote_item_subtotal); ?></td>
                    <td class="amount"><?= format_currency($quote->quote_item_tax_total); ?></td>
                    <td class="amount">
                        <?php if ($quote_tax_rates) {
                            foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                                <?= $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>%:
                                <?= format_currency($quote_tax_rate->quote_tax_rate_amount); ?><br/>
                            <?php }
                                    } else {
                                    echo format_currency('0');
                                    }
 ?>
                    </td>
                    <td class="amount"><?php
                    if ($quote->quote_discount_percent == floatval(0))
                    {
                        echo $quote->quote_discount_percent . '%';
                    }
                    else
                    {
                        echo format_currency($quote->quote_discount_amount);
                    }
                        ?>
                    </td>
                    <td class="amount"><b><?= format_currency($quote->quote_total); ?></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
