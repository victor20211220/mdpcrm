$(document).ready(function ($) {
    function addNewDeleteBtn(e){
        e.preventDefault();
        $($(this).closest('tbody')).remove();
        calculate_amounts(true);
        return false;
    }
    $('.delete_itemz').on('click', addNewDeleteBtn);

    //add new row button here
    $('.btn_add_row').click(function (e) {
        e.preventDefault();

        hide_pdf_button();
        if (strOfInputsForTable) {
            var x = $($.parseHTML(strOfInputsForTable)).appendTo('#item_table').removeAttr('id').addClass('item_list').show().find('input[type=text]').filter(':first').val('').focus();
            x.closest('tr').find('[name="item_price"]').val('');
            x.closest('tr').find('[name="item_id"]').val('');
            x.closest('tr').find('[name="item_product_id"]').val('');
            x.closest('tr').find('.delete_itemz').on('click', addNewDeleteBtn);
            x.closest('tr').find('[name="new_row_added"]').val($('#item_table tbody').length - 1);
            $(x.closest('tr').find('[name="item_total"]')).html('0,00');
        }

        createAutocompliteForInputTags();
        reload_amounts_on_change();

        $('input[name=item_name]').last().focus().select();
    });

    // typeahead start
    var substringMatcher = function (strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function (i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });

            cb(matches);
        };
    };

    function updateTableInputs(row, product) {
        if(!product){
            return false;
        }

        for(var ii= 0 ; ii < $('[name=item_product_id]').length; ii++){
            var val = $('[name=item_product_id]').eq(ii).val();
            if(val == product.product_id) {
                $('[name=item_product_id]').eq(ii).parent().parent().find('[name="item_quantity"]').focus();

                for(var jj = 0 ; jj < $('[name=item_product_id]').length; jj++){
                    var val = $('[name=item_product_id]').eq(jj).val();
                    if(val == "") {
                        $('[name=item_product_id]').eq(jj).parent().parent().parent().remove();
                        calculate_amounts(false);
                        return false;
                    }
                }
            }
        }

        $(row).find('[name="item_quantity"]').val(1);
        $(row).find('[name="item_price"]').val(product['product_price']);
        $(row).find('[name="item_product_id"]').val(product['product_id']);
        $(row).find('[name="product_stock"]').val(product['stock']);

        set_trigger_discount_item();
        reload_amounts_on_change();
        calculate_amounts(false);
    }

    function createAutocompliteForInputTags() {
        $('div#the-basicz > input.tags_inputs').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'invoice_product',
                source: substringMatcher(matchesProductNames)
            })
            .bind('typeahead:select', function (ev, suggestion) {
                updateTableInputs(ev.target.closest('tr'), productsAll[suggestion]);
            })
            .bind('typeahead:change', function (ev, suggestion) {
                updateTableInputs(ev.target.closest('tr'), productsAll[suggestion]);
            })
            .bind('typeahead:autocomplete', function (ev, suggestion) {
                updateTableInputs(ev.target.closest('tr'), productsAll[suggestion]);
            });
    }

    var product_ids = [];
    var matchesProductNames = [];
    var productsAll = {};
    var strOfInputsForTable = $('tbody.item_list')[0].outerHTML;
    var productsUrl = '';
    var urlString = window.location.href;
    var urlStringIndex = urlString.indexOf('guest/invoices/edit');

    if (urlStringIndex > 0) {
        companyHash = urlString.substring(urlStringIndex + 19, urlStringIndex + 20 + 32);
        productsUrl = '/guest/invoices/products' + companyHash;
    } else {
        productsUrl = '/products/ajax/process_product_selections';
    }

    //request to get products
    $.post(productsUrl, {
            product_ids: product_ids
        }, function (data) {
            var products = JSON.parse(data);
            products.forEach(function (item, k, arr) {
                matchesProductNames.push(item['product_name']);
                productsAll[item['product_name']] = item;
            });

            createAutocompliteForInputTags();
        }
    );

    $.ajax('/products/', {
        product_ids: product_ids
    });
});

