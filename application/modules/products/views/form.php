<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="m-n font-thin h3">
            <?= lang('create_product'); ?>
        </h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <fieldset>
                            <legend>
                                <?php if ($this->Mdl_products->form_value('product_id')) : ?>
                                    #<?= $this->Mdl_products->form_value('product_id'); ?>&nbsp;
                                    <?= $this->Mdl_products->form_value('product_name'); ?>
                                <?php else : ?>
                                    <?= lang('new_product'); ?>
                                <?php endif; ?>
                            </legend>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('family'); ?>: </label>
                                <div class="col-lg-6">
                                    <select name="family_id" id="family_id" class="form-control">
                                        <option value="0"><?= lang('select_family'); ?></option>
                                        <?php foreach ($families as $family) : ?>
                                            <option value="<?= $family->family_id; ?>"
                                                    <?php if ($this->Mdl_products->form_value('family_id') == $family->family_id) { ?>selected="selected"<?php } ?>
                                            >
                                                <?= $family->family_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">
                                    <?= lang('product_sku'); ?>:<i class="text-danger text-bold">*</i>
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" name="product_sku" id="product_sku" class="form-control"
                                           value="<?= $this->Mdl_products->form_value('product_sku'); ?>"
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('product_name'); ?>:<i
                                            class="text-danger text-bold">*</i> </label>
                                <div class="col-lg-6">
                                    <input type="text" name="product_name" id="product_name" class="form-control"
                                           value="<?= $this->Mdl_products->form_value('product_name'); ?>"
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('product_description'); ?>: </label>
                                <div class="col-lg-6">
                                    <textarea name="product_description" id="product_description" class="form-control"
                                              rows="3">
                                        <?= $this->Mdl_products->form_value('product_description'); ?>
                                    </textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('product_price'); ?>:<i
                                            class="text-danger text-bold">*</i> </label>
                                <div class="col-lg-6">
                                    <input type="text" name="product_price" id="product_price" class="form-control"
                                           value="<?= $this->Mdl_products->form_value('product_price'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('tax_rate'); ?>: </label>
                                <div class="col-lg-6">
                                    <select name="tax_rate_id" id="tax_rate_id" class="form-control">
                                        <option value="0"><?= lang('none'); ?></option>
                                        <?php foreach ($tax_rates as $tax_rate) : ?>
                                            <option value="<?= $tax_rate->tax_rate_id; ?>"
                                                <?php if ($this->Mdl_products->form_value('tax_rate_id') == $tax_rate->tax_rate_id) { ?> selected="selected" <?php } ?>
                                            >
                                                <?= $tax_rate->tax_rate_name
                                                . ' (' . format_amount($tax_rate->tax_rate_percent) . '%)'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">&nbsp;</label>
                                <div class="col-lg-6">
                                    <ul class="nav nav-pills nav-sm">
                                        <?php $this->layout->load_view('layout/header_buttons'); ?>
                                    </ul>
                                </div>
                            </div>

                        </fieldset>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <fieldset>
                            <legend><?= lang('extra_information'); ?></legend>

                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('purchase_price'); ?>: </label>
                                <div class="col-lg-6">
                                    <input type="text" name="purchase_price" id="purchase_price" class="form-control"
                                           value="<?= format_amount($this->Mdl_products->form_value('purchase_price')); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('stock_alert'); ?>: </label>
                                <div class="col-lg-6">
                                    <input type="text" name="stock_alert" id="stock_alert" class="form-control"
                                           value="<?= $this->Mdl_products->form_value('stock_alert'); ?>">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
