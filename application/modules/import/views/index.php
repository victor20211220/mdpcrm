<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('import_data'); ?></h1>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a class="btn btn-sm btn-success" href="/import/form">
                <i class="fa fa-plus"></i> <?= lang('new'); ?>
            </a>
        </div>
    </div>
</div>

<div class="wrapper-md">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= lang('import_data'); ?>
        </div>
        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="DataTables_Table_0_wrapper"
                 class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                <div class="col-sm-12">
                    <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                           id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                        <tr>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('id'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('date'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('clients'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('invoice_items'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('payments'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('options'); ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $class = 'odd'; ?>

                        <?php foreach ($imports as $import) : ?>
                            <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $import->import_id; ?></td>
                                <td><?= $import->import_date; ?></td>
                                <td><?= $import->num_clients; ?></td>
                                <td><?= $import->num_invoices; ?></td>
                                <td><?= $import->num_invoice_items; ?></td>
                                <td><?= $import->num_payments; ?></td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#"><i
                                                    class="fa fa-cog"></i> <?= lang('options'); ?></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= site_url('import/delete/' . $import->import_id); ?>"
                                                   onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
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
        </div>
    </div>
</div>
