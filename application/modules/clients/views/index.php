<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3">
                <?= lang('clients'); ?>
            </h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success padder" href="/clients/form">
                <?= lang('create_new'); ?>
            </a>
        </div>
        <div class="col-sm-6 col-xs-12 text-right pull-right">
            <ul class="nav nav-pills nav-sm custom-right-submenu-slv" style="margin-top: 10px !important">
                <li style="margin-left: -5px !important"
                    <?php if ($this->uri->segment(3) == 'active' or !$this->uri->segment(3)) { ?>class="active"<?php } ?>>
                    <a href="/clients/status/active"><?= lang('active'); ?></a>
                </li>
                <li style="margin-left: -5px !important"
                    <?php if ($this->uri->segment(3) == 'inactive') { ?>class="active"<?php } ?>>
                    <a href="/clients/status/inactive"><?= lang('inactive'); ?></a>
                </li>
                <li style="margin-left: -5px !important"
                    <?php if ($this->uri->segment(3) == 'all') { ?>class="active"<?php } ?>>
                    <a href="/clients/status/all"><?= lang('all'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php $this->layout->load_view('layout/alerts'); ?>

<div id="filter_results">
    <?php $this->layout->load_view('clients/partial_client_table'); ?>
</div>
