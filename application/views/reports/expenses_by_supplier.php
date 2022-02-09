<html>
<head>
    <title><?= lang('expenses_by_supplier'); ?></title>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/default/css/reports.css" type="text/css">
</head>
<body>
<div id="main">
    <table class="header-table">
        <tr>
            <td width="60%">
                <h3 class="report_title"><?= lang('123'); ?></h3><br><br>
                <?= lang('from_date'); ?>:&nbsp;<?= $from_date; ?><br>
                <?= lang('to_date'); ?>:&nbsp;<?= $to_date; ?><br>
            </td>
            <td width="30%" align='right'><?= invoice_logo_pdf(); ?></td>
        </tr>
    </table>
    <table class="data-table">
        <tr>
            <th><?= lang('client'); ?></th>
            <th class="amount"><?= lang('invoice_count'); ?></th>
            <th class="amount"><?= lang('sales'); ?></th>
            <th class="amount"><?= lang('sales_with_tax'); ?></th>
        </tr>
        <?php $totalsales = 0;
        $totalsales_with_tax = 0; ?>
        <?php foreach ($results as $result) { ?>
            <?php $totalinvoicecount = $totalinvoicecount + $result->invoice_count; ?>
            <?php $totalsales = $totalsales + $result->sales;
            $totalsales_with_tax = $totalsales_with_tax + $result->sales_with_tax; ?>
            <tr>
                <td><?= $result->client_name; ?></td>
                <td class="amount"><?= $result->invoice_count; ?></td>
                <td class="amount"><?= format_currency($result->sales); ?></td>
                <td class="amount"><?= format_currency($result->sales_with_tax); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td></td>
            <td class="amount"><?= $totalinvoicecount; ?></td>
            <td class="amount"><?= format_currency($totalsales); ?></td>
            <td class="amount"><?= format_currency($totalsales_with_tax); ?></td>
        </tr>
        <tr class="total">
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>
