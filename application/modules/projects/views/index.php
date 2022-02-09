<div id="headerbar">
    <h1><?= lang('projects'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="/projects/form'">
            <i class="fa fa-plus"></i> <?= lang('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?= pager(site_url('projects/index'), 'Mdl_projects'); ?>
    </div>
</div>

<div class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?= lang('project_name'); ?></th>
                <th><?= lang('client_name'); ?></th>
                <th><?= lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($projects as $project) : ?>
                <tr>
                    <td><?= $project->project_name; ?></td>
                    <td><?= ($project->client_id) ? $project->client_name : lang('none'); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-success btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?= lang('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= "/projects/form/{$project->project_id}"; ?>">
                                        <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= "/projects/delete/{$project->project_id}" ?>"
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
