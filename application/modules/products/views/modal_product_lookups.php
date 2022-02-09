<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var product_ids = [];

            $("input[name='product_ids[]']:checked").each(function () {
                product_ids.push(parseInt($(this).val()));
            });

            $.post("/products/ajax/process_product_selections", {
                product_ids: product_ids
            }, function (data) {
                items = JSON.parse(data);

                for (var key in items) {
                    // Set default tax rate id if empty
                    if (!items[key].tax_rate_id) items[key].tax_rate_id = 0;

                    if ($('#item_table tbody:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item_list').show();
                    }

                    $('#item_table tbody:last input[name=new_row_added]').val(0);
                    $('#item_table tbody:last input[name=item_product_id]').val(items[key].product_id);
                    $('#item_table tbody:last input[name=item_name]').val(items[key].product_name);
                    $('#item_table tbody:last textarea[name=item_description]').val(items[key].product_description);
                    $('#item_table tbody:last input[name=item_price]').val(items[key].product_price);
                    $('#item_table tbody:last input[name=item_quantity]').val('1');
                    $('#item_table tbody:last select[name=item_tax_rate_id]').val(items[key].tax_rate_id);

                    $('#modal-choose-items').modal('hide');
                }

                set_trigger_discount_item();
                reload_amounts_on_change();
                calculate_amounts()
            });
        });

        // Toggle checkbox when click on row
        $('#products_table tr').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            products_filter();
        });

        $('#filter_product').keyup(function () {
            products_filter();
        });

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            products_filter();
        });

        // Filter products
        function products_filter() {
            var filter_family = $('#filter_family').val();
            var filter_product = $('#filter_product').val();
            var lookup_url = "<?= site_url('products/ajax/modal_product_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            if (filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            $('.modal-body .table-responsive').load(lookup_url + " .table-responsive");
        }
    });
</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>
            <h3><?= lang('add_product'); ?></h3>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-xs-8">
                    <div class="form-inline">
                        <div class="form-group filter-form"></div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="filter_product" id="filter_product"
                                   placeholder="<?= lang('product_name'); ?>"
                                   value="<?= $filter_product ?>"
                            />
                        </div>
                        <button type="button" id="filter-button" class="btn btn-default">
                            <?= lang('search_product'); ?>
                        </button>
                    </div>
                </div>
                <div class="col-xs-4 text-right">
                    <button class="btn btn-default" type="button" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        <?= lang('cancel'); ?>
                    </button>
                    <button class="select-items-confirm btn btn-success" type="button">
                        <i class="fa fa-check"></i>
                        <?= lang('submit'); ?>
                    </button>
                </div>
            </div>
            <br/>

            <div class="table-responsive">
                <table id="products_table" class="table table-bordered table-striped">
                    <tr>
                        <th>&nbsp;</th>
                        <th><?= lang('product_sku'); ?></th>
                        <th><?= lang('family_name'); ?></th>
                        <th><?= lang('product_name'); ?></th>
                        <th><?= lang('product_description'); ?></th>
                        <th class="text-right"><?= lang('product_price'); ?></th>
                    </tr>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td class="text-left">
                                <input type="checkbox" name="product_ids[]"
                                       value="<?= $product->product_id; ?>">
                            </td>
                            <td nowrap class="text-left">
                                <b><?= $product->product_sku; ?></b>
                            </td>
                            <td>
                                <b><?= $product->family_name; ?></b>
                            </td>
                            <td>
                                <b><?= $product->product_name; ?></b>
                            </td>
                            <td>
                                <?= nl2br($product->product_description); ?>
                            </td>
                            <td class="text-right">
                                <?= format_currency($product->product_price); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?= lang('cancel'); ?>
                </button>
                <button class="select-items-confirm btn btn-success padder" type="button">
                    <i class="fa fa-check"></i>
                    <?= lang('submit'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
