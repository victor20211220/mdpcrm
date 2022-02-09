<script type="text/javascript">
    $(function () {
        show_fields();
        $('#user_type').change(function () {
            show_fields();
        });

        function show_fields() {
            $('#administrator_fields').hide();
            $('#guest_fields').hide();
            user_type = $('#user_type').val();
            if (user_type == 1 || user_type == 0 || user_type == 3) {
                $('#administrator_fields').show();
            } else if (user_type == 2) {
                $('#guest_fields').show();
            }
        }

        $("#user_country").select2({allowClear: true});
    });
</script>

<?php if (isset($modal_user_client)) {
    echo $modal_user_client;
}
?>

<div class="lter wrapper-md">
    <h1 class=" font-thin h3"><?= lang('user_form'); ?></h1>
</div>
<div class=""> <?= $this->layout->load_view('layout/alerts'); ?>
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="active">
                <a href="#tab1" data-toggle="tab"><?= lang('user_form'); ?>
                    <span class="badge badge-sm m-l-xs"></span>
                </a>
            </li>
            <li>
                <a href="#tab2" data-toggle="tab"><?= lang('email_settings'); ?>
                    <span class="badge bg-danger badge-sm m-l-xs"></span>
                </a>
            </li>

        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <form method="post" class="form-horizontal">
                    <fieldset>
                        <legend><?= lang('account_information'); ?></legend>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label><?= lang('name'); ?>: </label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="user_name" id="user_name" class="form-control"
                                       value="<?= $this->Mdl_users->form_value('user_name'); ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('email_address'); ?></label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="user_email" id="user_email" class="form-control"
                                       value="<?= $this->Mdl_users->form_value('user_email'); ?>"
                                />
                            </div>
                        </div>
                        <?php if (!$id) { ?>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('password'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="password" name="user_password" id="user_password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('verify_password'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="password" name="user_passwordv" id="user_passwordv"
                                           class="form-control"
                                    />
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label><?= lang('change_password'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= anchor('users/change_password/' . $id, lang('change_password')); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                <label class="control-label"><?= lang('user_type'); ?></label>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <select name="user_type" id="user_type" class="form-control">
                                    <?php foreach ($user_types as $key => $type) : ?>
                                        <option value="<?= $key; ?>"
                                            <?php if ($this->Mdl_users->form_value('user_type') == $key) { ?>
                                                selected="selected"<?php } ?>
                                        >
                                            <?= $type; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <div id="administrator_fields">
                        <fieldset>
                            <legend><?= lang('address'); ?></legend>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('street_address'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="text" name="user_address_1" id="user_address_1" class="form-control"
                                           value="<?= $this->Mdl_users->form_value('user_address_1'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('city'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="text" name="user_city" id="user_city" class="form-control"
                                           value="<?= $this->Mdl_users->form_value('user_city'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('state'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="text" name="user_state" id="user_state" class="form-control"
                                           value="<?= $this->Mdl_users->form_value('user_state'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('zip_code'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <input type="text" name="user_zip" id="user_zip" class="form-control"
                                           value="<?= $this->Mdl_users->form_value('user_zip'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label"><?= lang('country'); ?> <?= $this->Mdl_clients->form_value('client_country'); ?></label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <select name="user_country" id="user_country" class="form-control">
                                        <?php foreach ($countries as $cldr => $country) { ?>
                                            <option value="<?= $cldr; ?>"
                                                    <?php if ($selected_country == $cldr) { ?>selected="selected"<?php } ?>>
                                                <?= $country ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        <!--  <fieldset>                    <legend><?= lang('tax_information'); ?></legend>                    <div class="form-group">                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">                            <label class="control-label">                                <?= lang('vat_id'); ?>                            </label>                        </div>                        <div class="col-xs-12 col-sm-6">                            <input type="text" name="user_vat_id" id="user_vat_id" class="form-control"                                   value="<?= $this->Mdl_users->form_value('user_vat_id'); ?>">                        </div>                    </div>                    <div class="form-group">                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">                            <label class="control-label">                                <?= lang('tax_code'); ?>                            </label>                        </div>                        <div class="col-xs-12 col-sm-6">                            <input type="text" name="user_tax_code" id="user_tax_code" class="form-control"                                   value="<?= $this->Mdl_users->form_value('user_tax_code'); ?>">                        </div>                    </div>                </fieldset>-->
                        <fieldset>
                            <legend><?= lang('contact_information'); ?></legend>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs"><label
                                            class="control-label">                                <?= lang('phone_number'); ?>                            </label>
                                </div>
                                <div class="col-xs-12 col-sm-6"><input type="text" name="user_phone" id="user_phone"
                                                                       class="form-control"
                                                                       value="<?= $this->Mdl_users->form_value('user_phone'); ?>">
                                </div>
                            </div>
                            <!--<div class="form-group">                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">                            <label class="control-label">                                <?php /*echo lang('fax_number'); */ ?>                            </label>                        </div>                        <div class="col-xs-12 col-sm-6">                            <input type="text" name="user_fax" id="user_fax" class="form-control"                                   value="<?php /*echo $this->Mdl_users->form_value('user_fax'); */ ?>">                        </div>                    </div>-->
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs"><label
                                            class="control-label">                                <?= lang('mobile_number'); ?>                            </label>
                                </div>
                                <div class="col-xs-12 col-sm-6"><input type="text" name="user_mobile" id="user_mobile"
                                                                       class="form-control"
                                                                       value="<?= $this->Mdl_users->form_value('user_mobile'); ?>">
                                </div>
                            </div>
                            <!--<div class="form-group">                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">                            <label class="control-label">                                <?php /*echo lang('web_address'); */ ?>                            </label>                        </div>                        <div class="col-xs-12 col-sm-6">                            <input type="text" name="user_web" id="user_web" class="form-control"                                   value="<?php /*echo $this->Mdl_users->form_value('user_web'); */ ?>">                        </div>                    </div>-->
                        </fieldset>
                        <?php
                        //echo "<pre>" . print_r($preloaded_access_resources, true) . "</pre>";
                        $access_resources_filled = $this->Mdl_users->form_value('access_resources');
                        $counter = 1;

                        $checked_boxes_array = [
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked",
                            "checked"
                        ];

                        if (is_array($this->Mdl_users->form_value('access_resources'))) {
                            for ($i = 1; $i <= 10; $i++) {
                                if (!in_array($i, $access_resources_filled)) {
                                    $checked_boxes_array[$i - 1] = '';
                                }
                            }
                        } elseif (isset($preloaded_access_resources)) {
                            $checked_boxes_array = ["", "", "", "", "", "", "", "", "", ""];
                            foreach ($preloaded_access_resources as $r) {
                                $checked_boxes_array[$r['access_resource_id'] - 1] = "checked";
                            }
                        }

                        ?>
                        <fieldset>
                            <legend><?= lang('access_resources'); ?></legend>
                            <div class="form-group">
                                <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                    <label class="control-label">
                                        <?= lang('resources'); ?>
                                    </label>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <!--                <input style="visibility:hidden" type="checkbox" name="access_resources[]" value="1" checked><br />-->
                                            <?php foreach ($access_resources as $access_resource):
                                                //                    if($counter == 1)
                                                //                    {
                                                //                        $counter++; continue;
                                                //                    }
                                                ?>
                                                <label class="i-checks">
                                                    <input type="checkbox" id="checkbox-3" class="checkbox11"
                                                           name="access_resources[]"
                                                           value="<?= $access_resource['access_resource_id']; ?>"
                                                        <?= $checked_boxes_array[$counter - 1]; /*if(in_array($counter, $access_resources_filled)) echo " checked";*/ ?>><i></i></label>
                                                <?= $access_resource['resource'];
                                                $counter++; ?><br/><br/>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend><?= lang('custom_fields'); ?></legend>
                            <?php foreach ($custom_fields as $custom_field) { ?>
                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                                        <label class="control-label">
                                            <?= $custom_field->custom_field_label; ?>
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <input type="text" class="form-control"
                                               name="custom[<?= $custom_field->custom_field_column; ?>]"
                                               id="<?= $custom_field->custom_field_column; ?>"
                                               value="<?= html_escape($this->Mdl_users->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset>
                    </div>
                    <div id="guest_fields">
                        <div id="open_invoices" class="widget">
                            <div class="widget-title">
                                <h5 style="float: left;"><?= lang('client_access'); ?></h5>
                                <div class="pull-right">
                                    <a href="#add-user-client" class="btn btn-default" data-toggle="modal"><i
                                                class="fa fa-plus"></i><?= lang('add_client'); ?></a>
                                </div>
                            </div>
                            <div id="div_user_client_table">
                                <?= $user_client_table; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs"></div>
                        <div class="col-xs-12 col-sm-6 row">
                            <ul class="nav nav-pills nav-sm"><?php $this->layout->load_view('layout/header_buttons'); ?>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane" id="tab2">
                <form method="post" action="<?= site_url('users/settings_update'); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_host'); ?></label>
                        <input type="text" class="form-control" name="host" <?php if ($settings['host']) {
                            echo 'value="' . $settings['host'] . '"';
                        } else {
                            echo 'placeholder="' . lang('email_host') . '"';
                        } ?> required/>
                    </div>
                    <input type="hidden" name="user_id" value="<?= $id; ?>"/>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_username'); ?></label>
                        <input type="text" class="form-control" name="username" <?php if ($settings['username']) {
                            echo 'value="' . $settings['username'] . '"';
                        } else {
                            echo 'placeholder="' . lang('email_username') . '"';
                        } ?> required/>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_password'); ?></label>
                        <input type="password" class="form-control" name="password" <?php if ($settings['password']) {
                            echo 'value="' . $settings['password'] . '"';
                        } else {
                            echo 'placeholder="' . lang('email_password') . '"';
                        } ?> required/>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_type'); ?></label>
                        <select name="type" class="form-control" required>
                            <option value="0" <?php if ($settings['type'] == 0) {
                                echo 'selected="selected"';
                            } ?>><?= lang('email_imap'); ?></option>
                            <option value="1" <?php if ($settings['type'] == 1) {
                                echo 'selected="selected"';
                            } ?>><?= lang('email_pop3'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_ssl'); ?></label>
                        <select name="ssl_status" class="form-control" required>
                            <option value="0" <?php if ($settings['ssl_status'] == 0) {
                                echo 'selected="selected"';
                            } ?>><?= lang('email_no'); ?></option>
                            <option value="1" <?php if ($settings['ssl_status'] == 1) {
                                echo 'selected="selected"';
                            } ?>><?= lang('email_yes'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_frequency'); ?></label>
                        <select name="frequency" class="form-control" required>
                            <option value="5" <?php if ($settings['frequency'] == 5) {
                                echo 'selected="selected"';
                            } ?>>5 <?= lang('email_minutes'); ?></option>
                            <option value="10" <?php if ($settings['frequency'] == 10) {
                                echo 'selected="selected"';
                            } ?>>10 <?= lang('email_minutes'); ?></option>
                            <option value="15" <?php if ($settings['frequency'] == 15) {
                                echo 'selected="selected"';
                            } ?>>15 <?= lang('email_minutes'); ?></option>
                            <option value="30" <?php if ($settings['frequency'] == 30) {
                                echo 'selected="selected"';
                            } ?>>30 <?= lang('email_minutes'); ?></option>
                            <option value="60" <?php if ($settings['frequency'] == 60) {
                                echo 'selected="selected"';
                            } ?>>60 <?= lang('email_minutes'); ?></option>
                        </select>
                    </div>
                    <div class="but-wrapper">
                        <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1">
                            Cancel
                        </button>
                        <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success" value="1">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
