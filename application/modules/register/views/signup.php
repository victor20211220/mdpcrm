<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>mdpcrm</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/login/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/login/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/login/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/login/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/login/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/login/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/login/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/login/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/login/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/login/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/login/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/login/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/login/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/login/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/assets/login/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" type="text/css" href="/assets/login/css/styles.css">
</head>

<body>
<section id="main">
    <div><a href="/" class="icons icons-logo"></a></div>
    <section>
        <h1><?= lang('register_company') ?></h1>

        <?php
            if ($display_errors == true) {
                $this->layout->load_view('layout/alerts');
            }
        ?>

        <form method="post" action="">
            <input type="hidden" name="company_name" value="<?= set_value('company_name'); ?>">
            <input type="hidden" name="company_country" value="<?= set_value('company_country'); ?>">
            <input type="hidden" name="default_language" value="<?= set_value('default_language'); ?>">
            <input type="hidden" name="license_key" value="<?= set_value('license_key'); ?>">

            <div class="form-group">
                <div><span class="icons icons-email"></span></div>
                <input type="email" name="user_email" placeholder="<?= lang('email') ?>" value="<?= set_value('user_email'); ?>" required="">
            </div>

            <div class="form-group">
                <div><span class="icons icons-password"></span></div>
                <input type="password" name="password" placeholder="<?= lang('password') ?>" required>
            </div>

            <div class="form-group">
                <div><span class="icons icons-password"></span></div>
                <input type="password" name="user_password" placeholder="<?= lang('confirm') ?>" required>
            </div>

            <div class="form-button">
                <button name="btn_submit" value="1"><?= lang('register_company') ?></button>
            </div>

            <section>
                <nav>
                    <a href="/sessions/login"><?= lang('login') ?></a> |
                    <a href="/sessions/passwordreset"><?= lang('reset_password') ?></a>
                </nav>
            </section>
        </form>

    </section>
</section>
</body>
</html>
