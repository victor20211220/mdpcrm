<div class="lter wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3">
                <?= lang('payments'); ?>
            </h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success padder" href="/payments/form">
                <?= lang('create_new'); ?>
            </a>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php $this->layout->load_view('layout/alerts'); ?>
            <?php $this->layout->load_view('payments/partial_payment_table'); ?>
        </div>
    </div>
</div>
