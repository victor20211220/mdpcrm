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
    <link rel="stylesheet" type="text/css" href="/usr/share/nginx/html/assets/invoices/assets/css/invoice-5.css">
</head>
<body>
    <div class="wrapper two-columns">
        <div>
            <div id="qr-code">
                <?= getQrCode($qr_code_file_path); ?>
            </div>
        </div>
        <div align="right" class="logo">
            <?= invoice_logo(); ?>
        </div>
    </div>

    <div id="details" class="wrapper two-columns">
        <div>
      <b><?= lang('from'); ?></b>
            <div class="details">
                <b><?= $quote['company']['company_name']; ?></b>
                <div><span><?= lang('address'); ?>:</span> <?= $quote['company']['company_address']; ?></div>
                <div><span><?= lang('invoice_code'); ?>:</span> <?= $quote['company']['company_code']; ?></div>
                <div><span><?= lang('invoice_vat'); ?>:</span> <?= $quote['company']['company_vatregnumber']; ?></div>
                <?php if ($quote['company']['company_iban']) { ?>
                    <div>
                        <span><?= lang('invoice_bank'); ?>:</span>
                        <?= $quote['company']['company_iban']; ?>
                    </div>
                <?php } ?>
                <?php if ($quote['company']['company_bank_bic']) { ?>
                    <div>
                        <span><?= lang('swift'); ?>:</span>
                        <?= $quote['company']['company_bank_bic']; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div>
      <b><?= lang('to'); ?></b>
            <div class="details">
                <b><?= $quote['client']['client_name']; ?></b>
                <div><span><?= lang('address'); ?>:</span> <?= $quote['client']['client_address_1']; ?></div>
                <div><span><?= lang('invoice_code'); ?>:</span><?= $quote['client']['client_reg_number']; ?></div>
                <div><span><?= lang('invoice_vat'); ?>:</span><?= $quote['client']['client_vat_id']; ?></div>
                <?php if ($quote['client']['client_iban']) { ?>
                    <div>
                        <span><?= lang('iban'); ?>:</span>
                        <?= $quote['client']['client_iban']; ?>
                    </div>
                <?php } ?>
                <?php if ($quote['client']['client_swift']) { ?>
                    <div>
                        <span><?= lang('swift'); ?>:</span>
                        <?= $quote['client']['client_swift']; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="invoice-details">
        <div><?= lang('quote'); ?> #<?= $quote['quote']['quote_id']; ?></div>
        <span><?= lang('date'); ?>: <?= $quote['quote']['quote_date_created']; ?></span>
    </div>

    <table id="products">
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
                <th><?= lang('total_without_vat'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach ($quote['items'] as $item)
        {
            $i++;
            ?>
      <tr>
                <td>
                    <?= html_escape($item['item_name']); ?><br>
                    <div class="description"><?= $item['item_description']; ?></div>
                </td>
                <td><?= format_amount($item['item_quantity'], true, 8); ?></td>
                <td><?= format_amount($item['item_price'], true, 8, 2); ?></td>

                <?php if ($itemsDiscount): ?>
                <td><?= format_amount($item['item_discount_percent']); ?></td>
                <?php endif; ?>

                <td><?= $item['item_tax_percentage']; ?></td>
                <td><?= $item['item_tax_total']; ?></td>
                <td>
                    <?php $total_with_discount = (($item['item_subtotal'] * $item['item_discount_percent']) / 100); ?><?= format_currency($item['item_subtotal'] - $total_with_discount); ?>
                </td>
            </tr>
            <?php
            $total_without_vat = $total_without_vat + $item['item_subtotal'] - $total_with_discount;
            $total_vat_eur = $total_vat_eur + $item['item_tax_total'];
        }
        ?>
        </tbody>
    </table>

    <div class="wrapper two-columns light-bg">
        <div>&nbsp;</div>
        <div>
            <table id="subtotal">
                <tr>
                    <td width="50%"><?= lang('subtotal'); ?>:</td>
                    <td><?= format_currency($total_without_vat); ?></td>
                </tr>
                <tr>
                    <td><?= lang('VAT'); ?>:</td>
                    <td><?= format_currency($total_vat_eur); ?></td>
                </tr>
            </table>

            <table id="total">
                <tr>
                    <td width="50%"><?= lang('total'); ?>:</td>
                    <td><?= format_currency($total_vat_eur + $total_without_vat); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <p id="amount-in-words">
        <b><?= lang('amount_in_words'); ?>:</b>
        <?php $f = new NumberFormatter("en", NumberFormatter::SPELLOUT); echo ucwords($f->format($total_vat_eur + $total_without_vat)); ?>
    </p>

    <p id="comments">
    <b><?= lang('comments'); ?>:</b> <?= $invoice->invoice_terms; ?>
    </p>
</body>
</html>
