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

    <title>MDPCRM - guest receiving invoice</title>

    <meta name="description" content="Angularjs, Html5, Music, Landing, 4 in 1 ui kits package"/>
    <meta name="keywords" content="mdpcrm"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i&amp;subset=latin-ext" rel="stylesheet">
    <link rel="stylesheet" href="/assets/responsive/libs/assets/animate.css/animate.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/assets/font-awesome/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/assets/simple-line-icons/css/simple-line-icons.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/libs/jquery/bootstrap/dist/css/bootstrap.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/font.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/app.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/select2.min.css" type="text/css"/>
    <link rel="stylesheet" href="/assets/responsive/css/typeahead.css?2" type="text/css"/>

    <script src="/assets/responsive/js/jquery-1.11.2.min.js"></script>
    <script src="/assets/responsive/js/jquery-ui-1.11.2.custom.min.js"></script>
    <script src="/assets/responsive/js/dropzone.js"></script>
    <script src="/assets/responsive/js/plugins.js"></script>
    <script src="/assets/responsive/js/scripts.min.js"></script>
    <script src="/assets/responsive/js/bootstrap-datepicker.js"></script>
    <script src="/assets/responsive/js/select2.min.js"></script>

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

<body style="background-image: none !important;">

<div class="app app-header-fixed app-aside-dock">
    <div id="modal-placeholder" style="z-index: 999999 !important"></div>
    <div id="content" class="app-content" role="main">
        <div class="app-content-body">
            <?= $content; ?>
        </div>
    </div>
</div>

<script src="/assets/responsive/libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
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
            $('#modal-placeholder').load('/invoices/ajax/modal_create_invoice');
        });

        $('.create-task').click(function (e) {
            $('#modal-placeholder').load('/tasks/ajax/modal_create_task');
        });

        $('.assign_tasks').click(function (e) {
            $('#modal-placeholder').load('/tasks/ajax/modal_assign_tasks');
        });

        $('.create-received-invoice').click(function (e) {
            $('#modal-placeholder').load('invoices/ajax/modal_create_received_invoice');
        });

        $('.create-quote').click(function (e) {
            e.preventDefault();
            $('#modal-placeholder').load('quotes/ajax/modal_create_quote');
        });

        $('#btn_quote_to_invoice').click(function () {
            quoteId = $(this).data('quote-id');
            $('#modal-placeholder').load('/quotes/ajax/modal_quote_to_invoice/' + quoteId);
        });

        $('#btn_copy_invoice,#btn_copy_invoice_2').click(function () {
            $('#modal-placeholder').load('/invoices/ajax/modal_copy_invoice', {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('#btn_create_credit,#btn_create_credit_2').click(function () {
            $('#modal-placeholder').load('/invoices/ajax/modal_create_credit', {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('#btn_copy_quote').click(function () {
            $('#modal-placeholder').load('/quotes/ajax/modal_copy_quote', {
                quote_id: $(this).data('quote-id')
            });
        });

        $('.client-create-invoice').click(function () {
            $('#modal-placeholder').load('/invoices/ajax/modal_create_invoice', {
                client_id: $(this).data('client-id')
            });
        });

        $('.supplier-create-invoice').click(function () {
            $('#modal-placeholder').load('/invoices/ajax/modal_create_received_invoice', {
                supplier_id: $(this).data('supplier-id')
            });
        });

        $('.client-create-quote').click(function () {
            $('#modal-placeholder').load('/quotes/ajax/modal_create_quote', {
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

        setTimeout(function () {

            $('.alert-success').slideUp("normal", function () {
                $(this).remove();
            });
            ;

        }, 5000);


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
            '/assets/responsive/libs/jquery/datatables/media/js/jquery.dataTables.min.js',
            '/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.js',
            '/assets/responsive/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.css'
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
            alert(1);
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
    $('#myModal').on('show.bs.modal', function (e) {
        var loadurl = e.relatedTarget.data('load-url');
        $(this).find('.modal-body').load(loadurl);
    });
    /*$(document).on('hide.bs.modal','#myModal', function () {
        //location.reload();
     //Do stuff here
    });*/

    $('#aside a').on('click', function () {
        if ($(this).hasClass('auto')) {
            return 1;
        }
        $('.off-screen').removeClass('off-screen')
    })
    $('.glyphicon-align-justify').on('click', function () {
        $(document.body).toggleClass('body_stop_scrolling')
    })
</script>

<div id="myModal" class="modal fade" role="dialog" style="margin: 1% !important; !important; border-radius: 10px;">
    <div class="modal-dialog"
         style="width: 98% !important; height: 70% !important; margin: 10px !important; padding: 10px !important;">

        <!-- Modal content-->
        <div class="modal-content" style="height: auto; min-height: 85%; border-radius: 5px; overflow-y:hidden;">

        </div>

    </div>
</div>

<script src="/assets/default/js/date-select-radio.js"></script>

</body>

</html>
