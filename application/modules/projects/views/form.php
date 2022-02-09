<form method="post" class="form-horizontal">
    <div id="headerbar">
        <h1><?= lang('projects_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div class="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="project_name" class="control-label">
                    <?= lang('project_name'); ?>:
                </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="project_name" id="project_name" class="form-control"
                       value="<?= $this->Mdl_projects->form_value('project_name'); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label class="control-label"><?= lang('client'); ?>: </label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="client_id" id="client_id" class="form-control">
                    <option value=""><?= lang('no_client'); ?></option>
                    <?php foreach ($clients as $client) : ?>
                        <option value="<?= $client->client_id; ?>"
                            <?php if ($this->Mdl_projects->form_value('client_id') == $client->client_id) { ?> selected="selected" <?php } ?>
                        >
                            <?= $client->client_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

    </div>
</form>
