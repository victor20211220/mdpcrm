<div class="container">
    <div class="install-panel">

        <h1 id="logo"><span>mdpcrm</span></h1>

        <form method="post"
              action="<?php echo site_url($this->uri->uri_string()); ?>">

            <legend><?php echo lang('setup_database_details'); ?></legend>

            <?php if (!$database['success']) { ?>

                <?php if ($database['message'] and $_POST) { ?>
                    <p><span class="label label-danger"><?php echo lang('failure'); ?></span>
                        <?php echo $database['message']; ?>
                    </p>
                <?php } ?>

                <p><?php echo lang('setup_database_message'); ?></p>

                <div class="form-group">
                    <label for="db_hostname">
                        <?php echo lang('hostname'); ?>
                    </label>
                    <input type="text" name="db_hostname" id="db_hostname" class="form-control"
                           value="<?php echo $this->input->post('db_hostname', true); ?>">
                    <span class="help-block"><?php echo lang('setup_db_hostname_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('username'); ?>
                    </label>
                    <input type="text" name="db_username" id="db_username" class="form-control"
                           value="<?php echo $this->input->post('db_username', true); ?>">
                    <span class="help-block"><?php echo lang('setup_db_username_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('password'); ?>
                    </label>
                    <input type="password" name="db_password" id="db_password" class="form-control"
                           value="<?php echo $this->input->post('db_password', true); ?>">
                    <span class="help-block"><?php echo lang('setup_db_password_info'); ?></span>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('database'); ?>
                    </label>
                    <input type="text" name="db_database" id="db_database" class="form-control"
                           value="<?php echo $this->input->post('db_database', true); ?>">
                    <span class="help-block"><?php echo lang('setup_db_database_info'); ?></span>
                </div>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-primary" name="btn_try_again"
                       value="<?php echo lang('try_again'); ?>">
            <?php } else { ?>
                <p><i class="fa fa-check text-success fa-margin"></i>
                    <?php echo lang('setup_database_configured_message'); ?>
                </p>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php echo lang('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
