<div class=" lter wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('tax_rates'); ?></h1>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a class="btn btn-sm btn-success" href="<?= site_url('tax_rates/form'); ?>">
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
                           id="DataTables_Table_0"
                           role="grid" aria-describedby="DataTables_Table_0_info">
                        <thead>
                        <tr>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('tax_rate_name'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('tax_rate_percent'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('tax_rate_percent'); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $class = 'odd'; ?>
                        <?php foreach ($tax_rates as $tax_rate) : ?>
                            <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $tax_rate->tax_rate_name; ?></td>
                                <td><?= $tax_rate->tax_rate_percent; ?>%</td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            <i class="fa fa-cog"></i> <?= lang('options'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= "/tax_rates/form/{$tax_rate->tax_rate_id}"; ?>">
                                                    <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= "/tax_rates/delete/{$tax_rate->tax_rate_id}"; ?>"
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

