<script type="text/javascript">
    $(function () {
        $('#modal_delete_invoice_confirm').click(function () {
            invoice_id = $(this).data('invoice-id');
            window.location = 'invoices/delete' + invoice_id;
        });
    });
</script>

<div id="delete-invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-4 col-md-offset-4"
     role="dialog" aria-labelledby="delete-invoice" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('delete_invoice'); ?></h3>
        </div>
        <div class="modal-body">
            <p class="alert alert-danger"><?= lang('delete_invoice_warning'); ?></p>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <a href="#" id="modal_delete_invoice_confirm" class="btn btn-danger padder"
                   data-invoice-id="<?= $invoice->invoice_id; ?>">
                    <i class="fa fa-trash-o"></i>
                    <?= lang('confirm_deletion') ?>
                </a>

                <a href="#" class="btn btn-gray padder" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('no'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
