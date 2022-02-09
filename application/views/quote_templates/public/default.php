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

    <div class="pull-left">
        <?php if (in_array($quote->quote_status_id, [2, 3])) { ?>
            <a href="<?= site_url('guest/view/approve_quote/' . $quote->quote_url_key); ?>" class="btn btn-success"><i
                        class="fa fa-check"></i> <?= lang('approve_this_quote'); ?></a>
            <a href="<?= site_url('guest/view/reject_quote/' . $quote->quote_url_key); ?>" class="btn btn-danger"><i
                        class="fa fa-ban"></i> <?= lang('reject_this_quote'); ?></a>
        <?php } elseif ($quote->quote_status_id == 4) { ?>
            <a href="#" class="btn btn-success"><?= lang('quote_approved'); ?></a>
        <?php } elseif ($quote->quote_status_id == 5) { ?>
            <a href="#" class="btn btn-danger"><?= lang('quote_rejected'); ?></a>
        <?php } ?>
    </div>

    <div class="pull-right">
        <a href="<?= site_url('guest/view/generate_quote_pdf/' . $quote_url_key); ?>" class="btn btn-primary"><i
                    class="fa fa-file-pdf-o"></i> <?= lang('download_pdf'); ?></a>
    </div>

    <?php if ($flash_message) { ?>
        <div class="alert flash-message">
            <?= $flash_message; ?>
        </div>
    <?php } ?>

    <div class="clearfix"></div>
</div>

<div id="invoice-container">

    <div id="header">
        <table>
            <tr>
                <td id="company-name">
                    <?= invoice_logo(); ?>
                    <h2><?= $quote->user_name; ?></h2>
                    <p><?php if ($quote->user_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->user_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->user_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->user_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->user_address_1) {
                            echo $quote->user_address_1 . '<br>';
                        } ?>
                        <?php if ($quote->user_address_2) {
                            echo $quote->user_address_2 . '<br>';
                        } ?>
                        <?php if ($quote->user_city) {
                            echo $quote->user_city . ' ';
                        } ?>
                        <?php if ($quote->user_state) {
                            echo $quote->user_state . ' ';
                        } ?>
                        <?php if ($quote->user_zip) {
                            echo $quote->user_zip . '<br>';
                        } ?>
                        <?php if ($quote->user_phone) { ?><?= lang('phone_abbr'); ?>: <?= $quote->user_phone; ?>
                            <br><?php } ?>
                        <?php if ($quote->user_fax) { ?><?= lang('fax_abbr'); ?>: <?= $quote->user_fax; ?><?php } ?>
                    </p>
                </td>
                <td class="alignr"><h2><?= lang('quote'); ?> <?= $quote->quote_number; ?></h2></td>
            </tr>
        </table>
    </div>
    <div id="invoice-to">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h3><?= $quote->client_name; ?></h3>
                    <p><?php if ($quote->client_vat_id) {
                            echo lang("vat_id_short") . ": " . $quote->client_vat_id . '<br>';
                        } ?>
                        <?php if ($quote->client_tax_code) {
                            echo lang("tax_code_short") . ": " . $quote->client_tax_code . '<br>';
                        } ?>
                        <?php if ($quote->client_address_1) {
                            echo $quote->client_address_1 . '<br>';
                        } ?>
                        <?php if ($quote->client_address_2) {
                            echo $quote->client_address_2 . '<br>';
                        } ?>
                        <?php if ($quote->client_city) {
                            echo $quote->client_city . ' ';
                        } ?>
                        <?php if ($quote->client_state) {
                            echo $quote->client_state . ' ';
                        } ?>
                        <?php if ($quote->client_zip) {
                            echo $quote->client_zip . '<br>';
                        } ?>
                        <?php if ($quote->client_phone) { ?><?= lang('phone_abbr'); ?>: <?= $quote->client_phone; ?>
                            <br><?php } ?>
                    </p>
                </td>
                <td style="width:30%;"></td>
                <td style="width:25%;">
                    <table>
                        <tbody>
                        <tr>
                            <td><?= lang('quote_date'); ?></td>
                            <td style="text-align:right;"><?= date_from_mysql($quote->quote_date_created); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('expires'); ?></td>
                            <td style="text-align:right;"><?= date_from_mysql($quote->quote_date_expires); ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('total'); ?></td>
                            <td style="text-align:right;"><?= format_currency($quote->quote_total); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
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
                <td>
                    <?= format_currency($quote->quote_item_subtotal); ?>
                </td>
            </tr>
            <?php if ($quote->quote_item_tax_total > 0) : ?>
                <tr>
                    <td colspan="<?= $itemsDiscount ? 6 : 5; ?>" align="right" style="padding-right: 20px">
                        <b><?= lang('item_tax'); ?></b>
                    </td>
                    <td><?= format_currency($quote->quote_item_tax_total); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="<?= $itemsDiscount ? 6 : 5; ?>" align="right" style="padding-right: 20px">
                    <b><?= lang('total'); ?></b>
                </td>
                <td><b><?= format_currency($quote->quote_total); ?></b></td>
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
