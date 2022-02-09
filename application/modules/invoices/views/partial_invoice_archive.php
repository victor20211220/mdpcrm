<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?= lang('invoice'); ?></th>
            <th><?= lang('created'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($invoices_archive as $invoice) : ?>
            <tr>
                <td>
                    <a href="<?= "/invoices/download/" . basename($invoice); ?>"
                       title="<?= lang('invoice'); ?>" target="_blank">
                        <?= basename($invoice); ?>
                    </a>
                </td>
                <td>
                    <?= date("F d Y H:i:s.", filemtime($invoice)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
