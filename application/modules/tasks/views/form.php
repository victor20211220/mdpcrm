<form method="post" class="form-horizontal">

    <input type="hidden" name="client_id" value="<?= $this->Mdl_tasks->form_value('client_id'); ?>">

    <div class="lter wrapper-md">
        <?php echo "<pre>"; print_r($clients); print_r($task_statuses); ?>
        <h1 class="m-n font-thin h3"><?= lang('tasks_form'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <legend>
                    <?php if ($this->Mdl_tasks->form_value('task_id')) : ?>
                        #<?= $this->Mdl_tasks->form_value('task_id'); ?>&nbsp;
                        <?= $this->Mdl_tasks->form_value('task_name'); ?>
                    <?php else : ?>
                        <?= lang('new_task'); ?>
                    <?php endif; ?>
                </legend>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label"><?= lang('task_name'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="task_name" id="task_name" class="form-control"
                               value="<?= $this->Mdl_tasks->form_value('task_name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label"><?= lang('task_description'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="task_description" id="task_description" class="form-control"
                               value="<?= $this->Mdl_tasks->form_value('task_description'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label"><?= lang('client'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <select name="client_id" id="client_id" class="form-control">
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client->client_id; ?>"
                                        <?php if ($client->client_id == $this->Mdl_tasks->form_value('client_id')) { ?>selected="selected"<?php } ?>>
                                    <?= $client->client_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label">
                            <?= lang('task_finish_date'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <input name="task_finish_date" id="task_finish_date" class="form-control datepicker"
                                   value="<?= date_from_mysql($this->Mdl_tasks->form_value('task_finish_date')); ?>"
                            />
                            <label for="task_finish_date" class="input-group-btn">
                                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label"><?= lang('status'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <select name="task_status" id="task_status" class="form-control">
                            <?php foreach ($task_statuses as $key => $status): ?>
                                <option value="<?= $key; ?>"
                                        <?php if ($key == $this->Mdl_tasks->form_value('task_status')) { ?>selected="selected"<?php } ?>>
                                    <?= $status['label']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="form-group" style="display:none;" id="divhours">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label class="control-label"><?= lang('time'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" class="form-control" name="total_time">
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">

                    </div>
                    <div class="col-xs-12 col-sm-6 row">
                        <ul class="nav nav-pills nav-sm">
                            <div class="bg-white but-wrapper">
                                <?php
                                $this->load->library('user_agent');

                                $uri = $this->agent->referrer();
                                $uris = explode('/', $uri);

                                if ($uris[3] == 'clients') {
                                    $return = $this->agent->referrer() . '#tab6';
                                } else {
                                    $return = $this->agent->referrer();
                                } ?>
                                <a href="<?= $return; ?>" id="btn-cancel" name="btn_cancel"
                                   class="btn btn-default btn-sm" value="1">
                                   <?= lang('cancel'); ?>
                                </a>
                                <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success btn-sm"
                                        value="1">
                                    <?= lang('save'); ?>
                                </button>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>


<script type="text/javascript">
    $('#task_status').change(function () {
        if ($('#task_status').val() == '3') {
            $('#divhours').fadeIn('slow');
        }

        if ($('#task_status').val() != '3') {
            $('#divhours').fadeOut('slow');
        }
    });
</script>
