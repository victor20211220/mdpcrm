<style>
    .input-group-btn:last-child > .btn, .input-group-btn:last-child > .btn-group {
        margin-left: -1px;
        border-top-left-radius: 5px !important;
        border-bottom-left-radius: 5px !important;
    }

    .input-group-btn {
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
        border-left: none !important;
    }
</style>
<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('import_clients'); ?></h1>
</div>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
    <div>
        <div class="panel panel-default">
            <div class="panel-heading font-bold" style="border-top-left: 20px !important;">
                <?= lang('import_step_1'); ?>
            </div>

            <div class="panel-body">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="invoice_id" class="control-label"><?= lang('select_csv'); ?></label>
                    </div>

                    <div class="form-group">
                        <input ui-jq="filestyle" type="file" style="padding-bottom: 30px !important" data-icon="false"
                               data-classButton="btn btn-default" name="fileUpload"
                               data-classInput="form-control inline v-middle input-s"
                        />
                        <br/>
                        <span class="text-muted" style="margin-top: 30px !important">Any .csv file containing at least fields Client Name, Registration Number, Street Address and Email. You will be able to rearrange fields in the next step.</span>
                        <br/><br/>
                    </div>

                    <div class="form-group">
                        <div class="radio">
                            <label class="i-checks">
                                <input type="radio" name="duplicate_rec" value="update" checked>
                                <i></i>
                                <?= lang('client_imp_1'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="radio">
                            <label class="i-checks">
                                <input type="radio" name="duplicate_rec" value="ignore">
                                <i></i>
                                <?= lang('client_imp_2'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="help-block m-b-none"><?= lang('client_imp_2_help'); ?></span>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label for="checkbox-3" class="i-checks">
                                <input type="checkbox" id="checkbox-3" class="checkbox11" name="import_has_header">
                                <i></i><?php echo lang('f_row_is_header'); ?>
                            </label>
                            <span><?= lang('if_checked_ignore'); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <a href="<?= $this->agent->referrer(); ?>" id="btn-cancel" name="btn_cancel"
                           class="btn btn-default btn-sm" value="1">
                           <?= lang('cancel'); ?>
                        </a>
                        <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success btn-sm" value="1">
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
