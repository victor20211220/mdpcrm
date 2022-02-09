<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('view_received_invoices'); ?></h1>
        </div>

        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success padder create-received-invoice" href="#">
                <?= lang('create_new'); ?>
            </a>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="filter_results">
                <div id="DataTables_Table_0_wrapper"
                     class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">

                    <div class="col-sm-12">

                        <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                               id="DataTables_Table_0"
                               role="grid" aria-describedby="DataTables_Table_0_info">
                            <thead>

                            <tr role="row">
                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1"
                                    aria-label="Browser: activate to sort column ascending">
                                    <?= lang('invoice'); ?>
                                </th>

                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1"
                                    aria-label="Platform(s): activate to sort column ascending">
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
                                    <?= lang('issuer_name'); ?>
                                </th>

                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="CSS grade: activate to sort column ascending"
                                    style="text-align: left; padding-right: 25px;">
                                    <?= lang('type'); ?>
                                </th>

                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1"
                                    aria-label="CSS grade: activate to sort column ascending">
                                    <?= lang('amount'); ?>
                                </th>

                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1"
                                    aria-label="CSS grade: activate to sort column ascending">
                                    <?= lang('balance'); ?>
                                </th>
                                <th width="10%" class="no-sort" rowspan="1" colspan="1">
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

                                <tr role="row" class="<?= $class; ?>">
                                    <td>
                                        <a href="<?= site_url('invoices/view/' . $invoice->invoice_id); ?>" title="<?= lang('edit'); ?>">
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
                                        <?= $invoice->supplier_name; ?>
                                    </td>

                                    <td>
                                        <?php if ($invoice->is_received == 0) {
                                            echo 'Recieved through system';
                                        } else {
                                            echo 'Manually added';
                                        } ?>
                                    </td>

                                    <td class="amount<?= $invoice->invoice_sign == '-1' ? 'text-danger' : null; ?>">
                                        <?= format_currency($invoice->invoice_total); ?>
                                    </td>

                                    <td class="amount">
                                        <?= format_currency($invoice->invoice_balance); ?>
                                    </td>

                                    <td>
                                        <div class="options btn-group">
                                            <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                               href="#">
                                                <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                            </a>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="<?= site_url('invoices/view/' . $invoice->invoice_id); ?>">
                                                        <i class="fa fa-pencil fa-margin"></i><?= lang('edit'); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= site_url('invoices/generate_pdf/' . $invoice->invoice_id . '/true/' . $invoice->pdf_invoice_template); ?>"
                                                       target="_blank">
                                                        <i class="fa fa-file-pdf-o fa-margin"></i> <?= lang('download_pdf'); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?= site_url('invoices/delete/' . $invoice->invoice_id); ?>">
                                                        <i class="fa fa-trash-o fa-margin"></i><?= lang('delete'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <div style="width: 100%; text-align: center">
                        <?php if (!empty($receivingGroups)): ?>
                            <a href="http://<?= $companyUrl; ?>.mdpcrm.com/guest/invoices/create" style="color: darkslateblue">
                                Link for creating guest invoices from your customers -
                                http://<?= $companyUrl; ?>.mdpcrm.com/guest/invoices/create
                            </a>
                        <?php else: ?>
                            <span style="color: red">
                                Note: there must be a least 1 receiving invoice group for creating guest invoice
                            </span>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
