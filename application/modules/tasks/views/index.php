<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('tasks'); ?></h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success padder create-task" href="#">
                <?= lang('create_task'); ?>
            </a>
        </div>
        <div class="col-sm-6 col-xs-12 text-right pull-right">
            <ul class="nav nav-pills nav-sm custom-right-submenu-slv">
                <li <?php if ($status == 'all') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('tasks/status/all'); ?>">
                        <?= lang('all_comp_tasks'); ?>
                    </a>
                </li>
                <li <?php if ($status == 'my_tasks') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('tasks/status/my_tasks'); ?>">
                        <?= lang('my_tasks'); ?>
                    </a>
                </li>
                <li <?php if ($status == 'not_started') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('tasks/status/not_started'); ?>">
                        <?= lang('not_started'); ?>
                    </a>
                </li>
                <li <?php if ($status == 'in_progress') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('tasks/status/in_progress'); ?>">
                        <?= lang('in_progress'); ?>
                    </a>
                </li>
                <li <?php if ($status == 'complete') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('tasks/status/complete'); ?>">
                        <?= lang('complete'); ?>
                    </a>
                </li>
            </ul>
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
                    <table ui-jq="dataTable" id="DataTables_Table_0"
                           role="grid" class="table table-striped m-b-none dataTable no-footer"
                           aria-describedby="DataTables_Table_0_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc col-md-2"><?= lang('task_name'); ?></th>
                            <th><?= lang('status'); ?></th>
                            <th><?= lang('assigned_to'); ?></th>
                            <th><?= lang('task_date_created'); ?></th>
                            <th><?= lang('task_finish_date'); ?></th>
                            <th><?= lang('time'); ?></th>
                            <th class="col-md-1"><?= lang('options'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $class = 'odd';
                        foreach ($tasks as $task) {
                            $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                            <tr role="row" class="<?= $class; ?>">
                                <td>
                                    <a href="<?= site_url('tasks/form/' . $task->task_id); ?>"
                                       title="<?= lang('edit'); ?>"><?= $task->task_name; ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $task_statuses["$task->task_status"]["label"]; ?>
                                </td>
                                <td>
                                    <?= $task->user_assigned; ?>
                                </td>
                                <td>
                                    <?= date_from_mysql($task->task_date_created, false, true); ?>
                                </td>
                                <td>
                                    <span class="<?php if ($task->is_overdue) { ?>font-overdue<?php } ?>">
                                        <?= date_from_mysql($task->task_finish_date); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($task->task_status == 3) { echo $task->total_time; } ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('tasks/form/' . $task->task_id); ?>"
                                       title="<?= lang('edit'); ?>">
                                        <i class="fa fa-pencil fa-margin"></i>
                                    </a>
                                    <a href="<?= site_url('tasks/delete/' . $task->task_id); ?>"
                                       title="<?= lang('delete'); ?>"
                                       onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($type == 'assign') { ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#modal-placeholder').load("<?= site_url('tasks/ajax/modal_assign_tasks'); ?>");
        });
    </script>

<?php } ?>

<?php if ($type == 'create') { ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#modal-placeholder').load("<?= site_url('tasks/ajax/modal_create_task'); ?>");
        });
    </script>

<?php } ?>
