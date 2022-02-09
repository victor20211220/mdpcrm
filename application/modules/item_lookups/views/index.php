<div id="headerbar">
    <h1><?= lang('item_lookups'); ?></h1>

    <div class="pull-right">
        <a class="btn btn-sm btn-primary" href="<?= site_url('item_lookups/form'); ?>">
            <i class="fa fa-plus"></i> <?= lang('new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?= pager(site_url('item_lookups/index'), 'Mdl_item_lookups'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <?= $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?= lang('item_name'); ?></th>
                <th><?= lang('description'); ?></th>
                <th><?= lang('price'); ?></th>
                <th><?= lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($item_lookups as $item_lookup) : ?>
                <tr>
                    <td><?= $item_lookup->item_name; ?></td>
                    <td><?= $item_lookup->item_description; ?></td>
                    <td><?= format_currency($item_lookup->item_price); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-success btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i>
                                <?= lang('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= site_url('item_lookups/form/' . $item_lookup->item_lookup_id); ?>">
                                        <i class="fa fa-pencil fa-margin"></i>
                                        <?= lang('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= site_url('item_lookups/delete/' . $item_lookup->item_lookup_id); ?>"
                                       onclick="return confirm('<?= lang('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i>
                                        <?= lang('delete'); ?>
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
