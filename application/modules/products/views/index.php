<div class="bg-light lter b-b wrapper-md menu-header-page">
    <div class="row">
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
            <h1 class="m-n font-thin h3"><?= lang('products'); ?></h1>
        </div>
        <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-6">
            <a class="btn btn-sm btn-success padder" href="<?= site_url('products/form'); ?>">
                <?= lang('create_product'); ?>
            </a>
        </div>
    </div>
</div>

<div class="">
    <div class="panel panel-default">
        <div class="panel-body">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="table-responsive">
                <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
                       id="DataTables_Table_0"
                       role="grid" aria-describedby="DataTables_Table_0_info"
                       ui-options="{
                          aoColumns: [
                            null,null,null,null,null,null,
                            {sType: 'custom-amount-sort'},
                            null,null
                          ]
                       }"
                />
                <thead>
                <tr>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('family'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('product_sku'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('product_name'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('product_description'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('stock'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('stock_alert'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('product_price'); ?>
                    </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                        rowspan="1" colspan="1" aria-sort="ascending">
                        <?= lang('tax_rate'); ?>
                    </th>
                    <th><?= lang('options'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php $class = 'odd';
                foreach ($products as $product) : ?>
                    <? $class = ($class == 'odd') ? 'even' : 'odd'; ?>
                    <tr role="row" class="<?= $class; ?>">
                        <td><?= $product->family_name; ?></td>
                        <td><?= $product->product_sku; ?></td>
                        <td><?= $product->product_name; ?></td>
                        <td><?= nl2br($product->product_description); ?></td>
                        <td><?= $product->stock; ?></td>
                        <td><?= $product->stock_alert; ?></td>
                        <td><?= format_currency($product->product_price); ?></td>
                        <td><?= ($product->tax_rate_id) ? $product->tax_rate_name : lang('none'); ?></td>
                        <td>
                            <a href="<?= "/products/form/{$product->product_id}"; ?>" title="<?= lang('edit'); ?>">
                                <i class="fa fa-pencil fa-margin"></i>
                            </a>
                            <a href="<?= "/products/delete/{$product->product_id}"; ?>" title="<?= lang('delete'); ?>"
                               onclick="return confirm('<?= lang('delete_record_warning'); ?>');"
                            >
                                <i class="fa fa-trash-o fa-margin"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type in customer name, date or amount').css({
        'width': '250px',
        'display': 'inline-block'
    });
</script>
