<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('invoice_archive'); ?></h1>
</div>

<div class="wrapper bg-white b-b">
    <ul class="nav nav-pills nav-sm">
        <li>
            <form action="<?= '/invoices/archive/'; ?>" method="post">
                <div class="input-group">
                    <input name="invoice_number" id="invoice_number" type="text" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                            <?= lang('filter_invoices'); ?>
                        </button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
</div>

<div class="wrapper-md">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= lang('invoice_archive'); ?>
        </div>
        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="table-responsive">
                <div id="filter_results">
                    <?php $this->layout->load_view(
                        'invoices/partial_invoice_archive',
                        ['invoices_archive' => $invoices_archive]
                    ); ?>
                </div>
            </div>

        </div>
    </div>
</div>
