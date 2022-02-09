<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
    <div class="col-sm-12">
        <table ui-jq="dataTable" class="table m-b-none dataTable " id="DataTables_Table_payments"
               role="grid" aria-describedby="DataTables_Table_payments_info"
               ui-options="{order: [[ 0, 'desc' ]]}"
        >
            <thead>
            <tr role="row">
                <th style='min-width: 180px'><?= lang('payment_date'); ?></th>
                <th style='min-width: 80px'><?= lang('invoice_date'); ?></th>
                <th style='min-width: 80px'><?= lang('invoice'); ?></th>
                <th><?= lang('client'); ?></th>
                <th style="text-align: left; padding-right: 25px;"><?= lang('amount'); ?></th>
                <th><?= lang('payment_method'); ?></th>
                <th><?= lang('import_type'); ?></th>
                <th class="no-sort" rowspan="1" colspan="1"><?= lang('options'); ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $class = 'odd'; ?>
            <?php if ($payments): ?>
            <?php foreach ($payments as $payment) : ?>
                <tr role="row" class="<?= $class; ?> ">
                    <td style="text-align: center">
                        <?= $payment->payment_date; ?>
                    </td>
                    <td style="text-align: center">
                        <?= $payment->invoice_date_created; ?>
                    </td>
                    <td style="text-align: center">
                        <a href="<?= '/invoices/view/' . $payment->invoice_id; ?>">
                            <?= $payment->invoice_number; ?>
                        </a>
                    </td>
                    <td style="text-align: center">
                        <a href="<?= '/invoices/view/' . $payment->client_id; ?>">
                            <?= $payment->client_name; ?>
                        </a>
                    </td>
                    <td style="text-align: center">
                        <?= $payment->invoice_balance; ?>
                    </td>
                    <td style="text-align: center">
                        <?= $payment->payment_method_name; ?>
                    </td>
                    <td style="text-align: center">
                        --
                    </td>
                    <td style="text-align: center">
                        <div class="options btn-group">
                            <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= "/payments/form/{$payment->payment_id}"; ?>">
                                        <i class="fa fa-pencil fa-margin"></i>
                                        <?= lang('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= '/payments/delete/' . $payment->payment_id; ?>"
                                       onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i>
                                        <?= lang('delete'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= "/mailer/invoice/{$payment->payment_id}"; ?>">
                                        <i class="fa fa-send fa-margin "></i> <?= lang('send_email'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
