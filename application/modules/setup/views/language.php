<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>mdpcrm</span></h1>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <legend><?php echo lang('setup_choose_language'); ?></legend>

            <p><?php echo lang('setup_choose_language_message'); ?></p>

            <select name="ip_lang" class="form-control">
                <?php foreach ($languages as $language) { ?>
                    <option value="<?php echo $language; ?>"
                            <?php if ($language == 'english') { ?>selected="selected"<?php } ?>><?php echo ucfirst($language); ?></option>
                <?php } ?>
            </select>

            <br/>

            <input class="btn btn-success" type="submit" name="btn_continue" value="<?php echo lang('continue'); ?>">

        </form>

    </div>
</div>

