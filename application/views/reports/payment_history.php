<html>
<head>
    <title><?= lang('payment_history'); ?></title>
    <link rel="stylesheet" href="<?= base_url(); ?>/assets/default/css/reports.css" type="text/css">
</head>
<body>
<div id="main">
    <table class="header-table">
        <tr>
            <td width="60%">
                <h3 class="report_title"><?= lang('payment_history'); ?></h3><br><br>
                <?= lang('from_date'); ?>:&nbsp;<?= $from_date; ?><br>
                <?= lang('to_date'); ?>:&nbsp;<?= $to_date; ?><br>
            </td>
            <td width="30%" align='right'><?= invoice_logo_pdf(); ?></td>
        </tr>
    </table>
    <table class="data-table">
        <tr>
            <th><?= lang('date'); ?></th>
            <th><?= lang('invoice'); ?></th>
            <th><?= lang('client'); ?></th>
            <th><?= lang('payment_method'); ?></th>
            <th><?= lang('note'); ?></th>
            <th class="amount"><?= lang('amount'); ?></th>
        </tr>

        <?php $sum = 0; ?>

        <?php foreach ($results as $result) : ?>
            <tr>
                <td><?= date_from_mysql($result->payment_date, true); ?></td>
                <td><?= $result->invoice_number; ?></td>
                <td><?= $result->client_name; ?></td>
                <td><?= $result->payment_method_name; ?></td>
                <td><?= nl2br($result->payment_note); ?></td>
                <td class="amount"><?= format_currency($result->payment_amount);
                    $sum = $sum + $result->payment_amount; ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (!empty($results)) : ?>
            <tr>
                <td colspan=5><?= lang('total'); ?></td>
                <td class="amount"><?= format_currency($sum); ?></td>
            </tr>
        <?php endif; ?>

        <tr class="total">
            <td colspan="6">&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>
