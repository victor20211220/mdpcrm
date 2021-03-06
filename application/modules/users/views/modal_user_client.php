<?php $this->layout->load_view('clients/jquery_client_lookup'); ?>

<script type="text/javascript">
    $(function () {
        $('#btn_user_client').click(function () {

            $.post("/users/ajax/save_user_client", {
                user_id: '<?= $id; ?>',
                client_name: $('#client_name').val()
            }, function (data) {
                $('#div_user_client_table').load("/users/ajax/load_user_client_table", {user_id: '<?= $id; ?>'});
            });

        });
    });

</script>

<div id="add-user-client" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_add_user_client" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?= lang('add_client'); ?></h3>
        </div>

        <div class="modal-body">

            <div class="form-group">
                <label class="control-label"><?= lang('client'); ?>: </label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                       data-provide="typeahead" data-items="8" data-source='' autocomplete="off"
                />
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-default" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= lang('cancel'); ?>
                </button>
                <button class="btn btn-success" id="btn_user_client" type="button">
                    <i class="fa fa-check"></i> <?= lang('submit'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
