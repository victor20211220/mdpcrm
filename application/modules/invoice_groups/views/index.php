<div class="lter wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3">
                <?= lang('invoice_groups'); ?>
            </h1>
        </div>
    </div>
</div>

<div class="">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="active">
                <a href="#tab1" data-toggle="tab"><?= lang('invoice_groups'); ?>
                    <span class="badge bg-primary badge-sm m-l-xs"></span>
                </a>
            </li>
            <li>
                <a href="#tab2" data-toggle="tab"><?= lang('received_invoice_groups'); ?>
                    <span class="badge badge-sm m-l-xs"></span>
                </a>
            </li>

        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <div class="tab-info">
                    <div class="row" style="padding-top: 20px; padding-bottom: 20px; padding-left: 15px">
                        <a class="btn btn-success" href="<?= site_url('invoice_groups/form/1'); ?>">
                            <i class="fa fa-plus"></i> <?= lang('new'); ?>
                        </a></div>
                    <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                           id="DataTables_Table_0"
                           role="grid" aria-describedby="DataTables_Table_0_info">

                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('name'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('next_id'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('left_pad'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('options'); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $class = 'odd';
                        foreach ($groups['default'] as $group) : ?>
                            <? $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $group->invoice_group_name; ?></td>
                                <td><?= $group->invoice_group_next_id; ?></td>
                                <td><?= $group->invoice_group_left_pad; ?></td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-success btn-sm dropdown-toggle"
                                           data-toggle="dropdown" href="#">
                                            <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= "/invoice_groups/form/{$group->invoice_group_type}/{$group->invoice_group_id}"; ?>">
                                                    <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= "/invoice_groups/delete/{$group->invoice_group_id}"; ?>"
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
            <div class="tab-pane" id="tab2">
                <div class="tab-info">
                    <div class="row" style="padding-top: 20px; padding-bottom: 20px; padding-left: 15px">
                        <a class="btn btn-success" href="<?= site_url('invoice_groups/form/2'); ?>">
                            <i class="fa fa-plus"></i> <?= lang('new'); ?>
                        </a></div>
                    <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                           id="DataTables_Table_0"
                           role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('name'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('next_id'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('left_pad'); ?>
                            </th>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                rowspan="1" colspan="1" aria-sort="ascending">
                                <?= lang('options'); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $class = 'odd'; ?>
                        <?php foreach ($groups['received'] as $group) : ?>
                            <? $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $group->invoice_group_name; ?></td>
                                <td><?= $group->invoice_group_next_id; ?></td>
                                <td><?= $group->invoice_group_left_pad; ?></td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-success btn-sm dropdown-toggle"
                                           data-toggle="dropdown" href="#">
                                            <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= "/invoice_groups/form/{$group->invoice_group_type}/{$group->invoice_group_id}"; ?>">
                                                    <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= "/invoice_groups/delete/{$group->invoice_group_id}"; ?>"
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
