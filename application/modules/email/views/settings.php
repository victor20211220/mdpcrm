<div class="modal-header" style="background-color: #fff">
    <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>
    <h3><i class="fa fa-cog"></i> <?= lang('email_settings'); ?></h3>
</div>

<form method="post" action="/email/settings_update" enctype="multipart/form-data">
    <div class="">
        <div id="filter_results">
            <div class="">
                <div class="panel-body" style="border-bottom-style: none !important">
                    <?= $this->layout->load_view('layout/alerts'); ?>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_host'); ?></label>
                        <input type="text" class="form-control" name="host" value="<?= $settings['host']; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_username'); ?></label>
                        <input type="text" class="form-control" name="username" value="<?= $settings['username']; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_password'); ?></label>
                        <input type="password" class="form-control" name="password" value="<?= $settings['password']; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_type'); ?></label>
                        <select name="type" class="form-control" required>
                            <option value="0" <?= $settings['type'] == 0 ? 'selected' : null; ?>>
                                <?= lang('email_imap'); ?>
                            </option>
                            <!--
                            <option value="1" <?= $settings['type'] == 1 ? 'selected' : null; ?>>
                                <?= lang('email_pop3'); ?>
                            </option>
                            -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_ssl'); ?></label>
                        <select name="ssl_status" class="form-control" required>
                            <option value="0" <?= $settings['ssl_status'] == 0 ? 'selected' : null; ?>>
                                <?= lang('email_no'); ?>
                            </option>
                            <option value="1" <?= $settings['ssl_status'] == 1 ? 'selected' : null; ?>>
                                <?= lang('email_yes'); ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><?= lang('email_frequency'); ?></label>
                        <select name="frequency" class="form-control" required>
                            <option value="5" <?= $settings['frequency'] == 5 ? 'selected' : null; ?>>
                                5 <?= lang('email_minutes'); ?>
                            </option>
                            <option value="5" <?= $settings['frequency'] == 10 ? 'selected' : null; ?>>
                                10 <?= lang('email_minutes'); ?>
                            </option>
                            <option value="5" <?= $settings['frequency'] == 15 ? 'selected' : null; ?>>
                                15 <?= lang('email_minutes'); ?>
                            </option>
                            <option value="5" <?= $settings['frequency'] == 30 ? 'selected' : null; ?>>
                                30 <?= lang('email_minutes'); ?>
                            </option>
                            <option value="5" <?= $settings['frequency'] == 60 ? 'selected' : null; ?>>
                                60 <?= lang('email_minutes'); ?>
                            </option>
                        </select>
                    </div>
                    <div class="but-wrapper">
                        <button type="submit" data-dismiss="modal" id="btn-cancel" name="btn_cancel"
                                class="btn btn-default" value="1">
                                <?= lang('cancel'); ?>
                        </button>
                        <button type="submit" id="btn-submit" name="btn_submit" class="btn btn-success" value="1">
                            <?= lang('save'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
