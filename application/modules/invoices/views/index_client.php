<div id="headerbar">

    <h1><?= lang('invoices'); ?></h1>

    <div class="pull-right">
        <a class="create-invoice btn btn-sm btn-primary padder" href="#">
            <?= lang('create_new'); ?>
        </a>
    </div>

    <div class="pull-right">
        <?= pager("/invoices/client/{$client_id}/{$status}", 'Mdl_invoices'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?= $status == 'open' ? "class='active'" : null ; ?>>
                <a href="<?= "/invoices/client/{$client_id}/open"; ?>">
                    <?= lang('open'); ?>
                </a>
            </li>
            <li <?= $status == 'closed' ? "class='active'" : null; ?>>
                <a href="<?= "/invoices/client/{$client_id}/closed"; ?>">
                    <?= lang('closed'); ?>
                </a>
            </li>
            <li <?= $status == 'overdue' ? "class='active'" : null; ?>>
                <a href="<?= "/invoices/client/{$client_id}/overdue"; ?>">
                    <?= lang('overdue'); ?>
                </a>
            </li>
        </ul>
    </div>

</div>

<div id="content" class="table-content">
    <?php $this->layout->load_view('invoices/partial_invoice_table', ['invoices' => $invoices]); ?>
</div>
