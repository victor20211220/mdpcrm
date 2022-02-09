<div id="headerbar">
    <h1><?= lang('payments'); ?></h1>
    <div class="pull-right">
        <?= pager(site_url('guest/payments/index'), 'Mdl_payments'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="filter_results">
        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?= lang('date'); ?></th>
                    <th><?= lang('invoice'); ?></th>
                    <th><?= lang('amount'); ?></th>
                    <th><?= lang('payment_method'); ?></th>
                    <th><?= lang('note'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($payments as $payment) : ?>
                    <tr>
                        <td><?= date_from_mysql($payment->payment_date); ?></td>
                        <td><?= $payment->invoice_number; ?></td>
                        <td><?= format_currency($payment->payment_amount); ?></td>
                        <td><?= $payment->payment_method_name; ?></td>
                        <td><?= $payment->payment_note; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
