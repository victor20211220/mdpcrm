<style>
    .dataTables_filter label {
        width: 100% !important;
    }

    .dataTables_filter .input-sm {
        width: 100% !important;
    }
</style>

<div id="DataTables_Table_quotes_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
    <div class="col-sm-12">

        <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer" id="DataTables_Table_quotes"
               role="grid" aria-describedby="DataTables_Table_quotes_info"
               ui-options="{
                  dom: '<\'col-md-12 m-b-sm\'<\'col-md-9 text-left\'f><\'col-md-3 text-right\'l>><t>p',
                  aoColumns: [
                    null,null,null,null,null,
                    {sType: 'custom-amount-sort'},
                    null
                  ]
                }">
            <thead>
            <tr role="row">
                <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_quotes"
                    rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                    aria-sort="ascending">
                    <?= lang('status'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_quotes" rowspan="1"
                    colspan="1" aria-label="Browser: activate to sort column ascending">
                    <?= lang('quote'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_quotes" rowspan="1"
                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                    <?= lang('created'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_quotes" rowspan="1"
                    colspan="1" aria-label="Engine version: activate to sort column ascending">
                    <?= lang('due_date'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_quotes" rowspan="1"
                    colspan="1" aria-label="CSS grade: activate to sort column ascending"
                    style="text-align: left; padding-right: 25px;">
                    <?= lang('client_name'); ?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_quotes" rowspan="1"
                    colspan="1" aria-label="CSS grade: activate to sort column ascending">
                    <?= lang('amount'); ?>
                </th>
                <th rowspan="1" colspan="1" width="5%">
                    <?= lang('options'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php $class = 'odd'; ?>
            <?php foreach ($quotes as $quote): ?>
                <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                <?php if ($this->db->get_where('ip_quote_items', ['quote_id' => $quote->quote_id])->num_rows() > 0): ?>
                    <tr role="row" class="<?= $class; ?>">
                        <td>
                            <?php if ($quote_statuses[$quote->quote_status_id]['label'] == 'Draft') { ?>
                                <i class="fa fa-circle yellow"></i>
                            <?php } ?>
                            <?php if ($quote_statuses[$quote->quote_status_id]['label'] == 'Approved') { ?>
                                <i class="fa fa-circle green"></i>
                            <?php } ?>
                            <?php if ($quote_statuses[$quote->quote_status_id]['label'] == 'Sent') { ?>
                                <i class="fa fa-circle green"></i>
                            <?php }
                            echo $quote_statuses[$quote->quote_status_id]['label']; ?>
                        </td>
                        <td>
                            <a href="<?= "/quotes/view/{$quote->quote_id}"; ?>"
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
                            <a href="<?= "/clients/view/{$quote->client_id}"; ?>"
                               title="<?= lang('view_client'); ?>">
                                <?= $quote->client_name; ?>
                            </a>
                        </td>
                        <td style="text-align: left; padding-right: 25px;">
                            <?= format_currency($quote->quote_total); ?>
                        </td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown"
                                   href="#">
                                    <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?= "/quotes/view/{$quote->quote_id}"; ?>">
                                            <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= "/quotes/generate_pdf/{$quote->quote_id}"; ?>"
                                           target="_blank">
                                            <i class="fa fa-file-pdf-o fa-margin"></i> <?= lang('download_pdf'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= "/mailer/quote/{$quote->quote_id}" ; ?>">
                                            <i class="fa fa-send fa-margin "></i> <?= lang('send_email'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= "/quotes/delete/{$quote->quote_id}" ?>"
                                           onclick="return confirm('<?= lang('delete_quote_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
