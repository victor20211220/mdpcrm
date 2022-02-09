<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('custom_fields'); ?></h1>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a class="btn btn-sm btn-success" href="/custom_fields/form">
                <i class="fa fa-plus"></i> <?= lang('new'); ?>
            </a>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div id="DataTables_Table_0_wrapper"
                 class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                <div class="col-sm-12">

                    <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                           id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                        <tr>
                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                colspan="1" aria-label="Rendering engine: activate to sort column descending"
                                aria-sort="ascending"
                            >
                                <?= lang('table'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending"
                            >
                                <?= lang('label'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('column'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('options'); ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $class = 'odd'; ?>

                        <?php foreach ($custom_fields as $custom_field) : ?>
                            <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $custom_field->custom_field_table; ?></td>
                                <td><?= $custom_field->custom_field_label; ?></td>
                                <td><?= $custom_field->custom_field_column; ?></td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            <i class="fa fa-cog"></i> <?= lang('options'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= site_url('custom_fields/form/' . $custom_field->custom_field_id); ?>">
                                                    <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= site_url('custom_fields/delete/' . $custom_field->custom_field_id); ?>"
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


