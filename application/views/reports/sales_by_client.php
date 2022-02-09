<html>
<head><?php $totalinvoicecount = 0; ?>
    <?php if ($is_received == 0) : ?>
        <title><?= lang('sales_by_client'); ?></title>
    <?php endif; ?>
    <?php if ($is_received == 0) : ?>
        <title><?= lang('expenses_by_supplier'); ?></title>
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/default/css/reports.css" type="text/css">
</head>
<body>
<div id="main">
    <table class="header-table">
        <tr>
            <td width="60%">
                <?php if ($is_received == 0) : ?>
                    <h3 class="report_title"><?= lang('sales_by_client'); ?></h3><br><br>
                <?php endif; ?>

                <?php if ($is_received == 1) : ?>
                    <h3 class="report_title"><?= lang('expenses_by_supplier'); ?></h3><br><br>
                <?php endif; ?>

                <?= lang('from_date'); ?>:&nbsp;<?= $from_date; ?><br>
                <?= lang('to_date'); ?>:&nbsp;<?= $to_date; ?><br>
            </td>
            <td width="30%" align='right'><?= invoice_logo_pdf(); ?></td>
        </tr>
    </table>
    <table class="data-table">
        <tr>
            <?php if ($is_received == 0) : ?>
                <th><?= lang('client'); ?></th>
            <?php endif; ?>

            <?php if ($is_received == 1) : ?>
                <th><?= lang('supplier'); ?></th>
            <?php endif; ?>

            <th class="amount"><?= lang('invoice_count'); ?></th>

            <?php if ($is_received == 0) : ?>
                <th class="amount"><?= lang('sales'); ?></th>
                <th class="amount"><?= lang('sales_with_tax'); ?></th>
            <?php endif; ?>

            <?php if ($is_received == 1) : ?>
                <th class="amount"><?= lang('expenses'); ?></th>
                <th class="amount"><?= lang('expenses_with_tax'); ?></th>
            <?php endif; ?>
        </tr>

        <?php
            $totalsales = 0;
            $totalsales_with_tax = 0;
        ?>

        <?php foreach ($results as $result) : ?>
            <?php $totalinvoicecount = $totalinvoicecount + $result->invoice_count; ?>
            <?php $totalsales = $totalsales + $result->sales;
            $totalsales_with_tax = $totalsales_with_tax + $result->sales_with_tax; ?>
            <tr>
                <?php if ($is_received == 0) : ?>
                    <td><?= $result->client_name; ?></td>
                    <td class="amount"><?= $result->invoice_count; ?></td>
                    <td class="amount"><?= format_currency($result->sales); ?></td>
                    <td class="amount"><?= format_currency($result->sales_with_tax); ?></td>
                <?php endif; ?>
                <?php if ($is_received == 1) : ?>
                    <td><?= $result->supplier_name; ?></td>
                    <td class="amount"><?= $result->invoice_count; ?></td>
                    <td class="amount"><?= format_currency($result->sales); ?></td>
                    <td class="amount"><?= format_currency($result->sales_with_tax); ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>

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
