<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="font-thin h3"><?= lang('change_password'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">
            <div class="panel-body">

                <?= $this->layout->load_view('layout/alerts'); ?>

                <fieldset>
                    <legend><?= lang('change_password'); ?></legend>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label"><?= lang('password'); ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="password" name="user_password" id="user_password"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                            <label class="control-label">
                                <?= lang('verify_password'); ?>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="password" name="user_passwordv" id="user_passwordv" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        </div>
                        <div class="col-xs-12 col-sm-6 row">
                            <ul class="nav nav-pills nav-sm">
                                <?php $this->layout->load_view('layout/header_buttons'); ?>
                            </ul>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>
</form>
