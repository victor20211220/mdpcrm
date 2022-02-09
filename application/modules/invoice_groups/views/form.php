<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('invoice_group_form'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                        <label class="control-label" for="invoice_group_name">
                            <?= lang('name'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-8">
                        <input type="text" name="invoice_group_name" id="invoice_group_name" class="form-control"
                               value="<?= $this->Mdl_invoice_groups->form_value('invoice_group_name'); ?>"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                        <label class="control-label" for="invoice_group_identifier_format">
                            <?= lang('identifier_format'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-8">
                        <input type="text" class="form-control taggable"
                               name="invoice_group_identifier_format" id="invoice_group_identifier_format"
                               value="<?= empty($this->Mdl_invoice_groups->form_value('invoice_group_identifier_format'))? 'INV-{{{id}}}' : $this->Mdl_invoice_groups->form_value('invoice_group_identifier_format') ; ?>"
                               placeholder="INV-{{{id}}}"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                        <label class="control-label" for="invoice_group_next_id">
                            <?= lang('next_id'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-8">
                        <input type="text" name="invoice_group_next_id" id="invoice_group_next_id" class="form-control"
                               value="<?= $this->Mdl_invoice_groups->form_value('invoice_group_next_id'); ?>"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 col-lg-2 text-right text-left-xs">
                        <label class="control-label" for="invoice_group_left_pad">
                            <?= lang('left_pad'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-8">
                        <input type="text" name="invoice_group_left_pad" id="invoice_group_left_pad"
                               class="form-control"
                               value="<?= $this->Mdl_invoice_groups->form_value('invoice_group_left_pad'); ?>"
                        />
                    </div>
                </div>
                <br>

                <div class="form-group">
                    <div class="col-xs-12 col-lg-2">
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-4">
                        <h4><?= lang('identifier_format_template_tags'); ?></h4>
                        <p><?= lang('identifier_format_template_tags_instructions'); ?></p>
                        <a onclick="formulateInv(this)" class="text-tag text-info" data-tag="{{{id}}}"><?= lang('id'); ?></a><br>
                        <a onclick="formulateInv(this)" class="text-tag text-info" data-tag="{{{year}}}"><?= lang('current_year'); ?></a><br>
                        <a onclick="formulateInv(this)" class="text-tag text-info" data-tag="{{{month}}}"><?= lang('current_month'); ?></a><br>
                        <a onclick="formulateInv(this)" class="text-tag text-info" data-tag="{{{day}}}"><?= lang('current_day'); ?></a><br>
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
<script>

function formulateInv(dis){
    var curr_val = $("#invoice_group_identifier_format").val();
    var new_val = $(dis).attr("data-tag");
    $("#invoice_group_identifier_format").val(curr_val+new_val);
}

</script>