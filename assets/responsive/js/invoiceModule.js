$(document).ready(function () {
    setVatTrigger();
});

function setVatTrigger() {
    $('select[name=item_tax_rate_id]').each(function() {
        $(this).removeAttr('id');
        $(this).uniqueId();
    });

    $(document).keydown(function () {
        if (event.keyCode == 9 || event.keyCode == 13) {
            if ($('select[name=item_tax_rate_id]').last().attr('id') == $(document.activeElement).attr('id')) {
                event.preventDefault();
                $('.btn_add_row').trigger('click');
                setVatTrigger();
                calculate_amounts(false);
            }
        }
    });

    $(document).keyup(function () {
        if (event.keyCode == 9) {
            var namesArray = [
                'item_name',
                'item_quantity',
                'item_price',
                'item_discount_percent'
            ];

            if ($.inArray(document.activeElement.name, namesArray) > 0) {
                if ($(document.activeElement).closest('tr').find('input[name=new_row_added]').val() != '0') {
                    $(document.activeElement).val('');
                }
            }
        }
    });
}

function calculate_amounts(checkStockQuantity) {
    $("#changedetect").val(1);
    var items_total_subtotal = 0;
    var items_total_subtotal_with_tax = 0;
    var items_total_tax = 0;
    var items_total_discounts = 0;

    $('table tbody.item_list').each(function () {
        var priceIsFloat = false;
        var quantityIsFloat = false;

        // console.log(this);
        
        var itemPrice = parseFloat($(this).find('input[name=item_price]').val().replace(',', '.')).toFixed(0);
        var itemPriceCheck = parseFloat($(this).find('input[name=item_price]').val().replace(',', '.')).toFixed(8);
        var item_quantity = parseFloat($(this).find('input[name=item_quantity]').val().replace(',', '.')).toFixed(0);
        var itemQuantityCheck = parseFloat($(this).find('input[name=item_quantity]').val().replace(',', '.')).toFixed(8);
        var itemQuantityStock = parseInt($(this).find('input[name=product_stock]').val());
        var discount = parseInt($(this).find('input[name=item_discount_percent]').val());

        if (isNaN(itemPrice)) {
            itemPrice = 0;
            itemPriceCheck = 0;
        }

        if (itemPrice - itemPriceCheck != 0) {
            priceIsFloat = true;
            itemPrice = itemPriceCheck;
        }

        if (isNaN(parseInt(item_quantity))) {
            item_quantity = 1;
        } else {
            if (item_quantity - itemQuantityCheck != 0) {
                quantityIsFloat = true;
                item_quantity = itemQuantityCheck;
            }
        }

        if (
            checkStockQuantity === true &&
            itemQuantityStock > 0 &&
            item_quantity > itemQuantityStock
        ) {
            checkStockQuantity = false;
            var msg = "Amount entered exceeds number of products in stock (" + itemQuantityStock + "). Proceed?";
            if (confirm(msg) === false) {
                item_quantity = 1;
            }
        }

        if (isNaN(discount) || discount > 100 || discount < 0) {
            var disc_percent = 0;
        } else {
            var disc_percent = parseInt(discount);
        }

        if (isNaN(itemPrice) || itemPrice == 0) {
            $(this).find('input[name=item_price]').val('--,--');
        } else {
            $(this).find('input[name=item_price]').val(priceIsFloat == true ? itemPrice.toString().rtrim('0') : itemPrice.toString());
        }

        $(this).find('input[name=item_quantity]').val(quantityIsFloat == true ? item_quantity.toString().rtrim('0') : item_quantity.toString());
        $(this).find('input[name=item_discount_percent]').val(disc_percent);

        var disc_amount = (itemPrice / 100 * disc_percent).toFixed(2);

        $(this).find('input[name=item_discount_amount]').val(disc_amount);


        var total_disc_text = $(this).find('span[name=item_discount_total]').html().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '')

        $(this).find('span[name=item_discount_total]').html(total_disc_text + '' + (disc_amount * item_quantity).toFixed(2));


        //update item subtotals and totals
        var item_discount_amount = parseFloat($(this).find('input[name=item_discount_amount]').val().replace(',', '.'));
        var item_tax_rate = parseFloat(item_tax_rates_preloaded[$(this).find('select[name=item_tax_rate_id]').val()]);


        //item subtotal

        var item_subtotal_value = parseFloat(item_quantity * (parseFloat(itemPrice) - parseFloat(item_discount_amount))).toFixed(2);
        var item_subtotal_value_raw = parseFloat(item_quantity * parseFloat(itemPrice)).toFixed(2);
        var item_subtotal_text = $(this).find('span[name=subtotal]').html().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');

        $(this).find('span[name=subtotal]').html(item_subtotal_text + '' + item_subtotal_value_raw);


        //item tax

        var item_tax_value = (item_subtotal_value * item_tax_rate / 100).toFixed(2);
        var item_tax_text = $(this).find('span[name=item_tax_total]').text().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');

        $(this).find('span[name=item_tax_total]').text(item_tax_text + '' + item_tax_value);

        var item_total_value = item_subtotal_value;

        var item_total_with_vate = parseFloat(parseFloat(item_total_value)+parseFloat(item_tax_value)).toFixed(2);
        var vate_amount = item_tax_value;

        if (isNaN(item_total_value) || item_total_value == 0) {
            item_total_value = '--,--';
            item_total_with_vate = '--,--';
            vate_amount = '--,--';
        }

        var item_total_text = $(this).find('span[name=item_total]').text().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');

        $(this).find('span[name=item_total_with_vate]').text(item_total_with_vate);
        $(this).find('span[name=item_total_with_vate_new]').text(item_total_with_vate);
        $(this).find('span[name=vate_amount]').text(vate_amount);
        $(this).find('span[name=vate_amount_new]').text(vate_amount);
        $(this).find('span[name=item_total]').text(item_total_value);
        $(this).find('span[name=item_total_new]').text(item_total_value);

        items_total_subtotal += parseFloat(item_subtotal_value);
        items_total_subtotal_with_tax += parseFloat(item_total_with_vate);
        items_total_tax += parseFloat(item_tax_value);
        items_total_discounts += parseFloat(disc_amount * item_quantity);
    });

    items_total_subtotal = Math.round(items_total_subtotal * 100) / 100;
    items_total_tax = Math.round(items_total_tax * 100) / 100;
    items_total_subtotal_with_tax = Math.round((items_total_subtotal+items_total_tax) * 100) / 100;
    items_total_discounts = Math.round(items_total_discounts * 100) / 100;

    //subtotals

    var invoice_subtotal_text = $('.invoice_subtotal_value').text().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');
    if (currencySymbolPlacement == 'before') {
        $('.items_total_subtotal_with_tax').text(invoice_subtotal_text + items_total_subtotal_with_tax.toFixed(2));
    } else {
        $('.items_total_subtotal_with_tax').text(items_total_subtotal_with_tax.toFixed(2) + invoice_subtotal_text);
    }

    if (currencySymbolPlacement == 'before') {
        $('.invoice_subtotal_value').text(invoice_subtotal_text + items_total_subtotal.toFixed(2));
    } else {
        $('.invoice_subtotal_value').text(items_total_subtotal.toFixed(2) + invoice_subtotal_text);
    }

    var item_tax_total_text = $('.invoice_tax_total_value_two').text().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');
    $('.invoice_tax_total_value').text(item_tax_total_text + '' + (items_total_tax).toFixed(2));

    if (currencySymbolPlacement == 'before') {
        $('.invoice_tax_total_value_two').text(item_tax_total_text + '' + (items_total_tax).toFixed(2));
    } else {
        $('.invoice_tax_total_value_two').text((items_total_tax).toFixed(2) + item_tax_total_text);
    }


    var invoice_discount_amount = parseFloat($('#invoice_discount_amount').val()).toFixed(2);
    var invoice_discount_percent = parseFloat($('#invoice_discount_percent').val()).toFixed(2);
    var temp_total = items_total_subtotal + items_total_tax;

    if (invoice_discount_amount != undefined && parseFloat(invoice_discount_amount) > 0) {
        temp_total -= invoice_discount_amount;
    }

    if (invoice_discount_percent != undefined && parseFloat(invoice_discount_percent) > 0) {
        temp_total -= temp_total * (invoice_discount_percent / 100);
    }

    temp_total = Math.round(temp_total * 100) / 100;

    // totals

    var invoice_total_text = $('.invoice_total_value').text().replace(/[0-9 \.\,]/g, '').replace(/NaN/g, '');
    if (currencySymbolPlacement == 'before') {
        $('.invoice_total_value').html(invoice_total_text + '&nbsp;' + temp_total.toFixed(2));
    } else {
        $('.invoice_total_value').html(temp_total.toFixed(2) + invoice_total_text);
    }

    // balance

    var invoice_total_text = $('.balance_total_value').text().replace(/[0-9 \.\,\-]/g, '').replace(/NaN/g, '').replace(/--[\.]*--/g, '');
    if (currencySymbolPlacement == 'before') {
        $('.balance_total_value').html(invoice_total_text + temp_total.toFixed(2));
    } else {
        $('.balance_total_value').html(temp_total.toFixed(2) + invoice_total_text);
    }
}

function reload_amounts_on_change() {
    var elementsAll = "input[name=item_price],input[name=item_discount_percent],select[name=item_tax_rate_id],#invoice_discount_amount,#invoice_discount_percent";
    var elementQty = "input[name=item_quantity]";

    $(elementsAll).unbind().change(function() {
        calculate_amounts(false);
        hide_pdf_button();
    });

    $(elementQty).unbind().change(function() {
        calculate_amounts(true);
        hide_pdf_button();
    });
}
