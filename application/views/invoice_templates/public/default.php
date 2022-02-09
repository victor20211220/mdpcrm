<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>mdpcrm</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="/assets/default/css/templates.css">
    <link rel="stylesheet" href="/assets/default/css/custom.css">

    <style>
        body {
            color: #333 !important;
            padding: 0 0 25px;
            height: auto;
        }

        table {
            width: 100%;
        }

        #header table {
            width: 100%;
            padding: 0px;
            margin-bottom: 15px;
        }

        #header table td {
            vertical-align: text-top;
        }

        #invoice-to {
            margin-bottom: 15px;
        }

        #invoice-to td {
            text-align: left
        }

        #invoice-to h3 {
            margin-bottom: 10px;
        }

        .seperator {
            height: 25px
        }

        .no-bottom-border {
            border: none !important;
            background-color: white !important;
        }

        .alignr {
            text-align: right;
        }

        #invoice-container {
            margin: 25px auto;
            width: 900px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.25);
        }

        #menu-container {
            margin: 25px auto;
            width: 900px;
        }

        .flash-message {
            font-size: 120%;
            font-weight: bold;
        }
    </style>

</head>

<body>

<div id="menu-container">

    <a href="<?= site_url('guest/view/generate_invoice_pdf/' . $invoice_url_key); ?>" class="btn btn-success">
        <i class="fa fa-file-pdf-o"></i> <?= lang('download_pdf'); ?>
    </a>
    <?php if ($this->Mdl_settings->setting('merchant_enabled') == 1 and $invoice->invoice_balance > 0) { ?><a
        href="<?= site_url('guest/payment_handler/make_payment/' . $invoice_url_key); ?>" class="btn btn-success"><i
                class="fa fa-credit-card"></i> <?= lang('pay_now'); ?></a><?php } ?>

    <?php if ($flash_message) : ?>
        <div class="alert flash-message">
            <?= $flash_message; ?>
        </div>
    <?php endif; ?>
</div>

