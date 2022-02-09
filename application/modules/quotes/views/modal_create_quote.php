<script type="text/javascript">
    $(function () {
        // Display the create quote modal
        $('#create-quote').modal('show');

        $('#create-quote').on('shown', function () {
            $("#client_name").focus();
        });

        $().ready(function () {
            $("[name='client_name']").select2({
                createSearchChoice: function (term, data) {
                    if ($(data).filter(function () {
                            return this.text.localeCompare(term) === 0;
                        }).length === 0) {
                        return {id: term, text: term};
                    }
                },
                multiple: false,
                allowClear: true,
                data: [
                    <?php
                    $i = 0;
                    foreach ($clients as $client) {
                        $clientSelect = $client->client_id == $client_id ? 'true' : 'false';
                        echo "{
                            id: '" . str_replace("'", "\'", $client->client_id) . "',
                            text: '" . str_replace("'", "\'", $client->client_name) . "'
                        }";

                        if (($i + 1) != count($clients)) {
                            echo ',';
                        }

                        $i++;
                    }
                    ?>
                ]
            });

            <?php if ($client_id): ?>
            $("[name='client_name']").val(<?=$client_id; ?>);
            $("[name='client_name']").trigger('change');
            <?php endif; ?>

            $("#client_name").focus();
        });

        // Creates the quote
        $('#quote_create_confirm').click(function () {
            console.log('clicked');
            // Posts the data to validate and create the quote;
            // will create the new client if necessary
            $.post("<?= site_url('quotes/ajax/create'); ?>", {
                    client_name: $('#client_name').val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_password: $('#quote_password').val(),
                    user_id: '<?= $this->session->userdata('user_id'); ?>',
                    invoice_group_id: $('#invoice_group_id').val()
                },
                function (data) {
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful and quote was created
                        window.location = "<?= site_url('quotes/view'); ?>/" + response.quote_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });
</script>

<div id="create-quote" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"
     role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3 class="blueheader"><?= lang('create_quote'); ?></h3>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label for="client_name"><?= lang('client'); ?></label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                       autofocus="autofocus"
                    <?php if ($client_name) echo 'value="' . html_escape($client_name) . '"'; ?>>
            </div>

            <div class="form-group">
                <label for="quote_date_created">
                    <?= lang('quote_date'); ?>
                </label>
                <div class="input-group">
                    <input name="quote_date_created" id="quote_date_created" class="form-control datepicker" value="<?= date(date_format_setting()); ?>">
                    <label for="quote_date_created" class="input-group-btn">
                        <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="quote_password"><?= lang('quote_password'); ?></label>
                <input type="text" name="quote_password" id="quote_password" class="form-control"
                       value="<?php if ($this->Mdl_settings->setting('quote_pre_password') == '') {
                           echo '';
                       } else {
                           echo $this->Mdl_settings->setting('quote_pre_password');
                       } ?>" style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="invoice_group_id"><?= lang('invoice_group'); ?>: </label>

                <div class="controls">
                    <select name="invoice_group_id" id="invoice_group_id"
                            class="form-control">
                        <?php foreach ($invoice_groups as $invoice_group) { ?>
                            <option value="<?= $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->Mdl_settings->setting('default_quote_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php } ?>><?= $invoice_group->invoice_group_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
          <div class="pull-right">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                     <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder ajax-loader" id="quote_create_confirm" style="margin-right: 0;" type="button">
                  <?= lang('continue'); ?>
                </button>
            </div></div>
        </div>

    </form>

</div>
