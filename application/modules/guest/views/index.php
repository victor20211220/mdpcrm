<div id="headerbar">
    <h1><?= lang('dashboard'); ?></h1>
</div>

<div id="content">

    <?php if ($overdue_invoices) { ?>
        <div class="panel panel-default">

            <div class="panel-heading">
                <h3 class="panel-title"><?= lang('overdue_invoices'); ?></h3>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped no-margin">

                        <thead>
                        <tr>
                            <th><?= lang('invoice'); ?></th>
                            <th><?= lang('created'); ?></th>
                            <th><?= lang('due_date'); ?></th>
                            <th><?= lang('client_name'); ?></th>
                            <th><?= lang('amount'); ?></th>
                            <th><?= lang('balance'); ?></th>
                            <th><?= lang('options'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($overdue_invoices as $invoice) { ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"><?= $invoice->invoice_number; ?></a>
                                </td>
                                <td>
                                    <?= date_from_mysql($invoice->invoice_date_created); ?>
                                </td>
                                <td>
                            <span class="font-overdue">
                                <?= date_from_mysql($invoice->invoice_date_due); ?>
                            </span>
                                </td>
                                <td>
                                    <?= $invoice->client_name; ?>
                                </td>
                                <td>
                                    <?= format_currency($invoice->invoice_total); ?>
                                </td>
                                <td>
                                    <?= format_currency($invoice->invoice_balance); ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"
                                       class="btn btn-default btn-sm">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                        <?= lang('view'); ?>
                                    </a>

                                    <a href="<?= site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                       class="btn btn-default btn-sm">
                                        <i class="icon ion-printer"></i>
                                        <?= lang('pdf'); ?>
                                    </a>

                                    <?php if ($this->Mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?>
                                        <a href="<?= site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>"
                                           class="btn btn-sm btn-success">
                                            <i class="glyphicon glyphicon-ok"></i>
                                            <?= lang('pay_now'); ?>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?= lang('quotes_requiring_approval'); ?></h3>
        </div>

        <div class="panel-body">

            <?php if ($open_quotes) { ?>
                <div class="table-responsive">
                    <table class="table table-striped no-margin">

                        <thead>
                        <tr>
                            <th><?= lang('quote'); ?></th>
                            <th><?= lang('created'); ?></th>
                            <th><?= lang('due_date'); ?></th>
                            <th><?= lang('client_name'); ?></th>
                            <th><?= lang('amount'); ?></th>
                            <th><?= lang('options'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($open_quotes as $quote) { ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                                       title="<?= lang('edit'); ?>">
                                        <?= $quote->quote_number; ?>
                                    </a>
                                </td>
                                <td>
                                    <?= date_from_mysql($quote->quote_date_created); ?>
                                </td>
                                <td>
                                    <?= date_from_mysql($quote->quote_date_expires); ?>
                                </td>
                                <td>
                                    <?= $quote->client_name; ?>
                                </td>
                                <td>
                                    <?= format_currency($quote->quote_total); ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                                       class="btn btn-default btn-sm">
                                        <i class="glyphicon glyphicon-search"></i>
                                        <?= lang('view'); ?>
                                    </a>
                                    <a href="<?= site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>"
                                       class="btn btn-default btn-sm">
                                        <i class="icon ion-printer"></i>
                                        <?= lang('pdf'); ?>
                                    </a>
                                    <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
                                        <a href="<?= site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
                                           class="btn btn-success btn-sm">
                                            <i class="glyphicon glyphicon-check"></i>
                                            <?= lang('approve'); ?>
                                        </a>
                                        <a href="<?= site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
                                           class="btn btn-default btn-sm">
                                            <i class="glyphicon glyphicon-ban-circle"></i>
                                            <?= lang('reject'); ?>
                                        </a>
                                    <?php } elseif ($quote->quote_status_id == 4) { ?>
                                        <a href="#" class="btn btn-success btn-sm"><?= lang('approved'); ?></a>
                                    <?php } elseif ($quote->quote_status_id == 5) { ?>
                                        <a href="#" class="btn btn-danger btn-sm"><?= lang('rejected'); ?></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            <?php } else { ?>
                <span class="text-success"><?= lang('no_quotes_requiring_approval'); ?></span>
            <?php } ?>
        </div>
    </div>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?= lang('open_invoices'); ?></h3>
        </div>

        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped no-margin">

                    <thead>
                    <tr>
                        <th><?= lang('invoice'); ?></th>
                        <th><?= lang('created'); ?></th>
                        <th><?= lang('due_date'); ?></th>
                        <th><?= lang('client_name'); ?></th>
                        <th><?= lang('amount'); ?></th>
                        <th><?= lang('balance'); ?></th>
                        <th><?= lang('options'); ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($open_invoices as $invoice) { ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('guest/invoices/view/' . $invoice->invoice_id); ?>">
                                    <?= $invoice->invoice_number; ?>
                                </a>
                            </td>
                            <td>
                                <?= date_from_mysql($invoice->invoice_date_created); ?>
                            </td>
                            <td>
                            <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                <?= date_from_mysql($invoice->invoice_date_due); ?>
                            </span>
                            </td>
                            <td>
                                <?= $invoice->client_name; ?>
                            </td>
                            <td>
                                <?= format_currency($invoice->invoice_total); ?>
                            </td>
                            <td>
                                <?= format_currency($invoice->invoice_balance); ?>
                            </td>
                            <td>
                                <a href="<?= site_url('guest/invoices/view/' . $invoice->invoice_id); ?>"
                                   class="btn btn-default btn-sm">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    <?= lang('view'); ?>
                                </a>

                                <a href="<?= site_url('guest/invoices/generate_pdf/' . $invoice->invoice_id); ?>"
                                   class="btn btn-default btn-sm">
                                    <i class="icon ion-printer"></i>
                                    <?= lang('pdf'); ?>
                                </a>

                                <?php if ($this->Mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?>
                                    <a href="<?= site_url('guest/payment_handler/make_payment/' . $invoice->invoice_url_key); ?>"
                                       class="btn btn-success btn-sm">
                                        <i class="glyphicon glyphicon-ok"></i>
                                        <?= lang('pay_now'); ?>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>
