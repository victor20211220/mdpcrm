<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
    <div class="col-sm-12">

        <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer" id="DataTables_Table_0"
               role="grid" aria-describedby="DataTables_Table_0_info"
               ui-options="{
                  aoColumns: [
                    null,null,null,null,null,
                    {sType: 'custom-amount-sort'},
                    {sType: 'custom-amount-sort'},
                    null
                  ]
               }"
        >
            <thead>
            <tr role="row">
                <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                    rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                    aria-sort="ascending">
                    <?= lang('status'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1" aria-label="Browser: activate to sort column ascending">
                    <?= lang('invoice'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                    <?= lang('created'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1"
                    aria-label="Engine version: activate to sort column ascending">
                    <?= lang('due_date'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1" aria-label="CSS grade: activate to sort column ascending"
                    style="text-align: left; padding-right: 25px;">
                    <?= lang('client_name'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1" aria-label="CSS grade: activate to sort column ascending">
                    <?= lang('amount'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                    colspan="1" aria-label="CSS grade: activate to sort column ascending">
                    <?= lang('balance'); ?>
                </th>
                <th class="no-sort" rowspan="1" width="1%" style="width: 10px !important;">
                    <?= lang('options'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php $class = 'odd';
            foreach ($invoices as $invoice) {
                $class = ($class == 'odd') ? 'even' : 'odd';
                if ($this->config->item('disable_read_only') == true) {
                    $invoice->is_read_only = 0;
                }
                ?>
                <?php if ($this->db->get_where('ip_invoice_items',
                        ['invoice_id' => $invoice->invoice_id])->num_rows() > 0) { ?>
                    <tr role="row" class="<?= $class; ?>">
                        <td>
                            <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Draft'): ?>
                                <i class="fa fa-circle yellow"></i>
                            <?php endif; ?>
                            <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Sent'): ?>
                                <i class="fa fa-circle green"></i>
                            <?php endif; ?>
                            <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Paid'): ?>
                                <i class="fa fa-circle green"></i>
                            <?php endif; ?>

                            <?= $invoice_statuses[$invoice->invoice_status_id]['label'];
                            if ($invoice->invoice_sign == '-1'): ?>
                                &nbsp;
                                <i class="fa fa-credit-invoice" title="<?= lang('credit_invoice') ?>"></i>
                            <?php endif; ?>
                            <?php if ($invoice->is_read_only == 1): ?>
                                &nbsp;
                                <i class="fa fa-read-only" title="<?= lang('read_only') ?>"></i>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="<?= "/invoices/view/{$invoice->invoice_id}"; ?>" title="<?= lang('edit'); ?>">
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
                            <a href="<?= "/clients/view/{$invoice->client_id}"; ?>" title="<?= lang('view_client'); ?>">
                                <?= $invoice->client_name; ?>
                            </a>
                        </td>

                        <td class="amount <?= $invoice->invoice_sign == '-1' ? 'text-danger' : null; ?>">
                            <?= format_currency($invoice->invoice_total); ?>
                        </td>

                        <td class="amount">
                            <?= format_currency('-' . $invoice->invoice_balance); ?>
                        </td>

                        <td width="1% " style="width: 1px !important">
                            <div class="options btn-group">
                                <a class="btn btn-success btn-sm dropdown-toggle btndrop" data-toggle="dropdown"
                                   href="#">
                                    <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($invoice->is_read_only != 1) : ?>
                                        <li>
                                            <a href="<?= "/invoices/view/{$invoice->invoice_id}"; ?>">
                                                <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <a href="<?= "/invoices/generate_pdf/{$invoice->invoice_id}"; ?>" target="_blank">
                                            <i class="fa fa-file-pdf-o fa-margin"></i> <?= lang('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= "/mailer/invoice/{$invoice->invoice_id}"; ?>">
                                            <i class="fa fa-send fa-margin"></i> <?= lang('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="invoice-add-payment"
                                           data-invoice-id="<?= $invoice->invoice_id; ?>"
                                           data-invoice-balance="<?= $invoice->invoice_balance; ?>"
                                           data-invoice-payment-method="<?= $invoice->payment_method; ?>">
                                            <i class="fa fa-money fa-margin"></i>
                                            <?= lang('enter_payment'); ?>
                                        </a>
                                    </li>
                                    <?php if (
                                            $invoice->invoice_status_id == 1 ||
                                            ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)
                                    ) : ?>
                                        <li>
                                            <a href="<?= "/invoices/delete/{$invoice->invoice_id}"; ?>"
                                               onclick="return confirm('<?= lang('delete_invoice_warning'); ?>');">
                                                <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
