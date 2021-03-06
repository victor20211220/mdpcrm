<script type="text/javascript">
    $(function () {
        $('#modal_delete_quote_confirm').click(function () {
            quote_id = $(this).data('quote-id');
            window.location = '<?= site_url('quotes/delete'); ?>/' + quote_id;
        });
    });
</script>

<div id="delete-quote" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-4 col-md-offset-4"
     role="dialog" aria-labelledby="modal_delete_quote" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('delete_quote'); ?></h3>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <?= lang('delete_quote_warning'); ?>
            </div>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <a href="#" id="modal_delete_quote_confirm" class="btn btn-gray padder"
                   data-quote-id="<?= $quote->quote_id; ?>">
                    <i class="fa fa-trash-o"></i> <?= lang('yes'); ?>
                </a>
                <a href="#" class="btn btn-success padder" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('no'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
