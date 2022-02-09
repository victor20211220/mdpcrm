<?php
    if (isset($modal_user_client)) { echo $modal_user_client; }
?>

<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class=" font-thin h3"><?= lang('account_information'); ?></h1>
    </div>
    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">

                <?= $this->layout->load_view('layout/alerts'); ?>

                <div id="userInfo">

                    <fieldset>
                        <legend><?= lang('account_information'); ?></legend>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('name'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_name" id="company_name" class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_name')) {
                                           echo $this->Mdl_companies->form_value('company_name');
                                       } else {
                                           echo $_POST['company_name'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('company_address'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_address" id="company_address" class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_address')) {
                                           echo $this->Mdl_companies->form_value('company_address');
                                       } else {
                                           echo $_POST['company_address'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('company_code'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_code" id="company_code" class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_code')) {
                                           echo $this->Mdl_companies->form_value('company_code');
                                       } else {
                                           echo $_POST['company_code'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('company_vat'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_vatregnumber" id="company_vatregnumber"
                                       class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_vatregnumber')) {
                                           echo $this->Mdl_companies->form_value('company_vatregnumber');
                                       } else {
                                           echo $_POST['company_vatregnumber'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('company_iban'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_iban" id="company_iban" class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_iban')) {
                                           echo $this->Mdl_companies->form_value('company_iban');
                                       } else {
                                           echo $_POST['company_iban'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('company_bank_bic'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="company_bank_bic" id="company_bank_bic" class="form-control"
                                       value="<?php if ($this->Mdl_companies->form_value('company_bank_bic')) {
                                           echo $this->Mdl_companies->form_value('company_bank_bic');
                                       } else {
                                           echo $_POST['company_bank_bic'];
                                       } ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label">
                                    <?= lang('country'); ?>
                                </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <select name="company_country" id="company_country" class="form-control">
                                    <option></option>
                                    <?php foreach ($countries as $cldr => $country) : ?>
                                        <option value="<?= $cldr; ?>"
                                                <?php if ($selected_country == $cldr) { ?>selected="selected"<?php } ?>>
                                            <?= $country ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 row">
                                <ul class="nav nav-pills nav-sm">
                                    <?php $this->layout->load_view('layout/header_buttons'); ?>
                                </ul>
                            </div>
                        </div>
                    </fieldset>
                </div>

            </div>
        </div>
    </div>

</form>
