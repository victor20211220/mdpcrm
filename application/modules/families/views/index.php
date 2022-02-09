<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('families'); ?></h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success" href="<?= site_url('families/form'); ?>">
                <i class="fa fa-plus"></i> <?= lang('new'); ?>
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
                       role="grid" aria-describedby="DataTables_Table_0_info">
                    <thead>
                    <tr>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                            aria-sort="ascending">
                            <?= lang('family_name'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-sort="ascending">
                            <?= lang('options'); ?>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $class = 'odd';
                    foreach ($families as $family) {
                        $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                        <tr role="row" class="<?= $class; ?>">
                            <td><?= $family->family_name; ?></td>
                            <td>
                                <div class="options btn-group">
                                    <a class="btn btn-success btn-sm dropdown-toggle"
                                       data-toggle="dropdown" href="#">
                                        <i class="fa fa-cog"></i> <?= lang('options'); ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?= "/families/form/{$family->family_id}"; ?>">
                                                <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= "/families/delete/{$family->family_id}"; ?>"
                                               onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                                <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
