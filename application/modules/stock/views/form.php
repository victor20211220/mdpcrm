<link rel="stylesheet" href="/assets/responsive/css/jquery.bootstrap-touchspin.min.css" type="text/css"/>
<style>
    .bootstrap-touchspin .input-group-btn-vertical > .btn {
        height: 15.5px !important;
        margin-left: 0px !important;
        border-radius: 0px !important;
    }

    .bootstrap-touchspin .input-group-btn-vertical i {
        left: 2px !important;
        font-size: 8px !important;
    }
</style>

<script src="/assets/responsive/js/jquery.bootstrap-touchspin.min.js"></script>
<?php if ($read_only == 0) : ?>
    <script type="text/javascript">
        $(function () {

            $().ready(function () {
                $("[name='product_to_search']").select2({
                    createSearchChoice: function (term, data) {
                        if ($(data).filter(function () {
                                return this.text.localeCompare(term) === 0;
                            }).length === 0) {
                            return {id: term, text: term};
                        }
                    },
                    multiple: false,
                    allowClear: true,
                    placeholder: 'Search by product name ...',
                    data: [
                        <?php
                        $i = 0;
                        foreach ($products as $p) {
                            echo "{
                            id: '" . str_replace("'", "\'", $p->product_id) . "',
                            text: '" . str_replace("'", "\'", $p->product_name) . "',
                            stock: '" . str_replace("'", "\'", $p->stock) . "',
                            stock_alert: '" . str_replace("'", "\'", $p->stock_alert) . "',
                            family_name: '" . str_replace("'", "\'", $p->family_name) . "'
                            }";
                            if (($i + 1) != count($products)) {
                                echo ',';
                            }
                            $i++;
                        }
                        ?>
                    ]
                });

                $("[name='product_to_search']").on("select2-selecting", function (e) {
                    if (isNaN(e.choice.id)) return;

                    var tbody_class = 'item_stock_list_' + e.choice.id;
                    $('#stock_table_body_new_row').clone().appendTo('.stock_to_update_table').removeAttr('id').addClass(tbody_class).show().find('input[type=text]').filter(':first').focus();

                    $('.' + tbody_class + ' .nr_prod_id').val(e.choice.id);
                    $('.' + tbody_class + ' .nr_prod_id_display').html('#' + e.choice.id);
                    $('.' + tbody_class + ' .nr_prod_name').html(e.choice.text);
                    $('.' + tbody_class + ' .nr_prod_family').html(e.choice.family_name);
                    $('.' + tbody_class + ' .nr_prod_c_stock').html(e.choice.stock);
                    $('.' + tbody_class + ' .nr_prod_c_stock_a').html(e.choice.stock_alert);

                    $('.' + tbody_class + ' .nr_prod_old_stock').val(e.choice.stock);
                    $('.' + tbody_class + ' .nr_prod_old_stock_alert').val(e.choice.stock_alert);

                    $('.' + tbody_class + ' .nr_prod_new_stock').val(0);
                    $('.' + tbody_class + ' .nr_prod_new_stock_alert').val(e.choice.stock_alert);

                    $('.' + tbody_class + ' .nr_prod_new_stock').TouchSpin({
                        verticalbuttons: true,
                        min: -100
                    });
                    $('.' + tbody_class + ' .nr_prod_new_stock_alert').TouchSpin({
                        verticalbuttons: true,
                        min: -100
                    });

                    sdata = $("[name='product_to_search']").data('select2');

                    for (var i = sdata.opts.data.length - 1; i >= 0; i--) {
                        if (sdata.opts.data[i].id === e.choice.id) {
                            sdata.opts.data.splice(i, 1);
                        }
                    }

                    $("[name='product_to_search']").val('').attr("placeholder", "Search by product name ...");
                    $("#s2id_autogen2_search").val('');
                    $("[name='product_to_search']").select2('close');
                    //e.preventDefault()

                }).trigger('change');
            });
        });
    </script>

<?php endif; ?>

