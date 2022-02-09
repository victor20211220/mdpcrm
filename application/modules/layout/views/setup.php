<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en">
<![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en">
<![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en">
<![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <title>mdpcrm Setup</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="NOINDEX,NOFOLLOW">

    <link rel="icon" type="image/png" href="/assets/default/img/favicon.png">
    <link rel="stylesheet" href="/assets/default/css/welcome.css">

    <script src="/assets/default/js/libs/modernizr-2.8.3.min.js"></script>
    <script src="/assets/default/js/libs/jquery-1.11.2.min.js"></script>
    <script src="/assets/default/js/libs/select2.min.js"></script>
</head>

<body>
<noscript>
    <div class="alert alert-danger no-margin"><?= lang('please_enable_js'); ?></div>
</noscript>

<?= $content; ?>


<!--[if lt IE 7 ]>
<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
<script>window.attachEvent('onload', function () {
    CFInstall.check({mode: 'overlay'})
})</script>
<![endif]-->

</body>
</html>
