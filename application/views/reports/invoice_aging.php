<html>
<head>
    <title><?= lang('invoice_aging'); ?></title>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/default/css/reports.css" type="text/css">
</head>
<body>
<div id="main">
    <table class="header-table">
        <tr valign="top">
            <td width="60%">
                <h3><?= lang('invoice_aging'); ?></h3>
            </td>
            <td width="20%" align="center"><?= invoice_logo_pdf(); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <th><?= lang('client'); ?></th>
            <th class="amount"><?= lang('invoice_aging_1_15'); ?></th>
            <th class="amount"><?= lang('invoice_aging_16_30'); ?></th>
            <th class="amount"><?= lang('invoice_aging_above_30'); ?></th>
            <th class="amount"><?= lang('total'); ?></th>
        </tr>
        <?php foreach ($results as $result) { ?>
            <tr>
                <td><?= $result->client_name; ?></td>
                <td class="amount"><?= format_currency($result->range_1); ?></td>
                <td class="amount"><?= format_currency($result->range_2); ?></td>
                <td class="amount"><?= format_currency($result->range_3); ?></td>
                <td class="amount"><?= format_currency($result->total_balance); ?></td>
            </tr>
        <?php } ?>
        <tr class="total">
            <td colspan="5">&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>
