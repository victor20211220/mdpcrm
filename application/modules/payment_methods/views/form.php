<form method="post" class="form-horizontal">

    <div class=" lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('payment_form'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <input class="hidden" name="is_update" type="hidden"
                    <?php
                        if ($this->Mdl_payment_methods->form_value('is_update')) {
                            echo 'value="1"';
                        } else {
                            echo 'value="0"';
                        }
                    ?>
                />

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="payment_method_name" class="control-label">
                            <?= lang('payment_method'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="payment_method_name" id="payment_method_name" class="form-control"
                               value="<?= $this->Mdl_payment_methods->form_value('payment_method_name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    </div>
                    <div class="col-xs-12 col-sm-6 row">
                        <ul class="nav nav-pills nav-sm">
                            <div class="bg-white but-wrapper">
                                <a href="<?= site_url('payment_methods'); ?>" class="btn btn-default btn-sm" value="1">
                                    Cancel</a>
                                <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success btn-sm "
                                        value="1">Save
                                </button>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
