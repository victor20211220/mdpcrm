<style type="text/css">
    div.image-picker-custom {
        width: 150px;
        height: 200px !important;
    }

    div.image-picker-custom img {
        max-height: 175px !important;
    }

    div.image-picker-custom:hover {
        width: 220px;
        height: 260px !important;
    }

    div.image-picker-custom:hover img {
        max-height: 230px !important;
    }

    @-moz-document url-prefix() {
        input[type=file].form-control {
            padding-bottom: 35px !important;
        }

        input[type=file].input-sm {
            padding-bottom: 35px !important;
        }
    }
</style>

<div class="tab-info">

    <div class="form-group">
        <label for="settings[read_only_toggle]" class="control-label">
            <?= lang('set_to_read_only'); ?>
        </label>
        <select name="settings[read_only_toggle]" class="input-sm form-control">
            <option value="sent"
                <?=($this->Mdl_settings->setting('read_only_toggle') == 'sent' ? 'selected="selected"' : ''); ?>>
                <?= lang('sent'); ?>
            </option>
            <option value="viewed"
                <?=($this->Mdl_settings->setting('read_only_toggle') == 'viewed' ? 'selected="selected"' : ''); ?>>
                <?= lang('viewed'); ?>
            </option>
            <option value="paid"
                <?=($this->Mdl_settings->setting('read_only_toggle') == 'paid' ? 'selected="selected"' : ''); ?>>
                <?= lang('paid'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[invoices_due_after]" class="control-label">
            <?= lang('invoices_due_after'); ?>
        </label>
        <input type="text" name="settings[invoices_due_after]" class="input-sm form-control"
               value="<?= $this->Mdl_settings->setting('invoices_due_after'); ?>">
    </div>

    <div class="form-group">
        <label for="settings[default_invoice_terms]">
            <?= lang('default_terms'); ?>
        </label>
        <textarea name="settings[default_invoice_terms]" rows="3" class="input-sm form-control"><?= $this->Mdl_settings->setting('default_invoice_terms'); ?></textarea>
    </div>

    <div class="form-group">
        <label for="settings[invoice_default_payment_method]" class="control-label">
            <?= lang('default_payment_method'); ?>
        </label>
        <select name="settings[invoice_default_payment_method]" class="input-sm form-control">
            <option value=""></option>
            <?php
            $setting = $this->Mdl_settings->setting('invoice_default_payment_method');
            foreach ($payment_methods as $payment_method) {
                echo '<option value="' . $payment_method->payment_method_id . '"';
                if ($payment_method->payment_method_id == $setting) {
                    echo 'selected="selected"';
                }
                echo '>' . $payment_method->payment_method_name;
                echo '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[automatic_email_on_recur]" class="control-label">
            <?= lang('automatic_email_on_recur'); ?>
        </label>
        <select name="settings[automatic_email_on_recur]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->Mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?= lang('no'); ?>
            </option>
            <option value="1"
                    <?php if ($this->Mdl_settings->setting('automatic_email_on_recur')) { ?>selected="selected"<?php } ?>><?= lang('yes'); ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[mark_invoices_sent_pdf]" class="control-label">
            <?= lang('mark_invoices_sent_pdf'); ?>
        </label>
        <select name="settings[mark_invoices_sent_pdf]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->Mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?= lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->Mdl_settings->setting('mark_invoices_sent_pdf')) { ?>selected="selected"<?php } ?>><?= lang('yes'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[invoice_pre_password]" class="control-label">
            <?= lang('invoice_pre_password'); ?>
        </label>
        <input type="text" name="settings[invoice_pre_password]" class="input-sm form-control"
               value="<?= $this->Mdl_settings->setting('invoice_pre_password'); ?>">
    </div>

    <div class="form-group">
        <label class="control-label">
            <?= lang('invoice_logo'); ?>
        </label>
        <?php if ($this->Mdl_settings->setting('invoice_logo')) : ?>
            <img class="img img-responsive invoice_logo_custom" style="max-width: 300px"
                 src="<?= site_url('uploads/' . $this->Mdl_settings->setting('invoice_logo')); ?>" height="200">
            <br>
            <?= anchor('settings/remove_logo/invoice', 'Remove Logo'); ?><br>
        <?php endif; ?>
        <input type="file" name="invoice_logo" size="40" class="form-control input-sm"/>
    </div>

    <div class="form-group">
        <hr/>
        <h4><?= lang('invoice_template'); ?></h4>
    </div>

    <div class="form-group" style="height: 300px !important;">
        <label for="settings[pdf_invoice_template]" class="control-label">
            <?= lang('default_pdf_template'); ?>
        </label>

        <select style="80%" name="settings[pdf_invoice_template]" class="">

            <?php foreach ($pdf_invoice_templates as $invoice_template) { ?>
                <option data-img-class="image-picker-custom" data-img-src="<?= base_url(); ?>assets/responsive/pdf_temp/demo/<?= $invoice_template; ?>.jpg" value="<?= $invoice_template; ?>" <?php if ($this->Mdl_settings->setting('pdf_invoice_template') == $invoice_template) { ?>selected="selected"<?php } ?>><?= $invoice_template; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
