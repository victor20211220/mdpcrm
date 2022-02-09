<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('custom_field_form'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-1 control-label" for="custom_field_table">
                        <?= lang('table'); ?>:
                    </label>

                    <div class="col-xs-12 col-sm-8 col-md-6">
                        <select name="custom_field_table" id="custom_field_table"
                                class="form-control">
                            <?php foreach ($custom_field_tables as $table => $label) : ?>
                            <?php
                                if (strlen($this->Mdl_custom_fields->form_value('custom_field_table')) > 0 && $this->Mdl_custom_fields->form_value('custom_field_table') != $table) {
                                    continue;
                                }
                                ?>
                                <option value="<?= $table; ?>"
                                    <?php if ($this->Mdl_custom_fields->form_value('custom_field_table') == $table) : ?>
                                        selected="selected"
                                    <?php endif; ?>>
                                    <?= lang($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-1 control-label"><?= lang('label'); ?>: </label>

                    <div class="col-xs-12 col-sm-8 col-md-6">
                        <input type="text" name="custom_field_label" id="custom_field_label" class="form-control"
                               value="<?= $this->Mdl_custom_fields->form_value('custom_field_label'); ?>"
                        />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-1 control-label"></label>
                    <div class="col-xs-12 col-sm-8 col-md-6 row">
                        <ul class="nav nav-pills nav-sm">
                            <?php $this->layout->load_view('layout/header_buttons'); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
