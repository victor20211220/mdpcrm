<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('import_payments'); ?></h1>
</div>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading font-bold" style="border-top-right-radius: 5px;border-top-left-radius: 5px;">
                <?= lang('select_import_method'); ?>
            </div>

            <div class="panel-body">
                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio" onclick="window.location='<?= site_url('payments/import'); ?>';">
                            <i></i>
                            <?= lang('SEPA_xml'); ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label class="i-checks">
                            <input type="radio"
                                   onclick="window.location='<?= site_url('payments/import_iso_20022'); ?>';">
                            <i></i>
                            <?= lang('ISO_20022_xml'); ?>
                        </label>
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
