<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('stock_management'); ?></h1>
        </div>
        <div style="margin-top:5px !important; margin-left: 32px !important" class="col-sm-6 col-xs-12">
            <a class="btn btn-sm btn-success" href="/stock/form">
                <i class="fa fa-plus"></i> <?= lang('new_check'); ?>
            </a>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="table-responsive">
                <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                       id="DataTables_Table_0"
                       role="grid" aria-describedby="DataTables_Table_0_info"
                       ui-options="{ aoColumns: [null, null, null, null, null] }">
                    <thead>
                    <tr>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            aria-sort="ascending">
                            <?= lang('stock_update_name'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            aria-sort="ascending">
                            <?= lang('stock_operation_data'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            aria-sort="ascending">
                            <?= lang('stock_products_updated'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            aria-sort="ascending">
                            <?= lang('user_that_updated'); ?>
                        </th>
                        <th><?= lang('options'); ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $class = 'odd'; ?>

                    <?php foreach ($stock as $import) : ?>
                        <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                        <tr role="row" class="<?= $class; ?>">
                            <td><?= $import->stock_update_name; ?></td>
                            <td><?= $import->stock_update_date; ?></td>
                            <td><?= $import->stock_products_updated; ?></td>
                            <td><?= $import->user_name; ?></td>
                            <td>
                                <a href="<?= site_url('stock/form/' . $import->stock_id); ?>"
                                   title="<?= lang('view'); ?>"><i class="fa fa-eye fa-margin"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
