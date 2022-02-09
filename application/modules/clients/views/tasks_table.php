<div class="container">
    <div id="DataTables_Table_tasks_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
        <div class="col-sm-12">

            <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer" id="DataTables_Table_tasks"
               role="grid" aria-describedby="DataTables_Table_quotes_info"
               ui-options="{
                  aoColumns: [
                    null,null,null,null,null,
                    {sType: 'custom-amount-sort'},
                    null
                  ]
                }">
                <thead>
                <tr role="row">
                    <th class="col-md-2"><?= lang('task_name'); ?></th>
                    <th><?= lang('status'); ?></th>
                    <th><?= lang('assigned_to'); ?></th>
                    <th><?= lang('task_date_created'); ?></th>
                    <th><?= lang('task_finish_date'); ?></th>
                    <th><?= lang('time'); ?></th>
                    <th class="col-md-1"><?= lang('options'); ?></th>
                </thead>
                <tbody>

                <?php $class = 'odd'; ?>
                <?php foreach ($tasks as $task) : ?>
                    <? $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                    <tr role="row" class="<?= $class; ?>">
                        <td>
                            <a href="<?= "/tasks/form/{$task['task_id']}" ?>"
                               title="<?= lang('edit'); ?>">
                                <?= $task['task_name']; ?>
                            </a>
                        </td>
                        <td>
                            <?= $task["task_status"]; ?>
                        </td>
                        <td>
                            <?= $task["user_assigned"]; ?>
                        </td>
                        <td>
                            <?= date('m/d/Y', $task['task_date_created']); ?>
                        </td>
                        <td>
                            <span class="<?php if ($task['is_overdue']) { ?>font-overdue<?php } ?>">
                                <?= date('m/d/Y', $task['task_finish_date']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($task['task_status'] == 3) { echo $task['total_time']; } ?>
                        </td>
                        <td>
                            <a href="<?= "/tasks/form/{$task['task_id']}"; ?>"
                               title="<?= lang('edit'); ?>">
                                <i class="fa fa-pencil fa-margin"></i>
                            </a>
                            <a href="<?= "/tasks/delete/{$task['task_id']}"; ?>"
                               title="<?= lang('delete'); ?>"
                               onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                <i class="fa fa-trash-o fa-margin"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
