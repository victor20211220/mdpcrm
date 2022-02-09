<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        // Creates the invoice
        $('#select-items-confirm').click(function () {
            var item_lookup_ids = [];

            $("input[name='item_lookup_ids[]']:checked").each(function () {
                item_lookup_ids.push(parseInt($(this).val()));
            });

            $.post("<?= site_url('item_lookups/ajax/process_item_selections'); ?>", {
                item_lookup_ids: item_lookup_ids
            }, function (data) {
                items = JSON.parse(data);

                for (var key in items) {
                    if ($('#item_table tr:last input[name=item_name]').val() !== '') {
                        $('#new_item').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }
                    $('#item_table tr:last input[name=item_name]').val(items[key].item_name);
                    $('#item_table tr:last textarea[name=item_description]').val(items[key].item_description);
                    $('#item_table tr:last input[name=item_price]').val(items[key].item_price);
                    $('#item_table tr:last input[name=item_quantity]').val('1');

                    $('#modal-choose-items').modal('hide');


                }
            });
        });
    });

</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">

    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close">x</a>

            <h3><?= lang('add_item_from_lookup'); ?></h3>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <?php foreach ($item_lookups as $item) : ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="item_lookup_ids[]" value="<?= $item->item_lookup_id; ?>">
                            </td>
                            <td>
                                <b><?= $item->item_name; ?></b>
                            </td>
                            <td class="text-right">
                                <?= format_currency($item->item_price); ?>
                            </td>
                        </tr>
                        <tr class="bold-border">
                            <td colspan="3">
                                <?= $item->item_description; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-default" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="select-items-confirm" type="button">
                    <i class="fa fa-check"></i>
                    <?= lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>
</div>
