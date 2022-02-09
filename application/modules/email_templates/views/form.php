<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('email_template_form'); ?></h1>
    </div>
    <div class="">

        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <input class="hidden" name="is_update" type="hidden"
                    <?php if ($this->Mdl_email_templates->form_value('is_update')) {
                        echo 'value="1"';
                    } else {
                        echo 'value="0"';
                    } ?>
                />

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_title" class="control-label"><?= lang('title'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_title" id="email_template_title"
                               value="<?= $this->Mdl_email_templates->form_value('email_template_title'); ?>"
                               class="form-control"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_type" class="control-label"><?= lang('type'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <div class="radio">

                                <label class="i-checks">
                                    <input type="radio" name="email_template_type" id="email_template_type_invoice"
                                           value="invoice">

                                    <i></i>

                                    <?= lang('invoice'); ?>
                                </label>
                                <label class="i-checks">
                                    <input type="radio" name="email_template_type" id="email_template_type_quote"
                                           value="quote">
                                    <i></i><?= lang('quote'); ?>
                                </label>

                            </div>
                        </div>

                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_from_name" class="control-label"><?= lang('from_name'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_from_name" id="email_template_from_name"
                               class="form-control taggable"
                               value="<?= $this->Mdl_email_templates->form_value('email_template_from_name'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_from_email" class="control-label">
                            <?= lang('from_email'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_from_email" id="email_template_from_email"
                               class="form-control taggable"
                               value="<?= $this->Mdl_email_templates->form_value('email_template_from_email'); ?>"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_cc" class="control-label">
                            <?= lang('cc'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_cc" id="email_template_cc" class="form-control taggable"
                               value="<?= $this->Mdl_email_templates->form_value('email_template_cc'); ?>"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_bcc" class="control-label"><?= lang('bcc'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_bcc" id="email_template_bcc"
                               class="form-control taggable"
                               value="<?= $this->Mdl_email_templates->form_value('email_template_bcc'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_subject" class="control-label"><?= lang('subject'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="email_template_subject" id="email_template_subject"
                               class="form-control taggable"
                               value="<?= html_escape($this->Mdl_email_templates->form_value('email_template_subject')); ?>">
                    </div>
                </div>

                <input type="hidden" name="email_template_pdf_template" value="<?= $selected_pdf_template; ?>"/>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="email_template_body">
                            <?= lang('body'); ?>:
                        </label>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-paragraph">
                        <i class="fa fa-paragraph"></i>
                    </span>
                            <span class="html-tag btn btn-default" data-tag-type="text-bold">
                        <i class="fa fa-bold"></i>
                    </span>
                            <span class="html-tag btn btn-default" data-tag-type="text-italic">
                        <i class="fa fa-italic"></i>
                    </span>
                        </div>
                        <div class="html-tags btn-group btn-group-sm">
                            <span class="html-tag btn btn-default" data-tag-type="text-h1">H1</span>
                            <span class="html-tag btn btn-default" data-tag-type="text-h2">H2</span>
                            <span class="html-tag btn btn-default" data-tag-type="text-h3">H3</span>
                            <span class="html-tag btn btn-default" data-tag-type="text-h4">H4</span>
                        </div>
                        <div class="html-tags btn-group btn-group-sm">
                    <span class="html-tag btn btn-default" data-tag-type="text-code">
                        <i class="fa fa-code"></i>
                    </span>
                            <span class="html-tag btn btn-default" data-tag-type="text-hr">
                        &lt;hr/&gt;
                    </span>
                            <span class="html-tag btn btn-default" data-tag-type="text-css">
                        CSS
                    </span>
                        </div>
                        <br/><br/>
                        <textarea name="email_template_body" id="email_template_body" style="height: 200px;"
                                  class="email-template-body form-control taggable">
                            <?= $this->Mdl_email_templates->form_value('email_template_body'); ?>
                        </textarea>
                    </div>
                </div>

                <div class="form-group">

                    <div class="col-xs-12 col-sm-2 text-right text-left-xs"></div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <?= lang('preview'); ?>
                                <span id="email-template-preview-reload" class="pull-right cursor-pointer">
                            <i class="fa fa-refresh"></i>
                        </span>
                            </div>
                            <div class="panel-body">
                                <iframe id="email-template-preview"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs"></div>
                    <div class="col-xs-12 col-sm-6">
                        <h4><?= lang('email_template_tags'); ?></h4>

                        <p><?= lang('email_template_tags_instructions'); ?></p><br/>

                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                            <strong><?= lang('client'); ?></strong><br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_name}}}">
                                <?= lang('client_name'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_address_1}}}">
                                <?= lang('client'); ?> <?= lang('address'); ?> 1
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_address_2}}}">
                                <?= lang('client'); ?> <?= lang('address'); ?> 2
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_city}}}">
                                <?= lang('client'); ?> <?= lang('city'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_state}}}">
                                <?= lang('client'); ?> <?= lang('state'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_zip}}}">
                                <?= lang('client'); ?> <?= lang('zip_code'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{client_country}}}">
                                <?= lang('client'); ?> <?= lang('country'); ?>
                            </a>
                            <br>

                            <?php foreach ($custom_fields['ip_client_custom'] as $custom) : ?>
                                <a href="#" class="text-tag  text-info"
                                   data-tag="{{{<?= $custom->custom_field_column; ?>}}}">
                                    <?= $custom->custom_field_label; ?>
                                </a><br>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                            <strong><?= lang('user'); ?></strong><br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_name}}}">
                                <?= lang('user'); ?> <?= lang('name'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_company}}}">
                                <?= lang('user'); ?> <?= lang('company'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_address_1}}}">
                                <?= lang('user'); ?> <?= lang('address'); ?> 1
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_address_2}}}">
                                <?= lang('user'); ?> <?= lang('address'); ?> 2
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_city}}}">
                                <?= lang('user'); ?> <?= lang('city'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_state}}}">
                                <?= lang('user'); ?> <?= lang('state'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_zip}}}">
                                <?= lang('user'); ?> <?= lang('zip_code'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_country}}}">
                                <?= lang('user'); ?> <?= lang('country'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_phone}}}">
                                <?= lang('user'); ?> <?= lang('phone'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_fax}}}">
                                <?= lang('user'); ?> <?= lang('fax'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_mobile}}}">
                                <?= lang('user'); ?> <?= lang('mobile'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_email}}}">
                                <?= lang('user'); ?> <?= lang('email'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{user_web}}}">
                                <?= lang('user'); ?> <?= lang('web_address'); ?>
                            </a>
                            <br>

                            <?php foreach ($custom_fields['ip_user_custom'] as $custom) : ?>
                                <a href="#" class="text-tag text-info"
                                   data-tag="{{{<?= $custom->custom_field_column; ?>}}}">
                                    <?= $custom->custom_field_label; ?>
                                </a><br>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 hidden-invoice">
                            <strong><?= lang('invoices'); ?></strong><br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_guest_url}}}">
                                <?= lang('invoice'); ?> <?= lang('guest_url'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_number}}}">
                                <?= lang('invoice'); ?> <?= lang('id'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_date_due}}}">
                                <?= lang('invoice'); ?> <?= lang('due_date'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_date_created}}}">
                                <?= lang('invoice'); ?> <?= lang('created'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_terms}}}">
                                <?= lang('invoice_terms'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_total}}}">
                                <?= lang('invoice'); ?> <?= lang('total'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_paid}}}">
                                <?= lang('invoice'); ?> <?= lang('total_paid'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_balance}}}">
                                <?= lang('invoice'); ?> <?= lang('balance'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_status}}}">
                                <?= lang('invoice'); ?> <?= lang('status'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{invoice_logo}}}">
                                <?= lang('invoice_logo'); ?>
                            </a>
                            <br>

                            <?php foreach ($custom_fields['ip_invoice_custom'] as $custom) : ?>
                                <a href="#" class="text-tag  text-info"
                                   data-tag="{{{<?= $custom->custom_field_column; ?>}}}">
                                    <?= $custom->custom_field_label; ?>
                                </a><br>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 hidden-quote">
                            <strong><?= lang('quotes'); ?></strong><br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{quote_total}}}">
                                <?= lang('quote'); ?> <?= lang('total'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{quote_date_created}}}">
                                <?= lang('quote_date'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{quote_date_expires}}}">
                                <?= lang('quote'); ?> <?= lang('expires'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{quote_number}}}">
                                <?= lang('quote'); ?> <?= lang('id'); ?>
                            </a>
                            <br>
                            <a href="#" class="text-tag  text-info" data-tag="{{{quote_guest_url}}}">
                                <?= lang('quote'); ?> <?= lang('guest_url'); ?>
                            </a>
                            <br>

                            <?php foreach ($custom_fields['ip_quote_custom'] as $custom) : ?>
                                <a href="#" class="text-tag  text-info"
                                   data-tag="{{{<?= $custom->custom_field_column; ?>}}}">
                                    <?= $custom->custom_field_label; ?>
                                </a><br>
                            <?php endforeach; ?>
                        </div>
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

<script type="text/javascript">
    $(function () {
        var email_template_type = "<?= $this->Mdl_email_templates->form_value('email_template_type'); ?>";
        var $email_template_type_options = $("[name=email_template_type]");

        $email_template_type_options.click(function () {
            $(".show").removeClass("show").parent("select").each(function () {
                this.options.selectedIndex = 0;
            });

            $(".hidden-" + $(this).val()).addClass("show");
        });

        if (email_template_type === "") {
            $email_template_type_options.first().click();
        } else {
            $email_template_type_options.each(function () {
                if ($(this).val() === email_template_type) {
                    $(this).click();
                }
            });
        }
    });
</script>
