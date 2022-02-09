<script type="text/javascript">
    $(function() {
        $('#supplier_name').focus();
        $("#supplier_country").select2({
            allowClear : true
        });
    });
</script>

<script>
    function rekvizitaiAPI(searchBy, searchValue) {
        var name = '';
        var number = '';

        if (searchBy == 'name') {
            name = searchValue ? searchValue : $('#supplier_name').val();
        } else if (searchBy == 'number') {
            number = searchValue ? searchValue : $('#supplier_reg_number').val();
        } else {
            return;
        }

        if (
            (searchBy == 'name' && name.length == 0) ||
            (searchBy == 'number') && number.length == 0
        ) {
            return;
        }

        $.ajax({
            url: "/suppliers/ajax/api_search",
            type: "post",
            data: {
                name: name,
                number: number
            },
            success: function (data) {
                data = $.parseJSON(data);
                if (data) {
                    if (data.title && data.number && searchBy == 'name') {
                        return rekvizitaiAPI('number', data.number);
                    } else if (data.title && data.number) {
                        $("#supplier_name").val(data.title);
                        $('#supplier_reg_number').val(data.number);
                        $('#supplier_city').val(data.city);
                        $('#supplier_address_1').val(data.street + ' ' + data.houseNo);
                        $('#supplier_zip').val(data.postCode);
                        $('#supplier_email').val(data.email);
                        $('#supplier_phone').val(data.phone);
                        $('#supplier_mobile').val(data.mobile);
                        $('#supplier_fax').val(data.fax);
                        $('#supplier_web').val(data.website);
                        $('#supplier_vat_id').val(data.pvmCode);
                    } else {
                        alert('No results found');
                    }
                }
            },
            error: function () {
                alert("There seems to be an error in request");
            }
        });
    }
</script>

<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('suppliers'); ?></h1>
</div>


  <div class="">
   <div class="panel panel-default">
    <div class="panel-body">
    <form method="post"  role="form">

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden" value="<?= $this->Mdl_suppliers->form_value('is_update') ? 1 : 0; ?>"/>
         <legend><?= lang('personal_information'); ?></legend>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label><?= lang('active'); ?></label>
                    <label class="i-switch" style="position: relative; top: 5px; left: 5px;">
                        <input id="supplier_active" name="supplier_active" type="checkbox" value="1"
                            <?php
                            if ($this->Mdl_suppliers->form_value('supplier_active') == 1 or !is_numeric($this->Mdl_suppliers->form_value('supplier_active'))) {
                                echo 'checked';
                            }
                            ?>>
                        <i></i>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-5">

                <div class="form-group">
                    <label class="control-label">
                        <?= lang('supplier_name'); ?>:<i class="text-danger text-bold">*</i>
                    </label>
                    <div class="input-group">
                        <input id="supplier_name" name="supplier_name" type="text" class="form-control"
                               placeholder="<?= lang('supplier_name'); ?>"
                               value="<?= $this->Mdl_suppliers->form_value('supplier_name'); ?>"
                               style="border-bottom-right-radius: 0; border-top-right-radius: 0"
                        />
                        <span class="input-group-btn">
                            <span class="btn btn-default" type="button" onclick="rekvizitaiAPI('name', null)">search</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-5 col-sm-offset-1">
                <div class="form-group">
                    <label class="control-label"><?= lang('supplier_reg_number'); ?>: <i class="text-danger text-bold">*</i> </label>
                    <div class="input-group">
                        <input id="supplier_reg_number" name="supplier_reg_number" type="text" class="form-control"
                               placeholder="<?= lang('supplier_reg_number'); ?>"
                               value="<?= $this->Mdl_suppliers->form_value('supplier_reg_number'); ?>"
                               style="border-bottom-right-radius: 0; border-top-right-radius: 0"
                        />
                        <span class="input-group-btn">
                            <span class="btn btn-default" onclick="rekvizitaiAPI('number', null)">search</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
             <div class="col-xs-12 col-sm-5 ">
                <fieldset>
                    <legend><?= lang('address'); ?></legend>

                    <div class="form-group">
                        <label class="control-label"><?= lang('street_address'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_address_1" id="supplier_address_1" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_address_1'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('street_address_2'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_address_2" id="supplier_address_2" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_address_2'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('city'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_city" id="supplier_city" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_city'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('state'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_state" id="supplier_state" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_state'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('zip_code'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_zip" id="supplier_zip" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_zip'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('country'); ?>: </label>

                        <div class="">
                            <select name="supplier_country" id="supplier_country" class="form-control">
                                <option></option>
                                <?php foreach ($countries as $cldr => $country) { ?>
                                    <option value="<?= $cldr; ?>"
                                            <?php if ($selected_country == $cldr) { ?>selected="selected"<?php } ?>><?= $country ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-5 col-md-offset-1">
                <fieldset>

                    <legend><?= lang('contact_information'); ?></legend>

                    <div class="form-group">
                        <label class="control-label"><?= lang('phone_number'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_phone" id="supplier_phone" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_phone'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('fax_number'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_fax" id="supplier_fax" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_fax'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('mobile_number'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_mobile" id="supplier_mobile" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_mobile'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('email_address'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_email" id="supplier_email" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_email'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('web_address'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_web" id="supplier_web" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_web'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?= lang('tax_information'); ?></legend>

                    <div class="form-group">
                        <label class="control-label"><?= lang('vat_id'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_vat_id" id="supplier_vat_id" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_vat_id'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('tax_code'); ?>: </label>

                        <div class="">
                            <input type="text" name="supplier_tax_code" id="supplier_tax_code" class="form-control"
                                   value="<?= $this->Mdl_suppliers->form_value('supplier_tax_code'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?= lang('financial_details'); ?></legend>

                    <div class="form-group">
                        <label class="control-label"><?= lang('swift_code'); ?>: </label>

                        <div class="">
                                        <input type="text" class="form-control"
                                               name="supplier_swift"
                                               id="supplier_swift"
                                               value="<?= $this->Mdl_suppliers->form_value('supplier_swift'); ?>">
                                    </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= lang('iban_code'); ?>: </label>

                        <div class="">
                                        <input type="text" class="form-control"
                                               name="supplier_iban"
                                               id="supplier_iban"
                                               value="<?= $this->Mdl_suppliers->form_value('supplier_iban'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

        </div>

        <?php if ($custom_fields) { ?>
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                        <legend><?= lang('custom_fields'); ?></legend>
                        <?php foreach ($custom_fields as $custom_field) { ?>
                            <div class="form-group">
                                <label class="control-label"><?= $custom_field->custom_field_label; ?>: </label>

                                <div class="">
                                    <input type="text" class="form-control"
                                           name="custom[<?= $custom_field->custom_field_column; ?>]"
                                           id="<?= $custom_field->custom_field_column; ?>"
                                           value="<?= html_escape($this->Mdl_suppliers->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                                </div>
                                </div>
                            <?php } ?>
                            </fieldset>
                        </div>
                    </div>
                <?php } ?>
              </div>
              <?php $this->layout->load_view('layout/header_buttons'); ?>
             </form>
    </div>
  </div>
 </div>
