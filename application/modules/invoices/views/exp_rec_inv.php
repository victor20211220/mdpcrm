<style>
    .ui-state-highlight {
        height: 25px;
        line-height: 1.2em;
    }

    .list-group-item {
        cursor: move;
        padding: 5px 15px;
    }

    .list-group-lg .list-group-item {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .table-responsive {
        padding-bottom: 0px;
    }

    .list-group-sp .list-group-item {
        margin-bottom: 0px;
    }

    .date-opt > .radio {
        display: inline
    }
</style>

<script>
    $(function () {

        function update_order() {
            var dataArTemp = [];
            $("#sortable li").each(function (i, el) {
                var p = $(el).attr('id');
                dataArTemp.push(p);
            });
            $("#new_world_order").val(dataArTemp.join(','));
        }

        //ensure visible state matches initially

        $('#additional_options').change(function () {

            $('.add_opts_div').toggle(this.checked);
            $('#xml_id').prop('disabled', $('#additional_options').is(':checked'));
            $('#zip_id').prop('disabled', $('#additional_options').is(':checked'));
            $('#rec_inv').prop('disabled', $('#additional_options').is(':checked'));

        });
        //ensure visible state matches initially

        $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            stop: function (event, ui) {
                update_order();
            }
        });

        $('.rem_this_col').click(function (e) {
            e.preventDefault();
            $(this).closest('li').fadeOut('slow', function () {
                $(this).remove();
                update_order();
            });

        });
        update_order();

    });
</script>

<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('export_received_invoices'); ?></h1>
</div>

<div class="panel panel-default">

    <div class="">
        <div class="panel-body">
            <div id="report_options">

                <div class="panel-body">
                    <?= $this->layout->load_view('layout/alerts'); ?>

                    <form method="post" target="_blank">
                        <div class='row'>
                            <div class='col-sm-12'>
                                <div class="form-group has-feedback">
                                    <?= lang('export_received_invoices_details'); ?>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-sm-4'>
                                <div class="form-group has-feedback">
                                    <label for="from_date">
                                        <?= lang('from_date'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="from_date" id="from_date" class="form-control datepicker" value="<?= (new DateTime())->format('m/d/Y'); ?>">
                                        <label for="from_date" class="input-group-btn">
                                            <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-4 col-sm-offset-2'>
                                <div class="form-group has-feedback">
                                    <label for="to_date">
                                        <?= lang('to_date'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="to_date" id="to_date" class="form-control datepicker" value="<?= (new DateTime())->format('m/d/Y');?>">
                                        <label for="to_date" class="input-group-btn">
                                            <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-sm-12'>
                                <div class="input-group date-opt">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label class="i-checks">
                                                <input type="radio" id="date-opt-1" class='date_select_radio' name="date_opt[]" value="1" checked>
                                                <i></i>
                                                <?= lang('imp_ext_d_5'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="radio">
                                            <label class="i-checks">
                                                <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="2">
                                                <i></i>
                                                <?= lang('imp_ext_d_4'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="radio">
                                            <label class="i-checks">
                                                <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="3">
                                                <i></i>
                                                <?= lang('imp_ext_d_1'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="radio">
                                            <label class="i-checks">
                                                <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="4">
                                                <i></i>
                                                <?= lang('imp_ext_d_2'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="radio">
                                            <label class="i-checks">
                                                <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="5">
                                                <i></i>
                                                <?= lang('imp_ext_d_3'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="form-group has-feedback hidden">
                            <label for="to_date">
                                <?= lang('export_type'); ?>
                            </label>

                            <div class="input-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" name="export_type" checked id='rec_inv' value="rec_inv">
                                        <i></i>
                                        <?= lang('export_rec_inv'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" name="btn_export"
                                   value="<?= lang('export_button'); ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .add_opts_div {
        display: none;
    }
</style>


