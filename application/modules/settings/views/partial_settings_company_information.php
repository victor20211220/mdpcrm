<div class="tab-info">
<style type="text/css">
    .thumbnail{
        height: 150px !Important;
    }
</style>


    <div class="form-group">
        <label for="settings[company_name]" class="control-label">
            <?php echo lang('name'); ?>
        </label>
        <input type="text" name="company_name" id="company_name" class="input-sm form-control"
               value="<?php if ($company['company_name']) {echo $company['company_name'];} else {echo $_POST['company_name'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_address]" class="control-label">
            <?php echo lang('company_address'); ?>
        </label>
        <input type="text" name="company_address" id="company_address" class="form-control"
        value="<?php if ($company['company_address']) {echo $company['company_address'];} else {echo $_POST['company_address'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_code]" class="control-label">
            <?php echo lang('company_code'); ?>
        </label>
        <input type="text" name="company_code" id="company_code" class="form-control"
        value="<?php if ($company['company_code']) {echo $company['company_code'];} else {echo $_POST['company_code'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_vatregnumber]" class="control-label">
            <?php echo lang('company_vat'); ?>
        </label>
        <input type="text" name="company_vatregnumber" id="company_vatregnumber" class="form-control"
        value="<?php if ($company['company_vatregnumber']) {echo $company['company_vatregnumber'];} else {echo $_POST['company_vatregnumber'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_iban]" class="control-label">
            <?php echo lang('company_iban'); ?>
        </label>
        <input type="text" name="company_iban" id="company_iban" class="form-control"
        value="<?php if ($company['company_iban']) {echo $company['company_iban'];} else {echo $_POST['company_iban'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_bank_bic]" class="control-label">
            <?php echo lang('swift'); ?>
        </label>
        <input type="text" name="company_bank_bic" id="company_bank_bic" class="form-control"
        value="<?php if ($company['company_bank_bic']) {echo $company['company_bank_bic'];} else {echo $_POST['company_bank_bic'];} ?>">
    </div>

    <div class="form-group">
        <label for="settings[company_country]" class="control-label">
            <?php echo lang('country'); ?>
        </label>
        <select name="company_country" id="company_country" class="form-control">
            <option></option>
            <?php foreach ($countries as $cldr => $country) { ?>
                <option value="<?php echo $cldr; ?>"
                        <?php if ($company['company_country'] == $cldr) { ?>selected="selected"<?php } ?>><?php echo $country ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[company_url]" class="control-label">
            <?php echo lang('company_url'); ?>
        </label>
        <input type="text" name="company_url" id="company_url" class="form-control"
        value="<?php if ($company['company_url']) {echo $company['company_url'];} else {echo $_POST['company_url'];} ?>">
    </div>

</div>
