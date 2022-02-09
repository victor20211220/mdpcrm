<div class="tab-info">

    <div class="form-group">
        <label for="settings[merchant_enabled]" class="control-label">
            <?php echo lang('merchant_enable'); ?>
        </label>
        <select name="settings[merchant_enabled]" class="input-sm form-control merchant-enabled">
            <option value="0"><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->Mdl_settings->setting('merchant_enabled') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>

    <div id="merchant_fields" style="display:none" class="form-group">
        <label for="settings[merchant_driver]" class="control-label">
            <?php echo lang('merchant_driver'); ?>
        </label>
        <select name="settings[merchant_driver]" class="input-sm form-control merchant-driver">
            <option value=""></option>
            <option value="paysera" <?php if ($this->Mdl_settings->setting('merchant_driver') == 'paysera') { ?>selected="selected"<?php } ?>>Paysera</option>
            <?php foreach ($merchant_drivers as $merchant_driver) { ?>
                <option value="<?php echo $merchant_driver; ?>"
                        <?php if ($this->Mdl_settings->setting('merchant_driver') == $merchant_driver) { ?>selected="selected"<?php } ?>><?php echo ucwords(str_replace('_', ' ', $merchant_driver)); ?></option>
            <?php } ?>
        </select>
    </div>
<div id="paypal_fields" style="display: none" <?php if ($this->Mdl_settings->setting('merchant_driver') == 'paysera') { ?>style="display: none"<?php } ?>>
    <div class="form-group">
        <label for="settings[merchant_test_mode]" class="control-label">
            <?php echo lang('merchant_test_mode'); ?>
        </label>
        <select name="settings[merchant_test_mode]" class="input-sm form-control">
            <option value="0"><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->Mdl_settings->setting('merchant_test_mode') == 1) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[merchant_username]" class="control-label">
            <?php echo lang('username'); ?>
        </label>
        <input type="text" name="settings[merchant_username]" class="input-sm form-control"
               value="<?php echo $this->Mdl_settings->setting('merchant_username'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[merchant_password]" class="control-label">
            <?php echo lang('password'); ?>
        </label>
        <input type="password" name="settings[merchant_password]" class="input-sm form-control">
    </div>

    <div class="form-group">
        <label for="settings[merchant_signature]" class="control-label">
            <?php echo lang('merchant_signature'); ?>
        </label>
        <input type="text" name="settings[merchant_signature]" class="input-sm form-control"
               value="<?php echo $this->Mdl_settings->setting('merchant_signature'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[merchant_currency_code]" class="control-label">
            <?php echo lang('merchant_currency_code'); ?>
        </label>
        <select name="settings[merchant_currency_code]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($merchant_currency_codes as $val => $key) { ?>
                <option value="<?php echo $val; ?>"
                        <?php if ($this->Mdl_settings->setting('merchant_currency_code') == $val) { ?>selected="selected"<?php } ?>><?php echo $val; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[online_payment_method]" class="control-label">
            <?php echo lang('online_payment_method'); ?>
        </label>
        <select name="settings[online_payment_method]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($payment_methods as $payment_method) { ?>
                <option value="<?php echo $payment_method->payment_method_id; ?>"
                        <?php if ($this->Mdl_settings->setting('online_payment_method') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>><?php echo $payment_method->payment_method_name; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<div id="paysera_fields" style="display: none" <?php if ($this->Mdl_settings->setting('merchant_driver') == 'paypal_express') { ?>style="display: none"<?php } ?>>
    <div class="form-group">
        <label for="settings[merchant_driver_mode]" class="control-label">
            <?php echo lang('merchant_driver_mode'); ?>
        </label>
        <select name="settings[merchant_driver_mode]" class="input-sm form-control">
            <option value="0" <?php if ($this->Mdl_settings->setting('merchant_driver_mode') == '0') { ?>selected="selected"<?php } ?>>Live</option>
            <option value="1" <?php if ($this->Mdl_settings->setting('merchant_driver_mode') == '1') { ?>selected="selected"<?php } ?>>Testing</option>
        </select>
    </div>
    <div class="form-group">
        <label for="settings[merchant_currency_code]" class="control-label">
            <?php echo lang('merchant_currency_code'); ?>
        </label>
        <select name="settings[merchant_currency_code]" class="input-sm form-control">
            <option value=""></option>
            <?php foreach ($merchant_currency_codes as $val => $key) { ?>
                <option value="<?php echo $val; ?>"
                        <?php if ($this->Mdl_settings->setting('merchant_currency_code') == $val) { ?>selected="selected"<?php } ?>><?php echo $val; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="settings[paysera_project_id]" class="control-label">
            <?php echo lang('paysera_project_id'); ?>
        </label>
        <input type="text" name="settings[paysera_project_id]" class="input-sm form-control"
               value="<?php echo $this->Mdl_settings->setting('paysera_project_id'); ?>">
    </div>
    <div class="form-group">
        <label for="settings[paysera_sign_password]" class="control-label">
            <?php echo lang('paysera_sign_password'); ?>
        </label>
        <input type="text" name="settings[paysera_sign_password]" class="input-sm form-control"
               value="<?php echo $this->Mdl_settings->setting('paysera_sign_password'); ?>">
    </div>
</div>
</div>

<script type="text/javascript">
    $(function() {
        $('.merchant-driver').change(function() {
            var merchant_driver = $('.merchant-driver option:selected').val();

            if (merchant_driver == 'paysera') {
                $('#paypal_fields').css('display', 'none');
                $('#paysera_fields').css('display', 'inherit');
            } else if(merchant_driver == 'paypal_express') {
                $('#paypal_fields').css('display', 'inherit');
                $('#paysera_fields').css('display', 'none');
            }
        });

        $('.merchant-enabled').change(function() {
            var merchant_enabled = $('.merchant-enabled option:selected').val();

            if (merchant_enabled == '1') {
                $('#merchant_fields').css('display', 'inherit');
            } else if(merchant_enabled == '0') {
                $('#merchant_fields').css('display', 'none');
                $('#paysera_fields').css('display', 'none');
                $('#paypal_fields').css('display', 'none');
            }
        });
    });
</script>
