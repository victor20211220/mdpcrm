<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('stock_alerts'); ?></h1>
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
                       ui-options="{ aoColumns: [null, null, null, null] }"
                >
                    <thead>
                    <tr>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-sort="ascending">
                            <?= lang('product_name'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-sort="ascending">
                            <?= lang('family_name'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-sort="ascending">
                            <?= lang('stock'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-sort="ascending">
                            <?= lang('stock_alert'); ?>
                        </th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($alerts as $alert) { echo $alert; } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