<div id="invoice-container">

    <div id="header">
        <table>
            <tr>
                <td id="company-name">
                    <?= invoice_logo(); ?>
                    <p>
                        <?= $invoice->user_vat_id ? lang("vat_id_short") . ": {$invoice->user_vat_id}<br>" : '' ?>
                        <?= $invoice->user_tax_code ? lang("tax_code_short") . ": {$invoice->user_tax_code}" : '' ?>
                        <?= $invoice->user_address_1 ? "{$invoice->user_address_1}<br>" : ''; ?>
                        <?= $invoice->user_address_2 ? "{$invoice->user_address_2}<br>" : ''; ?>
                        <?= $invoice->user_city ? "{$invoice->user_city} " : ''; ?>
                        <?= $invoice->user_state ? "{$invoice->user_state} " : ''; ?>
                        <?= $invoice->user_zip ? "{$invoice->user_zip}<br>" : ''; ?>
                        <?= $invoice->user_phone ? lang('phone_abbr') . ": {$invoice->user_phone}<br>" : '' ?>
                        <?= $invoice->user_fax ? lang('fax_abbr') . ": {$invoice->user_fax}" : '' ?>
                    </p>
                </td>
                <td class="alignr"><h2><?= lang('invoice'); ?> <?= $invoice->invoice_number; ?></h2></td>
            </tr>
        </table>
    </div>
    <div id="invoice-to">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h3><?= $invoice->client_name; ?></h3>
                    <p> <?php if ($invoice->client_email) {
                            echo lang('email') . ': ' . $invoice->client_email . '<br/>';
                        } ?>
                        <?php if ($invoice->client_vat_id) {
                            echo lang("vat_id_short") . ": " . $invoice->client_vat_id . '<br>';
                        } ?>
                        <?php if ($invoice->client_tax_code) {
                            echo lang("tax_code_short") . ": " . $invoice->client_tax_code . '<br>';
                        } ?>
                        <?php if ($invoice->client_address_1) {
                            echo $invoice->client_address_1 . '<br>';
                        } ?>
                        <?php if ($invoice->client_address_2) {
                            echo $invoice->client_address_2 . '<br>';
                        } ?>
                        <?php if ($invoice->client_city) {
                            echo $invoice->client_city . ' ';
                        } ?>
                        <?php if ($invoice->client_state) {
                            echo $invoice->client_state . ' ';
                        } ?>
                        <?php if ($invoice->client_zip) {
                            echo $invoice->client_zip . '<br>';
                        } ?>
                        <?php if ($invoice->client_phone) { ?><?= lang('phone_abbr'); ?>: <?= $invoice->client_phone; ?>
                            <br><?php } ?>
                    </p>
                </td>
                <td style="width:30%;"></td>
                <td style="width:25%;">
                    <table>
                        <tbody>
                        <tr>
                            <td><?= lang('invoice_date'); ?></td>
                            <td style="text-align:right;"><?= date_from_mysql($invoice->invoice_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('due_date'); ?></td>
                            <td style="text-align:right;"><?= date_from_mysql($invoice->invoice_date_due); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('payment_method'); ?></td>
                            <td style="text-align:right;"><?php if ($payment_method != null) {
                                    echo $payment_method->payment_method_name;
                                } ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('amount_due'); ?></td>
                            <td style="text-align:right;"><?= format_currency($invoice->invoice_balance); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <?php $total_without_vat = 0;
    $total_vat_eur = 0; ?>
    <div id="invoice-items">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?= lang('item'); ?></th>
                <th><?= lang('quantity'); ?></th>
                <th><?= lang('price'); ?></th>

                <?php if ($itemsDiscount): ?>
                <th><?= lang('discount'); ?></th>
                <?php endif; ?>

                <th><?= lang('VAT'); ?>%</th>
                <th><?= lang('VAT'); ?> EUR</th>
                <th width="120"><?= lang('total_without_vat'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $i) : ?>
                <tr>
                    <td><?= $i->item_name; ?></td>
                    <td><?= format_amount($i->item_quantity, true, 8, 0); ?></td>
                    <td><?= format_amount($i->item_price, true, 8, 2); ?></td>

                    <?php if ($itemsDiscount): ?>
                    <td><?= format_amount($i->item_discount_percent); ?></td>
                    <?php endif; ?>

                    <td><?= "21.00% - PVM "; ?>   </td>
                    <td><?= $i->item_tax_total; ?></td>

                    <?php $total_with_discount = (($i->item_subtotal * $i->item_discount_percent) / 100); ?>
                    <td><?= format_currency($i->item_subtotal - $total_with_discount); ?></td>
                </tr>
                <?php $total_without_vat = $total_without_vat + $i->item_subtotal - $total_with_discount;
                $total_vat_eur = $total_vat_eur + $i->item_tax_total; ?>
            <?php endforeach ?>
            <tr>
                <td colspan="<?= $itemsDiscount ? 6 : 5; ?>" align="right" style="padding-right: 20px">
                    <b>TOTAL excluding VAT EUR</b>
                </td>
                <td><b><?= format_currency($total_without_vat); ?></b></td>
            </tr>

            <tr>
                <td colspan="<?= $itemsDiscount ? 6 : 5; ?>" align="right" style="padding-right: 20px">
                    <b>VAT EUR</b>
                </td>
                <td><b><?= format_currency($total_vat_eur); ?></b></td>
            </tr>

            <tr>
                <td colspan="<?= $itemsDiscount ? 6 : 5; ?>" align="right" style="padding-right: 20px">
                    <b><?= lang('total'); ?></b>
                </td>
                <td>
                    <b><?= format_currency($total_vat_eur + $total_without_vat); ?></b>
                </td>
            </tr>


            </tbody>
        </table>
        <div class="seperator"></div>


        <h4><strong>Amount in words:</strong></h4>
        <?php
            $totalwords = $total_vat_eur + $total_without_vat;
            $totalwords = ($totalwords = (double)number_format($totalwords, 2));
        ?>
        <p><?= convert_number_to_words($totalwords); ?></p>

    </div>

</div>

</body>
</html>
