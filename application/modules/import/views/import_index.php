<div id="headerbar">
    <h1><?= lang('import_data'); ?></h1>
</div>

<div id="content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h5><?= lang('import_from_csv'); ?></h5>
        </div>

        <div class="panel-body">
            <form method="post" action="<?= site_url($this->uri->uri_string()); ?>">
                <?php foreach ($files as $file) : ?>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="files[]" value="<?= $file; ?>"> <?= $file; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <input type="submit" class="btn btn-default" name="btn_submit"
                       value="<?= lang('import'); ?>">
            </form>
        </div>
    </div>
</div>
