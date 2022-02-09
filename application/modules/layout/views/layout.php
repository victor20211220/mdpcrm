<!DOCTYPE html>

<html lang="en">

<head>
    <style>
        thead {
            height: 70% !important;
        }
    </style>
    <link rel="manifest" href="/manifest.json">

    <meta charset="utf-8"/>

    <title>
        <?= $this->Mdl_settings->setting('custom_title') ?: 'mdpcrm'; ?>
    </title>

    <meta name="description" content="Angularjs, Html5, Music, Landing, 4 in 1 ui kits package"/>
    <meta name="keywords" content="AngularJS, angular, bootstrap, admin, dashboard, panel, app, charts, components,flat, responsive, layout, kit, ui, route, web, app, widgets"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i&amp;subset=latin-ext"
          rel="stylesheet">
    <link rel="stylesheet" href="/assets/responsive/libs/assets/animate.css/animate.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/assets/font-awesome/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/assets/simple-line-icons/css/simple-line-icons.css"
          type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/jquery/bootstrap/dist/css/bootstrap.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/font.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/app.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/select2.min.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/typeahead.css?2" type="text/css"/>
    <link rel="stylesheet" href="/assets/tutorialize/css/tutorialize.css">
    <link rel="stylesheet" href="/assets/emails/emails.css">
    <link rel="stylesheet" href="/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.css">

    <script src="/assets/responsive/js/jquery-1.11.2.min.js"></script>
    <script src="/assets/responsive/js/jquery-ui-1.11.2.custom.min.js"></script>
    <script src="/assets/responsive/js/dropzone.js"></script>
    <script defer src="/assets/responsive/js/plugins.js"></script>
    <script defer src="/assets/responsive/js/scripts.min.js"></script>
    <script src="/assets/responsive/js/bootstrap-datepicker.js"></script>
    <script src="/assets/responsive/js/select2.min.js"></script>
    <script src="/assets/tutorialize/js/jquery.tutorialize.min.js"></script>


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

    <style>
        .modal-dialog2 {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .modal-content2 {
            height: auto;
            min-height: 100%;
            border-radius: 0;
        }
    </style>

    <script src="/assets/responsive/js/bloodhound.js"></script>
    <script src="/assets/responsive/js/typeahead.bundle.js"></script>
    <script src="/assets/responsive/js/typeahead.jquery.js"></script>

</head>

<body>

<!-- header -->

<header id="header" class="app-header navbar" role="menu">
    <div class="navbar-header">
        <button class="pull-right visible-xs" ui-toggle="off-screen" target=".app-aside" ui-scroll="app">
            <i class="glyphicon glyphicon-align-justify"></i>
        </button>

        <button class="pull-right visible-xs dk" ui-toggle="show" target=".navbar-collapse">
            <i class="glyphicon glyphicon-cog"></i>
        </button>

        <a href="/" class="navbar-brand text-lt"></a>
    </div>

    <!-- navbar collapse -->

    <div class="collapse pos-rlt navbar-collapse">

        <!-- nabar right -->
        <!-- / navbar right -->

        <!-- link and dropdown -->

        <ul class="nav navbar-nav navbar-right tutor-menu-settings">
            <li class="dropdown">
                <a id="dropdown-menu-href" href="#" data-toggle="dropdown" class="dropdown-toggle">
                    <i class="fa fa-fw fa-plus visible-xs-inline-block"></i>
                    <span><i class="fa fa-cog"></i></span> <span class="caret"></span>
                </a>

                <ul id="dropdown-menu" class="dropdown-menu" role="menu" style="z-index: 10 !important">
                    <li><?= anchor('custom_fields/index', lang('custom_fields')); ?></li>
                    <li><?= anchor('email_templates/index', lang('email_templates')); ?></li>
                    <li><?= anchor('invoice_groups/index', lang('invoice_groups')); ?></li>
                    <li><?= anchor('payment_methods/index', lang('payment_methods')); ?></li>
                    <li><?= anchor('tax_rates/index', lang('tax_rates')); ?></li>

                    <?php if (isset($session_user_type) && $session_user_type == 0) : ?>
                        <li><?= anchor('companies/index', lang('manage_companies')); ?></li>
                    <?php endif; ?>

                    <?php if (isset($session_user_type) && ($session_user_type == 0 || $session_user_type == 3 || $session_user_type == 1)) : ?>
                        <li><?= anchor('users/index', lang('user_accounts')); ?></li>
                    <?php endif; ?>

                    <li class="divider hidden-xs hidden-sm"></li>

                    <?php if (isset($session_user_type) && ($session_user_type == 0 || $session_user_type == 3)) : ?>
                        <li id="system-settings-link">
                            <?= anchor('settings', lang('system_settings')); ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="#" onclick="showDashboardTutorial(true); return false">
                            System Tutorial
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="/sessions/logout"><i class="fa fa-sign-out"></i></a>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right" style="width: 220px">
            <li class="dropdown" style="width: 220px;">
                <span class="clear text" style="margin-top: 13px; margin-right: 15px;">
                    <span class="hidden-sm hidden-md text-sm">
                        <img src="/assets/responsive/img/a2.jpg" style="width: 38px; height: 38px; border-radius: 50%; margin-right: 10px;" align="left">
                        <?= str_replace('ADMIN', '', $this->session->userdata('user_name')); ?>
                        <br>
                        <?= $this->session->userdata('company_name'); ?>
                    </span>
                </span>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right col-md-5">
            <?php if (in_array("email", $user_resources) || (isset($session_user_type) && $session_user_type == 3)) : ?>

                <li class="hidden-xs">
                    <?php
                        $issetSettings = $this->db->get_where(
                                'ip_email_settings', ['user_id' => $this->session->userdata('user_id')]
                        )->num_rows();
                    ?>

                    <?php if ($issetSettings == 1): ?>
                        <a href="/email" data-toggle="modal" data-target="#myModal" data-backdrop="true">
                            <i class="fa fa-envelope"></i>
                            <span class="font-bold"> <?= lang('email'); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="/email/settings" data-toggle="modal" data-target="#emailSettingsModal" data-backdrop="true">
                            <i class="fa fa-envelope"></i>
                            <span class="font-bold"> <?= lang('email'); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
                <li class="hidden-xs">
                    <a href="/calendar" data-toggle="modal" data-target="#calendarModal" data-backdrop="true">
                        <i class="fa fa-calendar"></i>
                        <span class="font-bold">Calendar</span>
                    </a>
                </li>

            <?php endif; ?>
        </ul>

        <!-- / link and dropdown -->

    </div>

    <!-- / navbar collapse -->


</header>

<!-- / header -->

<div class="app app-header-fixed  app-aside-dock ">
    <!-- aside -->
    <aside id="aside" class="app-aside hidden-xs bg-dark" style="z-index: 5 !important">
        <div class="aside-wrap">
            <div class="navi-wrap">

                <!-- user -->

                <div class="clearfix hidden-xs text-center hide" id="aside-user">
                    <div class="dropdown wrapper">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle hidden-folded">

                <span class="clear">
                  <span class="block m-t-sm">
                    <strong class="font-bold text-lt">John.Smith</strong>
                    <b class="caret"></b>
                  </span>
                  <span class="text-muted text-xs block">Art Director</span>
                </span>

                        </a>

                        <!-- dropdown -->

                        <ul class="dropdown-menu animated fadeInRight w hidden-folded">
                            <li>
                                <a href>Settings</a>
                            </li>

                            <li>
                                <a href="page_profile.html">Profile</a>
                            </li>

                            <li>
                                <a href>
                                    <span class="badge bg-danger pull-right">3</span>
                                    Notifications
                                </a>
                            </li>

                            <li class="divider"></li>

                            <li>
                                <a href="page_signin.html">Logout</a>
                            </li>
                        </ul>

                        <!-- / dropdown -->

                    </div>

                    <div class="line dk hidden-folded"></div>

                </div>

                <!-- / user -->

                <nav ui-nav class="navi clearfix">
                    <ul class="nav">
                        <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                            <span>Navigation</span>
                        </li>

                        <?php if (
                                in_array("dashboard", $user_resources) ||
                                (isset($session_user_type) && $session_user_type == 3)
                        ) : ?>
                            <li<?= (isset($active_menu['dashboard'])) ? ' class="active"' : ''; ?>>
                                <a id="button-dashboard" href="/dashboard" class="auto">
                                    <i class="icon-icon-dashboard <?= (isset($active_menu['dashboard'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('dashboard'); ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (
                                in_array("clients", $user_resources) ||
                                (isset($session_user_type) && $session_user_type == 3)
                        ) : ?>

                            <li<?= (isset($active_menu['clients'])) ? ' class="active"' : ''; ?>>

                                <a id="button-clients" href class="auto">
                                  <span class="pull-right text-muted">
                                    <i class="fa fa-fw fa-angle-right text"></i>
                                    <i class="fa fa-fw fa-angle-down text-active"></i>
                                  </span>

                                    <i class="fa fa-user <?= (isset($active_menu['clients'])) ? 'text-primary-cust-blue' : ''; ?>"></i>

                                    <span class="font-bold"><?= lang('clients'); ?></span>
                                </a>

                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="/clients/form">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('add_client'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/clients">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_clients'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/clients/import">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('import_clients'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array("suppliers", $user_resources) || (isset($session_user_type) && $session_user_type == 3)) : ?>
                            <li class="tutor-menu-suppliers <?= (isset($active_menu['suppliers'])) ? ' active' : ''; ?>">

                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <i class="fa fa-users <?= (isset($active_menu['suppliers'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('suppliers'); ?>
                                    </span>
                                </a>

                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="/suppliers/form">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('add_supplier'); ?>
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="/suppliers">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_suppliers'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>

                            </li>
                        <?php endif; ?>

                        <?php if (in_array("quotes", $user_resources) || (isset($session_user_type) && $session_user_type == 3)) : ?>

                            <li<?= (isset($active_menu['quotes'])) ? ' class="active"' : ''; ?>>

                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <i class="fa fa-file <?= (isset($active_menu['quotes'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('quotes'); ?>
                                    </span>
                                </a>

                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="#" class="create-quote">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('create_quote'); ?>
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="/quotes/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_quotes'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (
                                in_array("invoices", $user_resources) ||
                                (isset($session_user_type) && $session_user_type == 3)
                        ) : ?>
                            <?php
                                if (isset($active_menu['invoices']) || isset($active_menu['reccuring'])) {
                                    if (
                                            $this->uri->segment(2) == 'view' &&
                                            $this->uri->segment(1) == 'invoices' &&
                                            $this->db->get_where('ip_invoices', ['invoice_id' => $this->uri->segment(3)])->row('is_received') == 1
                                    ) {
                                        $liClass = '';
                                    } else {
                                        $liClass = ' active';
                                    }
                                } else {
                                    $liClass = '';
                                }
                            ?>
                            <li class="tutor-menu-invoices<?= $liClass; ?>">

                                <a id="button-invoices" href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <i class="fa fa-file-text <?= (isset($active_menu['invoices']) || isset($active_menu['recurring'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('invoices'); ?>
                                    </span>
                                </a>
                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="#" class="create-invoice">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('create_invoice'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/invoices/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_invoices'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/invoices/recurring/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_recurring_invoices'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/invoices/impexp">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('export_invoices'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (
                                in_array("received invoices", $user_resources) ||
                                (isset($session_user_type) && $session_user_type == 3)
                        ) : ?>
                            <li <?php if ($this->uri->segment(2) == 'view' && $this->uri->segment(1) == 'invoices') {
                                if ($this->db->get_where('ip_invoices',
                                        ['invoice_id' => $this->uri->segment(3)])->row('is_received') == 1) {
                                    echo 'class="active"';
                                } else {
                                    echo '';
                                }
                            } else {
                                echo (isset($active_menu['received'])) ? ' class="active"' : '';
                            } ?>>

                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <b class="badge bg-warning pull-right"><?= $invoices_alerts; ?></b>
                                    <i class="icon-icon-received-invoices <?= (isset($active_menu['received'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('view_received_invoices'); ?>
                                    </span>
                                </a>

                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="#" class="create-received-invoice">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('add_received_invoice'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/invoices/received">
                                            <b class="badge bg-warning pull-right"><?= $invoices_alerts; ?></b>
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_received_invoices_2'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/invoices/received/impexp">
                                            <b class="badge bg-warning pull-right"><?= $invoices_alerts; ?></b>
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('export_received_invoices'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>

                            </li>
                        <?php endif; ?>

                        <?php if (
                                in_array("products", $user_resources) ||
                                (isset($session_user_type) && $session_user_type == 3)
                        ) : ?>
                            <li<?= (isset($active_menu['products']) || isset($active_menu['stock'])) ? ' class="active"' : ''; ?>>
                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <b class="badge bg-danger pull-right"><?= $stock_alerts; ?></b>
                                    <i class="fa fa-suitcase <?= (isset($active_menu['products']) || isset($active_menu['stock'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold">
                                        <?= lang('products'); ?>
                                    </span>
                                </a>

                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="/products/form">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('create_product'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/products/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_products'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/products/import">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('import_products'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/families/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span><?= lang('product_families'); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/stock">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('stock_management'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/stock/alerts">
                                            <b class="badge bg-danger pull-right"><?= $stock_alerts; ?></b>
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('stock_alerts'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array("payments", $user_resources) || (isset($session_user_type) && $session_user_type == 3)) : ?>
                            <li class="tutor-menu-payments <?= (isset($active_menu['payments'])) ? ' active' : ''; ?>">
                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>
                                    <i class="icon-icon-payments <?= (isset($active_menu['payments'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold"><?= lang('payments'); ?></span>
                                </a>
                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="/payments/form">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('enter_payment'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/payments/index">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('view_payments'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/payments/select">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('import_payments'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array("tasks", $user_resources) || (isset($session_user_type) && $session_user_type == 3)) : ?>

                            <?php if (isset($session_user_type) && $session_user_type == 3) : ?>
                                <li<?= (isset($active_menu['tasks'])) ? ' class="active"' : ''; ?>>

                                    <a href class="auto">
                                        <span class="pull-right text-muted">
                                            <i class="fa fa-fw fa-angle-right text"></i>
                                            <i class="fa fa-fw fa-angle-down text-active"></i>
                                        </span>

                                        <b class="badge bg-success pull-right"><?= $assgn_task_alerts; ?></b>
                                        <i class="icon-icon-tasks <?= (isset($active_menu['tasks'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                        <span class="font-bold"><?= lang('tasks'); ?></span>
                                    </a>

                                    <ul class="nav nav-sub dk">
                                        <li>
                                            <a href="/tasks/status/my_tasks">
                                                <b class="badge bg-success pull-right"><?= $assgn_task_ins; ?></b>
                                                <span class="arrownavigation">
                                                    <i class="fa fa-fw fa-angle-right"></i>
                                                </span>
                                                <span>
                                                    <?= lang('my_tasks'); ?>
                                                </span>
                                            </a>
                                        </li>

                                        <?php if (isset($session_user_type) && $session_user_type == 3) : ?>
                                            <li>
                                                <a class='assign_tasks' href="#">
                                                    <span class="arrownavigation">
                                                        <i class="fa fa-fw fa-angle-right"></i>
                                                    </span>
                                                    <span>
                                                        <?= lang('assign_user'); ?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>

                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if (in_array("reports", $user_resources)) : ?>
                            <li<?= (isset($active_menu['reports'])) ? ' class="active"' : ''; ?>>
                                <a href class="auto">
                                    <span class="pull-right text-muted">
                                        <i class="fa fa-fw fa-angle-right text"></i>
                                        <i class="fa fa-fw fa-angle-down text-active"></i>
                                    </span>

                                    <i class="icon-icon-reports <?= (isset($active_menu['reports'])) ? 'text-primary-cust-blue' : ''; ?>"></i>
                                    <span class="font-bold"><?= lang('reports'); ?></span>
                                </a>
                                <ul class="nav nav-sub dk">
                                    <li>
                                        <a href="/reports/invoice_aging_get">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('invoice_aging'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/reports/payment_history">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('payment_history'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/reports/sales_by_client">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('sales_by_client'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/reports/sales_by_year">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('sales_by_date'); ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/reports/expenses_by_supplier">
                                            <span class="arrownavigation">
                                                <i class="fa fa-fw fa-angle-right"></i>
                                            </span>
                                            <span>
                                                <?= lang('expenses_by_supplier'); ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </aside>

    <!-- / aside -->
    <div id="modal-placeholder" style="z-index: 999999 !important;"></div>
    <div id="modal-placeholder2" style="z-index: 999999 !important;"></div>

    <!-- content -->
    <div id="content" class="app-content" role="main">
        <div class="app-content-body ">
            <?= $content; ?>
        </div>
    </div>
    <!-- / content -->

</div>


<script src="/assets/responsive/libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
<script src="/assets/responsive/js/jquery.cookie.js"></script>
<script src="/assets/responsive/js/ui-load.js"></script>
<script src="/assets/responsive/js/ui-jp.config.js"></script>
<script src="/assets/responsive/js/ui-jp.js"></script>
<script src="/assets/responsive/js/ui-nav.js"></script>
<script src="/assets/responsive/js/ui-toggle.js"></script>
<script src="/assets/responsive/libs/jquery/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.js"></script>

<script type="text/javascript">
    $(function () {

        $(".datepicker").datepicker({
            autoclose: true,
            format: '<?= date_format_datepicker(); ?>',
            language: '<?= lang('cldr'); ?>',
            weekStart: '<?= $this->Mdl_settings->setting('first_day_of_week'); ?>',
            showOn: "both",
            buttonImage: "/assets/responsive/img/calendar.png",
            buttonImageOnly: true,
            buttonText: "Select date"
        });

        $(document).on('focus', ".datepicker", function () {
            $(this).datepicker({
                autoclose: true,
                format: '<?= date_format_datepicker(); ?>',
                language: '<?= lang('cldr'); ?>',
                weekStart: '<?= $this->Mdl_settings->setting('first_day_of_week'); ?>',
                showOn: "both",
                buttonImage: "/assets/responsive/img/calendar.png",
                buttonImageOnly: true,
                buttonText: "Select date"
            });
        });


        var arrForTime = new Date().toISOString().slice(0, 10).split('-');
        $('[name="to_date"]').val(arrForTime[1] + '/' + arrForTime[2] + '/' + arrForTime[0]);


        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "custom-amount-sort-pre": function (a) {
                a = (a === "-") ? 0 : a.replace(/[^\d\-\.]/g, "");
                return parseFloat(a);
            },

            "custom-amount-sort-asc": function (a, b) {
                return a - b;
            },

            "custom-amount-sort-desc": function (a, b) {
                return b - a;
            }
        });


        $('.nav-tabs').tab();
        $('.tip').tooltip();

        $('.create-invoice').click(function (e) {
            $('#modal-placeholder').load("/invoices/ajax/modal_create_invoice");
        });

        $('.create-task').click(function (e) {
            $('#assign-task').modal('hide');
            $('#modal-placeholder').load("/tasks/ajax/modal_create_task");
        });

        $('.assign_tasks').click(function (e) {
            $('#modal-placeholder2').load("/tasks/ajax/modal_assign_tasks");
        });


        $('.create-received-invoice').click(function (e) {
            $('#modal-placeholder').load("/invoices/ajax/modal_create_received_invoice");
        });

        $('.create-quote').click(function (e) {
            e.preventDefault();
            $('#modal-placeholder').load("/quotes/ajax/modal_create_quote");
        });

        $('#btn_quote_to_invoice').click(function () {
            quote_id = $(this).data('quote-id');
            $('#modal-placeholder').load("/quotes/ajax/modal_quote_to_invoice/" + quote_id);
        });

        $('#btn_copy_invoice,#btn_copy_invoice_2').click(function () {
            invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("/invoices/ajax/modal_copy_invoice", {invoice_id: invoice_id});
        });

        $('#btn_create_credit,#btn_create_credit_2').click(function () {
            invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("/invoices/ajax/modal_create_credit", {invoice_id: invoice_id});
        });

        $('#btn_copy_quote').click(function () {
            quote_id = $(this).data('quote-id');
            $('#modal-placeholder').load("/quotes/ajax/modal_copy_quote", {quote_id: quote_id});
        });


        $('.client-create-invoice').click(function () {
            $('#modal-placeholder').load("/invoices/ajax/modal_create_invoice", {
                client_id: $(this).data('client-id')
            });
        });

        $('.supplier-create-invoice').click(function () {
            $('#modal-placeholder').load("/invoices/ajax/modal_create_received_invoice", {
                supplier_id: $(this).data('supplier-id')
            });
        });

        $('.client-create-quote').click(function () {
            $('#modal-placeholder').load("/quotes/ajax/modal_create_quote", {
                client_id: $(this).data('client-id')
            });
        });

        $(document).on('click', '.invoice-add-payment', function () {
            invoice_id = $(this).data('invoice-id');
            invoice_balance = $(this).data('invoice-balance');
            invoice_payment_method = $(this).data('invoice-payment-method');

            $('#modal-placeholder').load("/payments/ajax/modal_add_payment", {
                invoice_id: invoice_id,
                invoice_balance: invoice_balance,
                invoice_payment_method: invoice_payment_method
            });
        });

        //hide success and error messages

        // setTimeout(function () {
        //     $('.alert-success').slideUp("normal", function () {
        //         $(this).remove();
        //     });
        // }, 5000);
    });


    // lazyload config


    var jp_config = {
        easyPieChart: ['/assets/responsive/libs/jquery/jquery.easy-pie-chart/dist/jquery.easypiechart.fill.js'],
        sparkline: ['/assets/responsive/libs/jquery/jquery.sparkline/dist/jquery.sparkline.retina.js'],
        plot: [
            '/assets/responsive/libs/jquery/flot/jquery.flot.js',
            '/assets/responsive/libs/jquery/flot/jquery.flot.pie.js',
            '/assets/responsive/libs/jquery/flot/jquery.flot.resize.js',
            '/assets/responsive/libs/jquery/flot.tooltip/js/jquery.flot.tooltip.min.js',
            '/assets/responsive/libs/jquery/flot.orderbars/js/jquery.flot.orderBars.js',
            '/assets/responsive/libs/jquery/flot-spline/js/jquery.flot.spline.min.js'
        ],
        moment: ['/assets/responsive/libs/jquery/moment/moment.js'],
        screenfull: ['/assets/responsive/libs/jquery/screenfull/dist/screenfull.min.js'],
        slimScroll: ['/assets/responsive/libs/jquery/slimscroll/jquery.slimscroll.min.js'],
        sortable: ['/assets/responsive/libs/jquery/html5sortable/jquery.sortable.js'],
        nestable: [
            '/assets/responsive/libs/jquery/nestable/jquery.nestable.js',
            '/assets/responsive/libs/jquery/nestable/jquery.nestable.css'
        ],
        filestyle: ['/assets/responsive/libs/jquery/bootstrap-filestyle/src/bootstrap-filestyle.js'],
        slider: [
            '/assets/responsive/libs/jquery/bootstrap-slider/bootstrap-slider.js',
            '/assets/responsive/libs/jquery/bootstrap-slider/bootstrap-slider.css'
        ],
        chosen: [
            '/assets/responsive/libs/jquery/chosen/chosen.jquery.min.js',
            '/assets/responsive/libs/jquery/chosen/bootstrap-chosen.css'
        ],
        TouchSpin: [
            '/assets/responsive/libs/jquery/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js',
            '/assets/responsive/libs/jquery/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css'
        ],
        wysiwyg: [
            '/assets/responsive/libs/jquery/bootstrap-wysiwyg/bootstrap-wysiwyg.js',
            '/assets/responsive/libs/jquery/bootstrap-wysiwyg/external/jquery.hotkeys.js'
        ],
        dataTable: [
        //     '/assets/responsive/libs/jquery/datatables/media/js/jquery.dataTables.min.js',
        //     '/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.js',
        //     '/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.css'
        ],
        vectorMap: [
            '/assets/responsive/libs/jquery/bower-jvectormap/jquery-jvectormap-1.2.2.min.js',
            '/assets/responsive/libs/jquery/bower-jvectormap/jquery-jvectormap-world-mill-en.js',
            '/assets/responsive/libs/jquery/bower-jvectormap/jquery-jvectormap-us-aea-en.js',
            '/assets/responsive/libs/jquery/bower-jvectormap/jquery-jvectormap.css'
        ],
        footable: [
            '/assets/responsive/libs/jquery/footable/dist/footable.all.min.js',
            '/assets/responsive/libs/jquery/footable/css/footable.core.css'
        ],
        fullcalendar: [
            '/assets/responsive/libs/jquery/moment/moment.js',
            '/assets/responsive/libs/jquery/fullcalendar/dist/fullcalendar.min.js',
            '/assets/responsive/libs/jquery/fullcalendar/dist/fullcalendar.css',
            '/assets/responsive/libs/jquery/fullcalendar/dist/fullcalendar.theme.css'
        ],
        daterangepicker: [
            '/assets/responsive/libs/jquery/moment/moment.js',
            '/assets/responsive/libs/jquery/bootstrap-daterangepicker/daterangepicker.js',
            '/assets/responsive/libs/jquery/bootstrap-daterangepicker/daterangepicker-bs3.css'
        ],
        tagsinput: [
            '/assets/responsive/libs/jquery/bootstrap-tagsinput/dist/bootstrap-tagsinput.js',
            '/assets/responsive/libs/jquery/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'
        ]
    };


    $(document).ready(function () {
        function customAmountSorter(a, b) {
            // alert(1);
            return 0;
            a = a.replace(/\d+/g, '');
            b = b.replace(/\d+/g, '');
            if (a > b) return 1;
            if (a < b) return -1;

            return 0;
        }

        $('.dataTables_filter input[type="search"]').attr('placeholder', 'Type in customer name, date or amount').css({
            'width': '250px',
            'display': 'inline-block'
        });
    })

</script>


<script type="text/javascript">
    // Javascript to enable link to tab
    var hash = document.location.hash;
    var prefix = "tab_";
    if (hash) {
        $('.nav-tabs a[href="' + hash.replace(prefix, "") + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash.replace("#", "#" + prefix);
    });
</script>


<script type="text/javascript">
    $('#aside a').on('click', function () {
        if ($(this).hasClass('auto')) {
            return 1;
        }
        $('.off-screen').removeClass('off-screen')
    });

    $('.glyphicon-align-justify').on('click', function () {
        $(document.body).toggleClass('body_stop_scrolling')
    })
</script>

<div id="myModal" class="modal" role="dialog" style="margin-top: -50px;">
    <div class="modal-dialog" style="width: 90% !important;">
        <div class="modal-content" style="border-radius: 5px;">

        </div>
    </div>
</div>

<div id="calendarModal" class="modal" role="dialog" style="margin-top: -50px;">
    <div class="modal-dialog" style="width: 90% !important;">
        <div class="modal-content" style="border-radius: 5px;">

        </div>
    </div>
</div>

<div id="emailSettingsModal" class="modal" role="dialog" style="border-radius: 10px; margin: auto; padding: auto;">
    <div class="modal-dialog" style="padding: 10px !important; margin: auto;">
        <div class="modal-content" style="border-radius: 5px;"></div>
    </div>
</div>

<script src="/assets/default/js/date-select-radio.js"></script>

<script type="text/javascript">
    function showDashboardTutorial(manualRun = false)
    {
        var tutorialViewed = localStorage.getItem('tutorialize-dashboard-viewed');
        var slides = [
            {
                content: "At dashboard you can see most relevant data about your company",
                minWidth: 250,
                width: 500,
                position: 'bottom-center',
                selector: '#button-dashboard',
                overlayMode: 'focus',
                title: 'Welcome to mdpCRM',
                showButtonClose: false
            },
            {
                content: "To start using mdpCRM, you need to insert missing data about your company here, at 'System settings'",
                width: 500,
                position: 'bottom-center',
                selector: '#dropdown-menu-href',
                overlayMode: 'focus',
                title: 'Insert details about your company',
                showButtonClose: false
            },
            {
                content: "You need to create invoice groups here",
                width: 500,
                position: 'bottom-center',
                selector: '#dropdown-menu-href',
                overlayMode: 'focus',
                title: 'For creating invoices',
                showButtonClose: false
            },
            {
                content: "You need to create client and insert details you have",
                width: 500,
                position: 'bottom-center',
                selector: '#button-clients',
                overlayMode: 'focus',
                title: "For creating client",
                showButtonClose: false
            },
            {
                content: "You need to go to create invoice and insert details",
                width: 500,
                position: 'bottom-center',
                selector: '#button-invoices',
                overlayMode: 'focus',
                title: "For creating invoice",
                showButtonClose: false
            }
        ];

        $.tutorialize({
            slides: slides,
            remember: false,
            rememberKeyName: 'tutorialize-dashboard',
            rememberOnceOnly: false,
            arrowPath: '/assets/tutorialize/arrows/arrow-blue.png',
            fontSize: '14px',
            labelNext: 'Next &rarr;',
            labelPrevious: '&larr; Previous',
            labelEnd: 'Got IT !',
            showButtonClose: true
        });

        if (manualRun === false && tutorialViewed != '1') {
            localStorage.setItem('tutorialize-dashboard-viewed', '1');
            $.tutorialize.start();
        }

        if (manualRun === true) {
            $.tutorialize.start();
        }
    }
</script>

<?php if ($this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '') : ?>
    <script>
        $(document).ready(function () {
            showDashboardTutorial(false);
        });
    </script>
<?php endif; ?>

</body>
</html>
