<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?php echo lang('sales_by_date'); ?></h1>
</div>

<div class="">

    <div id="report_options" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-file-pdf-o"></i>
                <?php echo lang('report_options'); ?>
            </h3>
        </div>
        <div class="panel-body">
            <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" target="_blank">

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12 col-sm-3">
                            <label for="from_date">
                                <?php echo lang('from_date'); ?>
                            </label>
                            <div class="input-group">
                                <input name="from_date" id="from_date" class="form-control datepicker" value="<?php echo date('m/d/Y', time()); ?>" required>
                                <label for="from_date" class="input-group-btn">
                                    <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-3">
                            <label for="to_date">
                                <?php echo lang('to_date'); ?>
                            </label>

                            <div class="input-group">
                                <input name="to_date" id="to_date" class="form-control datepicker" value="<?php echo date('m/d/Y', time()); ?>" required>
                                <label for="to_date" class="input-group-btn">
                                    <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <div class='row'>
                    <div class='col-sm-12'>
                        <div class="input-group date-opt">
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" id="date-opt-1" class='date_select_radio' name="date_opt[]" value="1" checked>
                                        <i></i>
                                        <?php echo lang('imp_ext_d_5'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="2">
                                        <i></i>
                                        <?php echo lang('imp_ext_d_4'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="3">
                                        <i></i>
                                        <?php echo lang('imp_ext_d_1'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="4">
                                        <i></i>
                                        <?php echo lang('imp_ext_d_2'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="radio">
                                    <label class="i-checks">
                                        <input type="radio" id="date-opt-2" class='date_select_radio' name="date_opt[]" value="5">
                                        <i></i>
                                        <?php echo lang('imp_ext_d_3'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <br>

                <div class="clearfix">
                    <div class="col-xs-12 col-md-2" style="margin-right:10px; padding-left:0px;">
                        <label for="minQuantity">
                            <?php echo lang('min_quantity'); ?>
                        </label>

                        <div>
                            <input type="number" id="minQuantity" name="minQuantity" min="0" class="form-control">
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-2" style=padding-left:0px;>
                        <label for="maxQuantity">
                            <?php echo lang('max_quantity'); ?>
                        </label>

                        <div>
                            <input type="number" id="maxQuantity" name="maxQuantity" min="0" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label for="checkboxTax" class="i-checks">
                            <input type="checkbox" id="checkboxTax" class="checkbox11" name="checkboxTax"><i></i><?php echo lang('values_with_taxes'); ?>
                        </label>
                    </div>
                </div>

                <br>

                <input type="submit" class="btn btn-success" name="btn_submit" value="<?php echo lang('run_report'); ?>">

            </form>
        </div>
    </div>
</div>
</div>
</div>
