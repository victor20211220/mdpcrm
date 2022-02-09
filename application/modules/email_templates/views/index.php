<div class=" lter wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('email_templates'); ?></h1>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a class="btn btn-sm btn-success" href="/email_templates/form">
                <i class="fa fa-plus"></i> <?= lang('new'); ?>
            </a>
        </div>
    </div>
</div>


<div class="panel panel-default" style="min-height: 300px ;">
    <div class="panel-body" style="min-height: 300px ;">

        <?php $this->layout->load_view('layout/alerts'); ?>
        <div id="DataTables_Table_0_wrapper"
             class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive" style="min-height: 300px ;">
            <div class="col-sm-12">
                <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                       id="DataTables_Table_0"
                       role="grid" aria-describedby="DataTables_Table_0_info"
                >

                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                            aria-sort="ascending">
                            <?= lang('title'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                            aria-sort="ascending">
                            <?= lang('type'); ?>
                        </th>
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                            rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                            aria-sort="ascending">
                            <?= lang('options'); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $class = 'odd'; ?>

                    <?php foreach ($email_templates as $tpl): ?>
                        <? $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                        <tr role="row" class="<?= $class; ?>">
                            <td><?= $tpl->email_template_title; ?></td>
                            <td><?= lang($tpl->email_template_type); ?></td>
                            <td>
                                <div class="options btn-group">
                                    <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-cog blueheader"></i> <?= lang('options'); ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?= "email_templates/form/{$tpl->email_template_id}"; ?>">
                                                <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= "email_templates/delete/{$tpl->email_template_id}"; ?>"
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
