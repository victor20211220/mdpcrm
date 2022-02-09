<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('recurring_invoices'); ?></h1>
</div>
<div class="">
    <div class="panel panel-default">
        <div class=" panel-body">
            <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer" id="DataTables_Table_0"
                   role="grid" aria-describedby="DataTables_Table_0_info">
                <thead>
                <tr role="row">
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('status'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('base_invoice'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('client'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('start_date'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('stop_date'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('every'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('next_date'); ?>
                    </th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                        aria-label="Browser: activate to sort column ascending">
                        <?= lang('options'); ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $class = 'odd';
                foreach ($recurring_invoices as $invoice) : ?>
                    <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                    <tr role="row" class="<?= $class; ?>">
                        <td>
                            <span class="label <?= $invoice->recur_status == 'active' ? "label-success" : 'canceled'; ?>">
                                <?= lang($invoice->recur_status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                <?= $invoice->invoice_number; ?>
                            </a>
                        </td>
                        <td>
                            <?= anchor('clients/view/' . $invoice->client_id, $invoice->client_name); ?>
                        </td>
                        <td>
                            <?= date_from_mysql($invoice->recur_start_date); ?>
                        </td>
                        <td>
                            <?= date_from_mysql($invoice->recur_end_date); ?>
                        </td>
                        <td>
                            <?= lang($recur_frequencies[$invoice->recur_frequency]); ?>
                        </td>
                        <td>
                            <?= date_from_mysql($invoice->recur_next_date); ?>
                        </td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?= lang('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?= site_url('invoices/recurring/stop/' . $invoice->invoice_recurring_id); ?>">
                                            <i class="fa fa-ban fa-margin"></i> <?= lang('stop'); ?>
                                        </a></li>
                                    <li>
                                        <a href="<?= '/invoices/recurring/delete/' . $invoice->invoice_recurring_id; ?>"
                                           onclick="return confirm('<?= lang('delete_record_warning'); ?>');"
                                        >
                                            <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div></div></div>
