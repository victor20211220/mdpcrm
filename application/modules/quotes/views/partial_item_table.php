<style type="text/css">
    .tabletotal td {
        padding: 15px !important;
        text-align: left !important;
    }

    .tabletotal td:last-child {
        text-align: right !important;
    }
</style>
<style>
    .special-border {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }
    .special-right {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }
</style>
<div class="table-responsive">
    <table id="item_table" class="items table table-condensed table-bordered">
        <thead>
        <tr>
            <th style="width: 1% !important" width="1px"></th>
            <th style="width: 20%"><?= lang('item'); ?></th>
            <th style="width: 5%"><?= lang('quantity'); ?></th>
            <th style="width: 9%"><?= lang('price'); ?></th>
            <th style="width: 5%"><?= lang('discount'); ?></th>
            <th style="width: 10%"><?= lang('VAT'); ?></th>
            <th style="width: 10%"><?= lang('total_without_vat'); ?></th>
        </tr>
        </thead>
        <tbody id="new_row" style="display: none;">
        <tr>
            <td rowspan="2" class="td-icon" width="1%" style="width: 1% !important;">
                <i class="fa fa-arrows cursor-move blueheader"></i>
            </td>
            <td class="td-text">
                <input type="hidden" name="quote_id" value="<?= $quote_id; ?>">
                <input type="hidden" name="item_id" value="">
                <div class="form-group" id="the-basicz">
                    <input type="text" name="item_name" class="form-control tags_inputs" value="" placeholder="<?= lang('item'); ?>">
                </div>
                <a href="javascript:void(0);" class="add-description">
                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="tooltip" title="<?= lang('update_description'); ?>"></i>
                </a>
                <div class="form-group hidden">
                    <textarea name="item_description" class="form-control"></textarea>
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="form-group">
                    <input type="text" name="item_quantity" class="form-control" value="" placeholder="Qty">
                </div>
            </td>
            <td class="td-amount">
                <div class="form-group">
                    <input type="text" name="item_price" class="form-control" value="" placeholder="<?= lang('price'); ?>">
                </div>
            </td>
            <td class="td-amount ">
                <div class="input-group">
                    <input type="text"
                           style="border-top-right-radius:0px !important;border-bottom-right-radius: 0px !important;"
                           name="item_discount_percent" class="form-control amount item_discount_percent"
                           value="" data-toggle="tooltip" data-placement="bottom"
                           title="<?= lang('per_item'); ?>">
                    <input type='hidden' name="item_discount_amount"
                           id="item_discount_amount"
                           class="form-control amount"
                           value="">
                    <span class="input-group-addon" style="margin-left:-10px">%</span>
                </div>
            </td>
            <td class="td-amount">
                <select name="item_tax_rate_id" name="item_tax_rate_id" class="form-control">
                    <?php foreach ($tax_rates as $tax_rate) { ?>
                        <option value="<?= $tax_rate->tax_rate_id; ?>">
                            <?= $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
            <td class="td-icon text-right td-vert-middle" id="row-total"
                style="border-top-right-radius:0px !important;border-bottom-right-radius: 0px !important;">
                <span name="item_total" class="amount"><?= format_currency(0.00); ?></span>
                <a href="#" class="delete_itemz"
                   title="<?= lang('delete'); ?>" style="padding-left: 10px !important">
                    <i class="fa fa-trash-o text-danger"></i>
                </a>
            </td>
        </tr>
        <tr style="display: none;">
            <td class="td-textarea">
                <div class="form-group">
                    <textarea name="item_description" class="form-control"></textarea>
                </div>
            </td>
            <td colspan="2" class="td-amount td-vert-middle">
                <span><?= lang('subtotal'); ?></span><br/>
                <span name="subtotal" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?= lang('discount'); ?></span><br/>
                <span name="item_discount_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle">
                <span><?= lang('tax'); ?></span><br/>
                <span name="item_tax_total" class="amount"></span>
            </td>
            <td class="td-amount td-vert-middle" style="position: relative; ">
                <span><?= lang('total'); ?></span><br/>
                <span name="item_total" class="amount"></span>
            </td>
        </tr>
        </tbody>

        <?php foreach ($items as $item) { ?>
            <tbody class="item_list">
            <tr>
                <td rowspan="2" class="td-icon" style="width: 1% !important;"><i
                            class="fa fa-arrows cursor-move blueheader"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" value="<?= $quote_id; ?>">
                    <input type="hidden" name="item_id" value="<?= $item->item_id; ?>">
                    <div class="form-group" id="the-basicz">
                        <input type="text" name="item_name" class="form-control tags_inputs"
                               value="<?= html_escape($item->item_name); ?>"
                               placeholder="<?= lang('item'); ?>"
                        />
                    </div>
                    <a href="javascript:void(0);" class="add-description">
                        <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="tooltip" data-original-title="<?= lang('update_description'); ?>"></i>
                    </a>
                    <div class="form-group hidden">
                        <textarea name="item_description" class="form-control">
                            <?= $item->item_description; ?>
                        </textarea>
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="form-group">
                        <input type="text" name="item_quantity" class="form-control"
                               value="<?= format_amount($item->item_quantity, true, 8); ?>" placeholder="Qty">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <input type="text" name="item_price" class="form-control"
                               value="<?= format_amount($item->item_price, true, 8); ?>"
                               placeholder="<?= lang('price'); ?>"
                        />
                    </div>
                </td>
                <td class="td-amount ">
                    <div class="input-group">
                        <input type="text"
                               style="border-top-right-radius:0px !important;border-bottom-right-radius: 0px !important;"
                               name="item_discount_percent" class="form-control amount item_discount_percent"
                               value="<?= format_amount($item->item_discount_amount, true); ?>" data-toggle="tooltip"
                               data-placement="bottom"
                               title="<?= lang('per_item'); ?>">
                        <input type='hidden' name="item_discount_amount"
                               id="item_discount_amount"
                               class="input-sm form-control amount"
                               value="">
                        <span class="input-group-addon" style="margin-left:-10px">%</span>

                    </div>
                </td>
                <td class="td-amount">
                    <div class="form-group">
                        <select name="item_tax_rate_id" name="item_tax_rate_id"
                                class="form-control">
                            <option value="0"><?= lang('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?= $tax_rate->tax_rate_id; ?>"
                                        <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>>
                                    <?= $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon td-vert-middle" style="vertical-align: middle !important; text-align: right !important; padding-right: 15px;">
                    <span name="item_total" class="amount">
                        <?= format_currency($item->item_total - $item->item_tax_total, false); ?>
                    </span>
                    <a href="<?= site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>"
                       title="<?= lang('delete'); ?>" style="padding-left: 10px !important">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
            <tr style="display: none;">
                <td class="td-textarea">
                    <div class="form-group">
                        <textarea name="item_description" class="form-control">
                            <?= $item->item_description; ?>
                        </textarea>
                    </div>
                </td>
                <td colspan="2" class="td-amount td-vert-middle">
                    <span><?= lang('subtotal'); ?></span><br/>
                    <span name="subtotal" class="amount">
                        <?= format_currency($item->item_subtotal); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?= lang('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount ">
                        <?= format_currency($item->item_discount); ?>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?= lang('tax'); ?></span><br/>
                    <span name="item_tax_total" class="amount">
                        <?= format_currency($item->item_tax_total); ?><br>
                    </span>
                </td>
                <td class="td-amount td-vert-middle">
                </td>
            </tr>
            </tbody>
        <?php } ?>

    </table>
</div>
<div class="row" style="margin-top: 40px;">
    <div class="col-xs-12 col-md-8">
        <div class="btn-group">
            <a href="#" class="btn_add_row btn btn-gray padder-h" style="margin-right: 5px">
                <i class="fa fa-plus"></i>
                <?= lang('add_new_row'); ?>
            </a>
            <a href="#" class="btn btn-success ajax-loader padder-h" id="btn_save_quotez" style="margin-right: 5px">
                <i class="fa fa-floppy-o"></i>
                <?= lang('save'); ?>
            </a>
        </div>
        <br/><br/>
    </div>
    <div class="col-md-5 col-md-offset-7">
        <table class="table table-condensed text-right tabletotal" style="border-top: 2px solid limegreen; border-left:1px solid #eaeff0; border-right:1px solid #eaeff0">
            <tr>
                <td><?= lang('subtotal'); ?></td>
                <td class="amount invoice_subtotal_value"><?= format_currency($quote->quote_item_subtotal); ?></td>
            </tr>

            <tr>
                <td><?= lang('vat_id_short'); ?></td>
                <td class="amount invoice_tax_total_value_two"><?= format_currency($quote->quote_item_tax_total); ?></td>
            </tr>

            <tr>
                <td class="td-vert-middle" style="padding-top: 20px !important">
                    <?= lang('discount'); ?>
                </td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <input id="quote_discount_amount" name="invoice_discount_amount"
                                       style="width: 75px !important"
                                       class="discount-option form-control input-sm amount special-right"
                                       value="<?= ($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : ''); ?>"
                                />

                                <div class="input-group-addon" style="width:50px;">
                                    <?= $this->Mdl_settings->setting('currency_symbol'); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-md">
                            <input id="quote_discount_percent" name="invoice_discount_percent"
                                   style="width: 75px !important"
                                   value="<?=($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount special-right">

                            <div class="input-group-addon" style="width:50px;">&percnt;</div>
                        </div>
                    </div>

                </td>
            </tr>
            <tr style="background-color: #26a9df !important;">
                <td style="color: #fff">
                    <?= lang('total'); ?>
                </td>
                <td style="color: #fff" class="amount invoice_total_value"><?= format_currency($quote->quote_total); ?></td>
            </tr>
        </table>
    </div>
</div>
<style type="text/css">
    td.td-icon {
        width: 1% !important;
    }
</style>
<script type="text/javascript">
    $('#btn_save_quotez').add('#btn_save_quotez_2').click(function (e) {
        e.preventDefault();
        var items = [];
        var item_order = 1;
        $('table tbody.item_list').each(function () {
            var row = {};
            $(this).find('input,select,textarea').each(function () {
                if ($(this).is(':checkbox')) {
                    row[$(this).attr('name')] = $(this).is(':checked');
                } else {
                    row[$(this).attr('name')] = $(this).val();
                }
            });
            row['item_order'] = item_order;
            item_order++;
            items.push(row);
        });
        $.post("<?= site_url('quotes/ajax/save'); ?>", {
                quote_id: <?= $quote_id; ?>,
                quote_number: $('#quote_number').val(),
                quote_date_created: $('#quote_date_created').val(),
                quote_date_expires: $('#quote_date_expires').val(),
                quote_status_id: $('#quote_status_id').val(),
                quote_password: $('#quote_password').val(),
                items: JSON.stringify(items),
                quote_discount_amount: $('#quote_discount_amount').val(),
                quote_discount_percent: $('#quote_discount_percent').val(),
                notes: $('#notes').val(),
                custom: $('input[name^=custom]').serializeArray()
            },
            function (data) {
                var response = JSON.parse(data);
                console.log(response);
                if (response.success == '1') {
                    window.location = "<?= site_url('quotes/view'); ?>/" + <?= $quote_id; ?>;
                }
                else {
                    $('.control-group').removeClass('error');
                    for (var key in response.validation_errors) {
                        $('#' + key).parent().parent().addClass('error');
                    }
                }
            });
    });
</script>
<script src="/assets/responsive/js/myScriptsForAutocomplite.js?3"></script>
