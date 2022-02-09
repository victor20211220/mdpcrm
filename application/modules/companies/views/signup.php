<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>mdpcrm</title>
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

    <script type="text/javascript" src="/assets/default/js/libs/jquery-1.11.1.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#email').focus();
        });
    </script>

</head>

<body>
<div class="container">
    <form method="post" class="form-horizontal">
        <div id="headerbar">
            <h1><?= lang('signup'); ?></h1>
        </div>

        <div id="content">
            <?php $this->layout->load_view('layout/alerts'); ?>

            <fieldset>
                <legend><?= lang('signup'); ?></legend>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label class="control-label"><?= lang('company'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="name" id="company_name" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-3 text-right text-left-xs">
                        <label class="control-label"><?= lang('password'); ?>: </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="password" name="user_password" id="user_password" class="form-control">
                    </div>
                </div>
            </fieldset>
        </div>
    </form>
</div>
</body>
</html>
