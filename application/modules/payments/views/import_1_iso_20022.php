<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('import_payments'); ?></h1>
</div>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading font-bold">
                <?= lang('import_step_1'); ?>
            </div>
            <div class="panel-body">
                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="invoice_id" class="control-label">
                            <?= lang('select_is_20022_xml'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input ui-jq="filestyle" accept=".xml" type="file" data-icon="false"
                               data-classButton="btn btn-default" name="fileUpload"
                               data-classInput="form-control inline v-middle input-s">
                    </div>
                </div>
                <div class="form-group"></div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">

                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <a href="/payments/select" id="btn-cancel" name="btn_cancel" class="btn btn-default">
                            <?= lang('cancel'); ?>
                        </a>
                        <button type="submit" id="btn-submit" name="btn_submit_1" class="btn btn-success">
                            <?= lang('next_step'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style type="text/css">
    span.group-span-filestyle.input-group-btn {
        padding-left: 10px !important;
    }

    .btn-default {
        padding-left: 10px !important;
        border-radius: 5px !important;
    }
</style>
