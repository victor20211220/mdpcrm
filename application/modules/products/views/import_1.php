<style>
    .input-group-btn:last-child > .btn,
    .input-group-btn:last-child > .btn-group {
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
    <h1 class="m-n font-thin h3">
        <?= lang('import_products'); ?>
    </h1>
</div>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
    <div class="panel panel-default" style="border-radius: 10px !important">
        <div class="panel-heading font-bold"
             style="border-top-right-radius: 20px !important;border-top-left-radius: 20px !important;"
        >
            <?= lang('import_step_1'); ?>
        </div>

        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="form-group">
                <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    <label for="invoice_id" class="control-label">Select CSV file</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input ui-jq="filestyle" type="file" data-icon="false" data-classButton="btn btn-default"
                           name="fileUpload" data-classInput="form-control inline v-middle input-s">
                    <br/>
                    <span class="text-muted" style="margin-top: 30px !important">Any .csv file containing at least fields SKU (Stock Keeping Unit) and Product name. You will be able to rearrange fields in the next step.</span>
                    <br/><br/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="duplicate_rec" value="update" checked>
                            <i></i>
                            On duplicate record, update the database product
                        </label>
                    </div>

                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="duplicate_rec" value="ignore">
                            <i></i>
                            On duplicate record, ignore imported data and keep the product from the database
                        </label>
                    </div>

                    <span class="help-block m-b-none">
                        Duplicate products are considered those with the same <b>SKU</b>
                    </span>
                    </br>
                </div>

                <div class="col-lg-offset-2 col-lg-10">
                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="prod_family" value="create" checked>
                            <i></i>
                            If import family doesn't exist, create a new one
                        </label>
                    </div>

                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="prod_family" value="ignore">
                            <i></i>
                            If import family doesn't exist, do not fill family field
                        </label>
                    </div>

                    <span class="help-block m-b-none">
                        * Please make sure you have a category/family column in your import file
                    </span></br>
                </div>

                <div class="col-lg-offset-2 col-lg-10">
                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="prod_tax_cond" value="create" checked>
                            <i></i>
                            If import tax rate doesn't exist, create a new one in the database
                        </label>
                    </div>

                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" name="prod_tax_cond" value="ignore">
                            <i></i>
                            If import tax rate doesn't exists, leave the default item tax rate field empty
                        </label>
                    </div>

                    <span class="help-block m-b-none">
                        * Please make sure you have a item tax rate column in your import file
                    </span></br>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <div class="checkbox">
                        <label for="checkbox-3" class="i-checks">
                            <input type="checkbox" id="checkbox-3" class="checkbox11" name="import_has_header">
                            <i></i><?php echo lang('f_row_is_header'); ?>
                        </label>
                        <span><?= lang('if_checked_ignore'); ?></span>
                    </div>
                </div>
            </div>

            <?php if (!$this->session->flashdata('alert_success')){ ?>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs"></div>
                    <div class="col-xs-12 col-sm-6">
                        <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1">
                            Cancel
                        </button>
                        <button type="submit" id="btn-submit" name="btn_submit_1" class="btn btn-success" value="1">
                            Next Step
                        </button>
                    </div>
                </div>
            <?php } ?>
            <?php if ($this->session->flashdata('alert_success')){ ?>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs"></div>
                    <div class="col-xs-12 col-sm-6">
                        <a href="<?= base_url(); ?>" class="btn btn-success">
                            Finished
                        </a>
                    </div>
                </div>
            <?php } ?>
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
