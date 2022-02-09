<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('quotes'); ?></h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success create-quote padder-h" href="#">
                <?= lang('create_quote'); ?>
            </a>
        </div>
        <div class="col-sm-6 col-xs-12 text-right pull-right">
            <ul class="nav nav-pills nav-sm custom-right-submenu-slv"
                style="margin-top: 10px !important; margin-left: 10px !important; margin-right: 0px !important">
                <li style="margin-left: -5px !important" <?php if ($status == 'all') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/all'); ?>"><?= lang('all'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'draft') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/draft'); ?>"><?= lang('draft'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'sent') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/sent'); ?>"><?= lang('sent'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'viewed') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/viewed'); ?>"><?= lang('viewed'); ?>
                    </a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'approved') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/approved'); ?>"><?= lang('approved'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'rejected') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/rejected'); ?>"><?= lang('rejected'); ?></a>
                </li>
                <li style="margin-left: -5px !important" <?php if ($status == 'canceled') { ?>class="active"<?php } ?>>
                    <a href="<?= site_url('quotes/status/canceled'); ?>"><?= lang('canceled'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">

            <div id="filter_results">
                <?php $this->layout->load_view('quotes/partial_quote_table', ['quotes' => $quotes]); ?>
            </div>
        </div>
    </div>
</div>
