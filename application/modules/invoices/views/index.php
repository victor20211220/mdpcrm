<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <span class="page-header" style="font-size: 24px; font-weight: bolder; color: #555; margin-left: 30px">
                <?= lang('invoices'); ?>
            </span>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success create-invoice padder-h" href="#">
                <?= lang('create_invoice'); ?>
            </a>
        </div>
        <div class="col-sm-6 col-xs-12 text-right pull-right">
            <ul class="nav nav-pills nav-sm custom-right-submenu-slv" style="margin-top: 10px !important">
                <li style="margin-left: -5px !important" <?= $status == 'all' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/all"><?= lang('all'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?= $status == 'draft' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/draft"><?= lang('draft'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?= $status == 'sent' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/sent"><?= lang('sent'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?= $status == 'viewed' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/viewed"><?= lang('viewed'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?= $status == 'approved' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/paid"><?= lang('paid'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?= $status == 'rejected' ? "class='active'" : null; ?>>
                    <a href="/invoices/status/overdue"><?= lang('overdue'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class=" panel-body">
            <div id="filter_results">
                <?php $this->layout->load_view('invoices/partial_invoice_table', ['invoices' => $invoices]); ?>
            </div>
        </div>
    </div>
</div>
