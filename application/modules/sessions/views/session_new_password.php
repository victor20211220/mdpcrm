<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('page_new_password_title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/assets/default/css/style.css" rel="stylesheet">
    <link href="/assets/default/css/custom.css" rel="stylesheet">

    <style>
        body {
            padding-top: 60px;
        }
    </style>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="https://code.jquery.com/jquery-2.2.4.js"
            integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
</head>

<body>
<div class="container">

    <div id="password_reset"
         class="panel panel-default panel-body col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

        <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

        <h3><?= lang('set_new_password'); ?></h3>
        <br/>

        <form class="form-horizontal" method="post" action="/sessions/passwordreset">

            <input name="user_id" value="<?= $user_id; ?>" class="hidden" readonly>

            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="new_password" class="control-label"><?= lang('new_password'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="password" name="new_password" id="new_password" class="form-control"
                           placeholder="<?= lang('new_password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 col-sm-3">
                    <label for="new_password" class="control-label"><?= lang('confirm_password'); ?></label>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <input type="password" name="new_password2" id="new_password2" class="form-control"
                           placeholder="<?= lang('password_confirmation'); ?>">
                </div>
            </div>

            <input type="submit" name="btn_new_password" class="btn btn-block btn-warning" value="<?= lang('set_new_password'); ?>">
        </form>

    </div>
</div>

<script type="text/javascript">
    var password = document.getElementById("new_password");
    var confirm_password = document.getElementById("new_password2");

    function validatePassword() {
        if (password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Passwords Don't Match");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirm_password.onkeyup = validatePassword;
</script>
</body>
</html>
