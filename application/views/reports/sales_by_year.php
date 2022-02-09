<html><?php $total = 0; ?>
<head>
    <title><?= lang('sales_by_date'); ?></title>
    <link rel="stylesheet" href="/assets/default/css/reports.css" type="text/css">

</head>

<body>
<div id="main">
    <table class="header-table">
        <tr>
            <td width="60%">
                <h3 class="report_title"><?= lang('sales_by_date'); ?></h3>
                <div><?= lang('from_date'); ?>:&nbsp;<?= $from_date; ?></div>
                <div><?= lang('to_date'); ?>:&nbsp;<?= $to_date; ?></div>
                <div><?= lang('min_quantity'); ?>:&nbsp;<?= $min_quantity; ?></div>
                <div><?= lang('max_quantity'); ?>:&nbsp;<?= $max_quantity; ?></div>
            </td>
            <td width="30%" align='right'><?= invoice_logo_pdf(); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <th style="width:15%;text-align:center;border-bottom: none;"> <?= lang('vat_id'); ?> </th>
            <th style="width:40%;text-align:center;border-bottom: none;"> <?= lang('name'); ?> </th>
            <th style="width:15%;text-align:center;border-bottom: none;"> <?= lang('period'); ?> </th>
            <th style="width:30%;text-align:center;border-bottom: none;"> <?= lang('quantity'); ?> </th>
        </tr>

        <?php

        $initial_year = 0;
        $final_year = 0;
        $numYears = 1;
        $numRows = 1;
        $contRows = 0;
        $contYears = 0;
        $pattern = '/^payment_*/i';

        foreach ($results as $result) {
            if ($final_year == 0) {
                foreach ($result as $index => $value) {
                    if ($initial_year == 0) {
                        $initial_year = intval(substr($index, 11, 4));
                    }
                    $aux = intval(substr($index, 11, 4));
                    if ($aux > $final_year) {
                        $final_year = $aux;
                    }
                }
            }

            if ($contYears == 0 && ($final_year - $initial_year) > 0) {
                $numYears = $final_year - $initial_year + 1;
                $contYears = 1;
            }
            if ($contRows == 0) {
                $numRows = $numRows + ($numYears * 4);
                $contRows = 1;
            }
            $total = $total + $result->total_payment;
        ?>

            <tr>
                <td style="border-bottom: none;text-align:center;"> <?= $result->VAT_ID; ?> </td>
                <td style="border-bottom: none;text-align:center;" rowspan="<?= $numRows; ?>"
                    valign="top"> <?= $result->Name; ?> </td>
                <td style="border-bottom: none;text-align:center;"> <?= lang('annual'); ?> </td>
                <td style="border-bottom: none;text-align:center;"> <?= format_currency($result->total_payment); ?> </td>
            </tr>

            <?php

            foreach ($result as $index => $value) {

                $quarter = substr($index, 8, 2);
                $year = substr($index, 11, 4);

                if (preg_match($pattern, $index)) {
                    ?>

                    <tr>
                        <td style="border-bottom: none;">&nbsp;</td>
                        <td style="border-bottom: none;text-align:center;"><?php
                            if ($quarter == "t1") {
                                echo lang('Q1') . "/" . $year;
                            } else {
                                if ($quarter == "t2") {
                                    echo lang('Q2') . "/" . $year;
                                } else {
                                    if ($quarter == "t3") {
                                        echo lang('Q3') . "/" . $year;
                                    } else {
                                        if ($quarter == "t4") {
                                            echo lang('Q4') . "/" . $year;
                                        }
                                    }
                                }
                            }
                            ?></td>
                        <td style="border-bottom: none;text-align:center;"><?php if ($value > 0) {
                                echo format_currency($value);
                            } ?></td>
                    </tr>

                    <?php
                }
            }
            ?>


            <?php
        }
        ?>

        <tr class="total">
            <td></td>
            <td></td>
            <td></td>
            <td style="border-bottom: none;text-align:center;"> <?= format_currency($total); ?></td>
        </tr>

    </table>
</div>
</body>
</html>
