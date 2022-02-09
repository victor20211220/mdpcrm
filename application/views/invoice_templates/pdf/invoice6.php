<?php
    $total_without_vat = 0;
    $total_vat_eur = 0;
    $total_total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="/usr/share/nginx/html/assets/invoices/assets/css/invoice-6.css">
</head>
<body>
    <div class="wrapper two-columns">
        <div class="logo">
            <?= invoice_logo(); ?>
        </div>
        <div align="right">
            <div id="qr-code">
                <?= getQrCode($qr_code_file_path); ?>
            </div>
        </div>
    </div>

    <div id="invoice-details">
        <div><?= lang('invoice'); ?> # <?= $invoice->invoice_number; ?></div>
        <span><?= lang('date'); ?>:  <?= $invoice->invoice_date_created; ?></span>
    </div>

    <div id="details" class="wrapper two-columns">
        <div style="max-width: 75% !important;">
            <b><?= lang('from'); ?></b>
            <div class="details">
                <b><?= $invoice->company_name; ?></b>
                <div style="white-space: nowrap">
                    <span><?= lang('address'); ?>:</span>
                    <?= $invoice->company_address; ?>
                </div>
                <div style="white-space: nowrap">
                    <span><?= lang('invoice_code'); ?>:</span>
                    <?= $invoice->company_code; ?>
                </div>
                <div style="white-space: nowrap">
                    <span><?= lang('invoice_vat'); ?>:</span>
                    <?= $invoice->company_vatregnumber; ?>
                </div>
                <?php if ($invoice->company_iban) { ?>
                    <div>
                        <span><?= lang('invoice_bank'); ?>:</span>
                        <?= $invoice->company_iban; ?>
                    </div>
                <?php } ?>
                <?php if ($invoice->company_bank_bic) { ?>
                    <div>
                        <span><?= lang('swift'); ?>:</span>
                        <?= $invoice->company_bank_bic; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div style="max-width: 75% !important;">
            <b><?= lang('to'); ?></b>
            <div class="details">
                <b><?= $invoice->client_name; ?></b>
                <div style="white-space: nowrap">
                    <span><?= lang('address'); ?>:</span>
                    <?= $invoice->client_address_1; ?>
                </div>
                <div style="white-space: nowrap">
                    <span><?= lang('invoice_code'); ?>:</span>
                    <?= ($invoice->client_city) ? $invoice->client_reg_number : ''; ?>
                </div>
                <div style="white-space: nowrap">
                    <span><?= lang('invoice_vat'); ?>:</span>
                    <?= ($invoice->client_state) ? $invoice->client_vat_id : ''; ?>
                </div>
                <?php if ($invoice->client_iban) { ?>
                    <div>
                        <span><?= lang('iban'); ?>:</span>
                        <?= $invoice->client_iban; ?>
                    </div>
                <?php } ?>
                <?php if ($invoice->client_swift) { ?>
                    <div>
                        <span><?= lang('swift'); ?>:</span>
                        <?= $invoice->client_swift; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <table id="products">
        <thead>
        <tr>
            <th width="35%"><?= lang('item'); ?></th>
            <th width="10%"><?= lang('quantity'); ?></th>
            <th width="10%"><?= lang('price'); ?></th>

            <?php if ($itemsDiscount): ?>
            <th width="10%"><?= lang('discount'); ?></th>
            <?php endif; ?>

            <th width="10%"><?= lang('VAT'); ?>%</th>
            <th width="10%"><?= lang('VAT'); ?> EUR</th>
            <th width="15%"><?= lang('total_without_vat'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach ($items as $item)
        {
            $i++;
            ?>
            <?php
            $item_total_one = ($item->item_quantity)*($item->item_price);

            if ($item->item_discount_percent > 0) {
                $item_total = $item_total_one - ($item_total_one / 100 * ($item->item_discount_percent));
            } else {
                $item_total = $item_total_one;
            }

            $tax_percentage = ($item->item_tax_total)*100/$item_total; ?>
            <tr>
                <td>
                    <?= html_escape($item->item_name); ?><br>
                    <div class="description"><?= $item->item_description; ?></div>
                </td>
                <td><?= format_amount($item->item_quantity, true, 8); ?></td>
                <td><?= format_amount($item->item_price, true, 8, 2); ?></td>

                <?php if ($itemsDiscount): ?>
                <td>
                    <?= $item->item_discount_percent > 0 ? format_amount($item->item_discount_percent).'%' : '-'; ?>
                </td>
                <?php endif; ?>

                <td><?= round($tax_percentage); ?>%</td>
                <td><?= $item->item_tax_total; ?></td>
                <td>
                    <?php $total_with_discount = (($item->item_subtotal * $item->item_discount_percent) / 100); ?>
                    <?= format_currency($item->item_subtotal - $total_with_discount); ?>
                </td>
            </tr>
            <?php
            $total_without_vat = $total_without_vat + $item->item_subtotal - $total_with_discount;
            $total_vat_eur = $total_vat_eur + $item->item_tax_total;
        }
        ?>
        </tbody>
    </table>

    <table id="subtotal">
        <tr>
            <td width="50%"></td>
            <td width="25%"><?= lang('subtotal'); ?>:</td>
            <td><?= format_currency($total_without_vat); ?></td>
        </tr>
        <tr>
            <td></td>
            <td><?= lang('VAT'); ?>:</td>
            <td><?= format_currency($total_vat_eur); ?></td>
        </tr>
    </table>

    <table id="total">
        <tr>
            <td width="50%"></td>
            <td width="25%"><?= lang('total'); ?>:</td>
            <td><?= format_currency($total_vat_eur + $total_without_vat); ?></td>
        </tr>
    </table>

    <p id="amount-in-words">
        <b<?= lang('amount_in_words'); ?>:</b>
        <?php $f = new NumberFormatter("en", NumberFormatter::SPELLOUT); echo ucwords($f->format($total_vat_eur + $total_without_vat)); ?>
    </p>

    <p id="comments" style="margin-top: 30px !important;">
        <b><?= lang('comments'); ?>:</b> <?= $invoice->invoice_terms; ?>
    </p>
</body>
</html>
