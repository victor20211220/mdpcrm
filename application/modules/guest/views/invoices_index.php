<div id="headerbar">
    <h1><?= lang('invoices'); ?></h1>

    <div class="pull-right">
        <?= pager(site_url('guest/invoices/status/' . $this->uri->segment(4)), 'Mdl_invoices'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'open') { ?>class="active"<?php } ?>>
                <a href="<?= site_url('guest/invoices/status/open'); ?>">
                    <?= lang('open'); ?>
                </a>
            </li>
            <li <?php if ($status == 'paid') { ?>class="active"<?php } ?>>
                <a href="<?= site_url('guest/invoices/status/paid'); ?>">
                    <?= lang('paid'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

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
                <?php foreach ($invoices as $invoice) : ?>
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
                            <?= date_from_mysql($invoice->invoice_date_due); ?>
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
                                </a><?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
