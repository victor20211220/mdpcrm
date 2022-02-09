<div id="headerbar">
    <h1><?= lang('companies'); ?></h1>
    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="/companies/form">
            <i class="fa fa-plus"></i> <?= lang('new'); ?>
        </a>
    </div>
    <div class="pull-right">
        <?= pager(site_url('companies/index'), 'Mdl_companies'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <?= $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?= lang('name'); ?></th>
                <th><?= lang('country'); ?></th>
                <th><?= lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($companies as $company) : ?>
                <tr>
                    <td><?= $company->company_name; ?></td>
                    <td><?= $countries[$company->company_country]; ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-sm btn-success dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?= lang('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= "/companies/form/{$company->company_id}"; ?>">
                                        <i class="fa fa-pencil fa-margin"></i> <?= lang('edit'); ?>
                                    </a>
                                </li>
                                    <li>
                                        <a href="<?= "/companies/delete/{$company->company_id}"; ?>"
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
