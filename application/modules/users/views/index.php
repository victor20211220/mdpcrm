<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('users'); ?></h1>
        </div>
        <div class="col-sm-3 col-xs-12">
            <a class="btn btn-sm btn-success" href="/users/form">
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
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('name'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('company'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('user_type'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('email_address'); ?>
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                aria-label="Browser: activate to sort column ascending">
                                <?= lang('options'); ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $class = 'odd'; ?>

                        <?php foreach ($users as $user) : ?>
                            <?php $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td><?= $user->user_name; ?></td>
                                <td><?= $user->company_name; ?></td>
                                <td><?= $user_types[$user->user_type]; ?></td>
                                <td><?= $user->user_email; ?></td>
                                <td>
                                    <?php if (
                                            ($my_user_type == 3 || $user->user_type == 0)
                                            ||
                                            (($my_user_type != 3 && $user->user_type != 0) && $user->user_id == $my_user_id)
                                    ) : ?>
                                        <div class="options btn-group">
                                            <a class="btn btn-sm btn-success dropdown-toggle"
                                               data-toggle="dropdown" href="#">
                                                <i class="fa fa-cog"></i> <?= lang('options'); ?>
                                            </a>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="<?= site_url('users/form/' . $user->user_id); ?>">
                                                        <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                                    </a>
                                                </li>

                                                <?php if (($my_user_type == 3 || $user->user_type == 0) && ($user->user_id != $my_user_id)) : ?>
                                                    <li>
                                                        <a href="<?= site_url('users/delete/' . $user->user_id); ?>"
                                                           onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                                            <i class="fa fa-trash-o fa-margin"></i> <?= lang('delete'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                            </ul>

                                        </div>
                                    <?php endif; ?>
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