<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="font-thin h3"><?= lang('stock_management'); ?></h1>
    </div>


    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">

                <?= $this->layout->load_view('layout/alerts'); ?>

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="stock_update_name" class="control-label">
                            <?= lang('stock_update_name'); ?>        </label>
                        <input type="text" name="stock_update_name" class="input-sm form-control"
                               value="<?= $this->Mdl_stock->form_value('stock_update_name'); ?>"
                               <?php if ($read_only == 1){ ?>disabled="disabled"<?php } ?>
                        />
                    </div>
                    <div class="form-group">
                        <label for="stock_update_description">
                            <?= lang('stock_update_description'); ?>        </label>
                        <textarea <?php if ($read_only == 1){ ?>disabled="disabled"<?php } ?>
                                  name="stock_update_description" rows="3"
                                  class="input-sm form-control">
                            <?= $this->Mdl_stock->form_value('stock_update_description'); ?>
                        </textarea>
                    </div>

                    <?php if ($read_only == 0) : ?>
                        <div class="form-group">
                            <label for="stock_update_name" class="control-label">
                                <?= lang('stock_update_name'); ?>
                            </label>
                            <input type="text" name="product_to_search" class="input-sm form-control" value=""/>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">

                        <?php if ($read_only == 0) : ?>

                            <table class="table table-bordered table-condensed no-margin table-striped m-b-none dataTable no-footer stock_to_update_table">
                                <thead>
                                <th><?= lang('product_id'); ?>#</th>
                                <th><?= lang('product_name'); ?></th>
                                <th><?= lang('product_family_name'); ?></th>
                                <th><?= lang('product_stock'); ?></th>
                                <th><?= lang('product_stock_alert'); ?></th>
                                <th style='width: 150px'><?= lang('new_items'); ?></th>
                                <th style='width: 200px'><?= lang('product_new_s_a'); ?></th>
                                </thead>
                                <tbody id='stock_table_body_new_row' style="display: none;">
                                <input class='nr_prod_id' name='product_id[]' type='hidden'>
                                <input class='nr_prod_old_stock' name='nr_prod_old_stock[]' type='hidden'>
                                <input class='nr_prod_old_stock_alert' name='nr_prod_old_stock_alert[]' type='hidden'>
                                <td class='nr_prod_id_display'></td>
                                <td class='nr_prod_name'></td>
                                <td class='nr_prod_family'></td>
                                <td class='nr_prod_c_stock'></td>
                                <td class='nr_prod_c_stock_a'></td>
                                <td>
                                    <input type="text" name='nr_prod_new_stock[]' class='nr_prod_new_stock' value=''>
                                </td>
                                <td>
                                    <input type="text" name='nr_prod_new_stock_alert[]' class='nr_prod_new_stock_alert' value=''>
                                </td>
                                </tbody>
                            </table>

                        <?php else: ?>

                            <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                                   id="DataTables_Table_0"
                                   role="grid" aria-describedby="DataTables_Table_0_info"
                                   ui-options="{ aoColumns: [null, null, null, null, null, null, null] }">
                                <thead>
                                <th><?= lang('product_id'); ?>#</th>
                                <th><?= lang('product_name'); ?></th>
                                <th><?= lang('product_family_name'); ?></th>
                                <th><?= lang('product_stock'); ?></th>
                                <th><?= lang('product_stock_alert'); ?></th>
                                <th style='width: 150px'><?= lang('product_new_stock'); ?></th>
                                <th style='width: 200px'><?= lang('product_new_stock_alert'); ?></th>
                                </thead>
                                <tbody>
                                <?php $class = 'odd'; ?>
                                <?php foreach ($history_stock as $stock) : ?>
                                    <tr>
                                        <td class='nr_prod_id_display'># <?= $stock->product_id ?></td>
                                        <td class='nr_prod_name'><?= $stock->product_name ?></td>
                                        <td class='nr_prod_family'><?= $stock->family_name ?></td>
                                        <td class='nr_prod_c_stock'><?= $stock->old_stock ?></td>
                                        <td class='nr_prod_c_stock_a'><?= $stock->old_stock_alert ?></td>
                                        <td><?= $stock->new_stock ?></td>
                                        <td><?= $stock->new_stock_alert ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-6 row">
                            <div class=" bg-white">
                                <?php if ($read_only == 0) : ?>
                                    <ul class="nav nav-pills nav-sm row">
                                        <?php $this->layout->load_view('layout/header_buttons'); ?>
                                    </ul>
                                <?php else : ?>
                                    <ul class="nav nav-pills nav-sm row">
                                        <?php $this->layout->load_view('layout/back_button'); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
