<style type="text/css">
    .tabletotal td {
        padding: 15px !important;
        text-align: left !important;
    }

    .tabletotal td:last-child {
        text-align: right !important;
    }

    .special-side {
        border-left: none !important;
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
            <th style="width:20%"><?= lang('item'); ?></th>
            <th style="width: 5%"><?= lang('quantity'); ?></th>
            <th style="width: 9%"><?= lang('price'); ?></th>
            <th style="width:5%"><?= lang('discount'); ?></th>
            <th style="width: 10%"><?= lang('VAT'); ?></th>
            <th style="width:10%"><?= lang('total_without_vat'); ?></th>
        </tr>
        </thead>


        <tbody id="new_row" style="display: none;">
        <tr>
            <td width="1%" style="width: 1% !important" class="td-icon">
                <i class="fa fa-arrows cursor-move blueheader"></i>
            </td>

            <td class="td-text">
                <input type="hidden" name="new_row_added" value="1">
                <input type="hidden" name="invoice_id" value="<?= $invoice_id; ?>">
                <input type="hidden" name="item_product_id" value="-1">
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="product_stock" value="0">

                <div class="form-group" id="the-basicz">
                    <input type="text" id="tags" name="item_name" class="form-control tags_inputs" value="" placeholder="Item">
                </div>

                <a href="javascript:void(0);" class="add-description">
                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="tooltip" title="<?= lang('update_description'); ?>"></i>
                </a>
                <div class="form-group hidden">
                    <textarea name="item_description" class="form-control"></textarea>
                </div>
            </td>

            <td class="td-amount td-quantity">
                <input type="text" name="item_quantity" class="form-control" value="" placeholder="Qty">
            </td>

            <td class="td-amount">
                <input type="text" name="item_price" class="form-control" value="" placeholder="Price">
            </td>

            <td class="td-amount ">
                <div class="input-group">
                    <input type="text"
                           style="border-top-right-radius:0px !important; border-bottom-right-radius: 0px !important; border-bottom-left-radius: 7px !important; border-top-left-radius: 7px !important;"
                           name="item_discount_percent" class="form-control amount item_discount_percent"
                           value="" data-toggle="tooltip" data-placement="bottom"
                           title="<?= lang('per_item'); ?>">
                    <input type='hidden' name="item_discount_amount"
                           id="item_discount_amount"
                           class="input-sm form-control amount"
                           value="">
                    <span class="input-group-addon special-side" style="margin-left:-10px">%</span>
                </div>
            </td>

            <td class="td-amount">
                <select name="item_tax_rate_id" name="item_tax_rate_id" class="form-control">
                    <?php foreach ($tax_rates as $tax_rate): ?>
                        <option value="<?= $tax_rate->tax_rate_id; ?>">
                            <?= $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td class="td-icon td-vert-middle" style="vertical-align: middle !important; text-align: right !important; padding-right: 15px;">
                <span name="item_total" class="amount">
                    <?= format_currency(0.00, false); ?>
                </span>
                <a class="delete_itemz" id="delete_itemz" href="#">
                    <i class="fa fa-trash-o text-danger"></i>
                </a>
            </td>
        </tr>

        <tr style="display: none;">

            <td></td>
            <td class="td-textarea"></td>
            <td colspan="2" class="td-amount td-vert-middle">
                <span><?= lang('subtotal'); ?></span><br/>
                <span name="subtotal" class="amount">
                    <?= format_currency(0.00); ?>
                </span>
            </td>

            <td class="td-amount td-vert-middle">
                <span><?= lang('discount'); ?></span><br/>
                <span name="item_discount_total" class="amount">
                    <?= format_currency(0.00); ?>
                </span>
            </td>

            <td class="td-amount td-vert-middle">
                <span><?= lang('tax'); ?></span><br/>
                <span name="item_tax_total" class="amount">
                    <?= format_currency(0.00); ?>
                </span>
            </td>

            <td class="td-amount td-vert-middle"></td>
        </tr>
        </tbody>


        <?php foreach ($items as $item) : ?>
            <tbody class="item_list">
            <tr>
                <td style="width: 1%" class="td-icon">
                    <i class="fa fa-arrows cursor-move blueheader"></i>
                </td>

                <td class="td-text">
                    <input type="hidden" name="new_row_added" value="0">
                    <input type="hidden" name="invoice_id" value="<?= $invoice_id; ?>">
                    <input type="hidden" name="item_product_id" value="<?= $item->item_product_id; ?>">
                    <input type="hidden" name="product_stock" value="<?= $item->product_stock; ?>">
                    <input type="hidden" name="item_id"
                           value="<?= $item->item_id; ?>" <?= $invoice->is_read_only == 1 ? 'disabled=""' : null; ?>
                    />

                    <div class="form-group" id="the-basicz">
                        <input type="text" name="item_name" class="form-control tags_inputs" id="tags"
                               value="<?= html_escape($item->item_name); ?>"
                               placeholder="<?= lang('item_name'); ?>" <?= $invoice->is_read_only == 1 ? 'disabled=""' : null; ?>
                        />
                    </div>

                    <a href="javascript:void(0);" class="add-description"><i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="tooltip" title="<?= lang( 'update_description' ); ?>"></i></a>
                    <div class="form-group hidden">
                        <textarea name="item_description" class="form-control" <?php if ($invoice->is_read_only == 1) echo 'disabled="disabled"';?>><?= $item->item_description; ?></textarea>
                    </div>
                </td>

                <td class="td-amount td-quantity">
                    <input type="text" name="item_quantity" class="form-control"
                           value="<?= format_amount($item->item_quantity, true, 8); ?>"
                           placeholder="Qty"
                        <?= $invoice->is_read_onyly ? 'disabled="disabled"' : null; ?>
                    />
                </td>

                <td class="td-amount">
                    <input type="text" name="item_price" class="form-control"
                           value="<?= format_amount($item->item_price, true, 8); ?>"
                        <?= $invoice->is_read_onyly ? 'disabled="disabled"' : null; ?>
                    />
                </td>

                <td class="td-amount ">
                    <div class="input-group">
                        <input type="text" name='item_discount_percent'
                               class="form-control amount item_discount_percent"
                               value="<?= format_amount_int($item->item_discount_percent); ?>"
                               data-toggle="tooltip" data-placement="bottom"
                               title="<?= lang('per_item'); ?>"
                               style="border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;"
                               <?= $invoice->is_read_onyly ? 'disabled="disabled"' : null; ?>
                        />

                        <input type='hidden' name="item_discount_amount"
                               id="item_discount_amount"
                               class="input-sm form-control amount"
                               value="<?= format_amount($item->item_discount_amount); ?>"
                        />

                        <span class="input-group-addon special-side">%</span>
                    </div>
                </td>

                <td class="td-amount">
                    <div class="form-group">
                        <select name="item_tax_rate_id"
                                class="form-control"
                                <?= $invoice->is_read_onyly ? 'disabled="disabled"' : null; ?>
                        >
                            <option value="0"><?= lang('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) : ?>
                                <option value="<?= $tax_rate->tax_rate_id; ?>"
                                        <?= $item->item_tax_rate_id == $tax_rate->tax_rate_id ? 'selected' : null; ?>>
                                    <?= $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </td>

                <td class="td-icon td-vert-middle" style="position: relative; vertical-align: middle !important; text-align: right !important; padding-right: 15px;">
                    <span name="item_total" class="amount">
                        <?= format_currency($item->item_total - $item->item_tax_total, false); ?>
                    </span>
                    <?php if ($invoice->is_read_only != 1): ?>
                        <a href="<?= site_url('invoices/delete_item/' . $invoice->invoice_id . '/' . $item->item_id); ?>"
                           title="<?= lang('delete'); ?>" style="padding-left: 10px !important">
                            <i class="fa fa-trash-o text-danger"></i>
                        </a>
                    <?php endif; ?>
                </td>

            </tr>

            <tr style="display: none;">
                <td></td>
                <td class="td-textarea"></td>

                <td colspan="2" class="td-amount td-vert-middle">
                    <span><?= lang('subtotal'); ?></span><br/>
                    <span name="subtotal" class="amount">
                        <?= format_currency($item->item_subtotal); ?>
                    </span>
                </td>

                <td class="td-amount td-vert-middle">
                    <span><?= lang('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount">
                        <?= format_currency($item->item_discount); ?>
                    </span>
                </td>

                <td class="td-amount td-vert-middle">
                    <span><?= lang('tax'); ?></span><br/>
                    <span name="item_tax_total" class="amount">
                        <?= format_currency($item->item_tax_total); ?>
                    </span>
                </td>
            </tr>

            </tbody>

        <?php endforeach; ?>

        <tfoot>

        </tfoot>
    </table>

</div>


<div class="row">
    <div class="col-xs-12 col-md-8">
        <ul class="nav nav-pills nav-sm">
            <?php if ($invoice_is_recieved == false) : ?>
                <li>
                    <ul class="dropdown-menu">

                        <?php if ($invoice->is_read_only != 1 && 1 == 2) : ?>
                            <li>
                                <a href="#add-invoice-tax" data-toggle="modal">
                                    <i class="fa fa-plus fa-margin"></i> <?= lang('add_invoice_tax'); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="#" id="btn_create_credit_2" data-invoice-id="<?= $invoice_id; ?>">
                                <i class="fa fa-minus fa-margin"></i> <?= lang('create_credit_invoice'); ?>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="invoice-add-payment"
                               data-invoice-id="<?= $invoice_id; ?>"
                               data-invoice-balance="<?= $invoice->invoice_balance; ?>"
                               data-invoice-payment-method="<?= $invoice->payment_method; ?>"
                            >
                                <i class="fa fa-credit-card fa-margin"></i>
                                <?= lang('enter_payment'); ?>
                            </a>
                        </li>

                        <?php if (count($items) > 0) : ?>
                            <li>
                                <a href="#" id="btn_generate_pdf_2" data-invoice-id="<?= $invoice_id; ?>">
                                    <i class="fa fa-file-pdf-o fa-margin"></i>
                                    <?= lang('download_pdf'); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="<?= site_url('mailer/invoice/' . $invoice->invoice_id); ?>">
                                <i class="fa fa-send fa-margin"></i>
                                <?= lang('send_email'); ?>
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <a href="#" id="btn_create_recurring_2"
                               data-invoice-id="<?= $invoice_id; ?>">
                                <i class="fa fa-repeat fa-margin"></i>
                                <?= lang('create_recurring'); ?>
                            </a>
                        </li>

                        <li>
                            <a href="#" id="btn_copy_invoice_2"
                               data-invoice-id="<?= $invoice_id; ?>">
                                <i class="fa fa-copy fa-margin"></i>
                                <?= lang('copy_invoice'); ?>
                            </a>
                        </li>

                        <?php if (
                                $invoice->invoice_status_id == 1 ||
                                ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)
                        ) : ?>
                            <li>
                                <a href="#delete-invoice" data-toggle="modal">
                                    <i class="fa fa-trash-o fa-margin"></i>
                                    <?= lang('delete'); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>

            <li>
                <?php if ($invoice->is_read_only != 1) { ?>
            <li>
                <a href="#" class="btn_add_row btn btn-empty">
                    <i class="fa fa-plus"></i> <?= lang('add_new_row'); ?>
                </a>
            </li>
        <?php } ?>
            <?php if ($invoice->is_read_only != 1) { ?>
            </li>

            <?php if (count($items) > 0) { ?>
                <li>
                    <a href="#" class="btn_view_pdf btn btn-sm btn-default">
                        <i class="fa fa-file-pdf-o fa-margin"></i>
                        <?= lang('download_pdf'); ?>
                    </a>
                </li>
            <?php } ?>

            <li>
                <?php }

                if ($invoice->is_read_only != 1) : ?>
                    <a href="#" class="btn btn-sm btn-success ajax-loader<?= $saved ? ' saved' : ''; ?>"
                       id="btn_save_invoice_2">
                        <i class="fa fa-floppy-o"></i> <?= $saved ? lang('saved') : lang('save'); ?>
                    </a>
                <?php endif; ?>
            </li>

            <li class='invoice-labels'>
                <?php if ($invoice->invoice_is_recurring) : ?>
                    <span class="label label-info"><?= lang('recurring'); ?></span>
                <?php endif; ?>
            </li>

            <li class='invoice-labels'>
                <?php if ($invoice->is_read_only == 1) : ?>
                <span class="label label-danger">
                    <i class="fa fa-read-only"></i> <?= lang('read_only'); ?>
                </span>
                <?php endif; ?>
            </li>
        </ul>
        <br/><br/>
    </div>


    <div class="col-md-5 col-md-offset-7">
        <table class="table table-condensed text-right tabletotal" style="border:1px solid #eaeff0">
            <tr>
                <td style="width: 40%;">
                    <?= lang('subtotal'); ?>
                </td>
                <td style="width: 60%;"
                    class="amount invoice_subtotal_value">
                    <?= format_currency($invoice->invoice_item_subtotal); ?>
                </td>

            </tr>

            <tr>
                <td>
                    <?= lang('vat_id_short'); ?>
                </td>
                <td>
                    <?= $invoice_tax_rates ?
                        '<span class="invoice_tax_total_value_two">' . format_currency($invoice_tax_rates) . '</span>' :
                        '<span class="invoice_tax_total_value_two">' . format_currency('0') . '</span>';
                    ?>
                </td>

            </tr>

            <tr>
                <td class="td-vert-middle" style="padding-top: 20px !important">
                    <?= lang('discount'); ?>
                </td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">

                                <input id="invoice_discount_amount" name="invoice_discount_amount"
                                       style="width: 50px !important"
                                       class="discount-option form-control input-sm amount special-right"
                                       value="<?= $invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : ''; ?>"
                                       <?= ($invoice->is_read_only == 1) ? 'disabled="disabled"' : null; ?>
                                >

                                <div class="input-group-addon" style="width:50px;">
                                    <?= $this->Mdl_settings->setting('currency_symbol'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-md">
                            <input id="invoice_discount_percent" name="invoice_discount_percent"
                                   style="width: 50px !important"
                                   value="<?= ($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount special-right"
                                   <?= ($invoice->is_read_only == 1) ? 'disabled="disabled"' : null ?>
                            >
                            <div class="input-group-addon" style="width:50px;">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr style="background-color: #26a9df !important;">
                <td style="color: #fff;">
                    <?= lang('total'); ?>
                </td>
                <td style="color: #fff;" class="amount invoice_total_value">
                    <?= format_currency($invoice->invoice_total); ?>
                </td>
            </tr>

            <tr style="background-color: #f6f8f9;<?= $new !== false ? ' display: none;' : null; ?>">
                <td>
                    <?= lang('paid'); ?>
                </td>
                <td class="amount">
                    <?= format_currency($invoice->invoice_paid); ?>
                </td>
            </tr>

            <tr style="background-color: #26a9df !important;<?= $new !== false ? ' display: none;' : null; ?>">
                <td>
                    <span style="color: #fff;">
                        <?= lang('balance'); ?>
                    </span>
                </td>
                <td class="amount balance_total_value">
                    <span style="color: #fff;">
                        <?= format_currency(($invoice->invoice_balance) * -1); ?>
                    </span>
                </td>
            </tr>

        </table>


	</div>

</div>
<script>

$("#item_table").on('mouseover','.fa-pencil-square-o',function () {
    $("[data-toggle=tooltip]").tooltip();
});
</script>

<script src="/assets/responsive/js/myScriptsForAutocomplite.js?3"></script>
