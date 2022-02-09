<form method="post" class="form-horizontal">

    <div class=" lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('tax_rates'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label">
                            <?= lang('tax_rate_name'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="tax_rate_name" id="tax_rate_name" class="form-control"
                               value="<?= $this->Mdl_tax_rates->form_value('tax_rate_name'); ?>"
                        />
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label">
                            <?= lang('tax_rate_percent'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="tax_rate_percent" id="tax_rate_percent" class="form-control"
                               value="<?= $this->Mdl_tax_rates->form_value('tax_rate_percent'); ?>">
                        <span class="form-control-feedback">%</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    </div>
                    <div class="col-xs-12 col-sm-6 row">
                        <ul class="nav nav-pills nav-sm">
                            <?php $this->layout->load_view('layout/header_buttons'); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
