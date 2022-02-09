<script type="text/javascript">
    $(function () {
        $('#client_name').focus();
        $("#client_country").select2({
            allowClear: true
        });
    });
</script>


<script>
    function rekvizitaiAPI(searchBy, searchValue) {
        var name = '';
        var number = '';

        if (searchBy == 'name') {
            name = searchValue ? searchValue : $('#client_name').val();
        } else if (searchBy == 'number') {
            number = searchValue ? searchValue : $('#client_reg_number').val();
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
            url: "/clients/ajax/api_search",
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
                        $("#client_name").val(data.title);
                        $('#client_reg_number').val(data.number);
                        $('#client_city').val(data.city);
                        $('#client_address_1').val(data.street + ' ' + data.houseNo);
                        $('#client_zip').val(data.postCode);
                        $('#client_email').val(data.email);
                        $('#client_phone').val(data.phone);
                        $('#client_mobile').val(data.mobile);
                        $('#client_fax').val(data.fax);
                        $('#client_web').val(data.website);
                        $('#client_vat_id').val(data.pvmCode);
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
    <h1 class="m-n font-thin h3"><?= lang('clients'); ?></h1>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">

            <form method="post" role="form">

                <div id="content">

                    <?php $this->layout->load_view('layout/alerts'); ?>

                    <input class="hidden" name="is_update" type="hidden" value="<?= $this->Mdl_clients->form_value('is_update') ? 1 : 0; ?>">

                    <legend><?= lang('personal_information'); ?></legend>

                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label><?= lang('active_client'); ?></label>
                                <label class="i-switch" style="position: relative; top: 5px; left: 5px;">
                                    <input id="client_active" name="client_active" type="checkbox" value="1"
                                        <?php
                                        if ($this->Mdl_clients->form_value('client_active') == 1 or !is_numeric($this->Mdl_clients->form_value('client_active'))) {
                                            echo 'checked';
                                        }
                                        ?>
                                    >
                                    <i></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    <?= lang('client_name'); ?>:<i class="text-danger text-bold">*</i>
                                </label>

                                <div class="input-group">
                                    <input id="client_name" name="client_name" type="text" class="form-control"
                                           placeholder="<?= lang('client_name'); ?>"
                                           value="<?= $this->Mdl_clients->form_value('client_name'); ?>"
                                           style="border-bottom-right-radius: 0; border-top-right-radius: 0"
                                    />
                                    <span class="input-group-btn">
                                        <span class="btn btn-default" type="button" onclick="rekvizitaiAPI('name')">search</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    <?= lang('client_reg_number'); ?>:<i class="text-danger text-bold">*</i>
                                </label>
                                <div class="input-group">
                                    <input id="client_reg_number" name="client_reg_number" type="text" class="form-control"
                                           placeholder="<?= lang('client_reg_number'); ?>"
                                           value="<?= $this->Mdl_clients->form_value('client_reg_number'); ?>"
                                           style="border-bottom-right-radius: 0; border-top-right-radius: 0"
                                    />
                                    <span class="input-group-btn">
                                        <span class="btn btn-default" onclick="rekvizitaiAPI('number')">search</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-xs-12 col-sm-6 ">
                            <fieldset>
                                <legend><?= lang('address'); ?></legend>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('street_address'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_address_1" id="client_address_1"
                                               class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_address_1'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('street_address_2'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_address_2" id="client_address_2"
                                               class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_address_2'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('city'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_city" id="client_city" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_city'); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('state'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_state" id="client_state" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_state'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('zip_code'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_zip" id="client_zip" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_zip'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('country'); ?>: </label>

                                    <div class="">
                                        <select name="client_country" id="client_country" class="form-control">
                                            <option></option>
                                            <?php foreach ($countries as $cldr => $country) : ?>
                                                <option value="<?= $cldr; ?>"
                                                        <?php if ($selected_country == $cldr) { ?>selected="selected"<?php } ?>
                                                >
                                                    <?= $country ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <fieldset>

                                <legend><?= lang('contact_information'); ?></legend>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('phone_number'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_phone" id="client_phone" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_phone'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('fax_number'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_fax" id="client_fax" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_fax'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('mobile_number'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_mobile'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('email_address'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_email" id="client_email" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_email'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('web_address'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_web" id="client_web" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_web'); ?>"
                                        />
                                    </div>
                                </div>

                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-5">
                            <fieldset>

                                <legend><?= lang('tax_information'); ?></legend>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('vat_id'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_vat_id'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('tax_code'); ?>: </label>
                                    <div class="">
                                        <input type="text" name="client_tax_code" id="client_tax_code"
                                               class="form-control"
                                               value="<?= $this->Mdl_clients->form_value('client_tax_code'); ?>"
                                        />
                                    </div>
                                </div>

                            </fieldset>
                        </div>

                        <div class="col-xs-12 col-sm-5 col-sm-offset-1">
                            <fieldset>

                                <legend><?= lang('financial_details'); ?></legend>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('swift_code'); ?>: </label>
                                    <div class="">
                                        <input type="text" class="form-control"
                                               name="client_swift"
                                               id="client_swift"
                                               value="<?= $this->Mdl_clients->form_value('client_swift'); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label"><?= lang('iban_code'); ?>: </label>
                                    <div class="">
                                        <input type="text" class="form-control"
                                               name="client_iban"
                                               id="client_iban"
                                               value="<?= $this->Mdl_clients->form_value('client_iban'); ?>"
                                        />
                                    </div>
                                </div>

                            </fieldset>
                        </div>

                    </div>

                    <?php if ($custom_fields) : ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <fieldset>
                                    <legend><?= lang('custom_fields'); ?></legend>

                                    <?php foreach ($custom_fields as $custom_field) : ?>
                                        <div class="form-group">
                                            <label class="control-label">
                                                <?= $custom_field->custom_field_label; ?>:
                                            </label>
                                            <div class="">
                                                <input type="text" class="form-control"
                                                       name="custom[<?= $custom_field->custom_field_column; ?>]"
                                                       id="<?= $custom_field->custom_field_column; ?>"
                                                       value="<?= html_escape($this->Mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>"
                                                />
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </fieldset>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php $this->layout->load_view('layout/header_buttons'); ?>

            </form>
        </div>
    </div>
</div>
