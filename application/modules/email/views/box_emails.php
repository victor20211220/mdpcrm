<div id='email-alert'></div>
<span style="display: none">
    <span id="email-box-id"><?= $boxId; ?></span>
    <span id="email-box-name"><?= $boxName; ?></span>
</span>
<table class='email-item-table'>
    <thead style='border-bottom: 12px solid #F2F2F2;'>
    <tr class='email-item-head-tr'>
        <th class='email-item-col-check text-center'>
            <i class='fas fa-cog fa-xxs'></i>
        </th>
        <th class='email-item-col-subject'>
            SUBJECT
        </th>
        <th class='email-item-col-star text-center'>
            <span class='fas fa-star fa-xxs'></span>
        </th>
        <th class='email-item-col-from'>
            FROM
        </th>
        <th class='email-item-col-date'>
            DATE
        </th>
        <th class='email-item-col-size'>
            SIZE
        </th>
        <th class='email-item-col-flag text-center'>
            <span class='fas fa-flag fa-xxs'></span>
        </th>
    </tr>
    </thead>
    <tbody>

    <?php if ($messages): ?>
    <?php foreach ($messages as $m): ?>
        <?php
        $from = $m->from_name ? $m->from_name : $m->from_email;
        $date = DateTime::createFromFormat('D M d Y H:i:s e+', $m->date);
        $size = number_format($m->size);
        ?>

        <tr class='email-item-body-tr' data-id='<?= $m->id; ?>'>
        <td class='email-item-col-check text-center'>
            <input class="email-item-checkbox" type='checkbox' data-id='<?= $m->id; ?>'>
        </td>
        <td class='email-item-col-subject font-bold'>
            <?php if ($m->seen == 0): ?>
            <b><?= $m->subject; ?></b>
            <?php else: ?>
            <?= $m->subject; ?>
            <?php endif; ?>
        </td>
        <td class='email-item-col-star text-center'>
            <?php if ($m->star): ?>
            <span class='fas fa-star fa-xxs text-success'></span>
            <?php else: ?>
            <span class='fas fa-star fa-xxs'></span>
            <?php endif; ?>
        </td>
        <td class='email-item-col-from'>
            <?= $from; ?>
        </td>
        <td class='email-item-col-date'>
            <?= $date; ?>
        </td>
        <td class='email-item-col-size'>
            <?= $size; ?>
        </td>
        <td class='email-item-col-flag text-center'>
            <?php if ($m->flagged): ?>
            <span class='fas fa-flag fa-xxs text-success'></span>
            <?php else: ?>
            <span class='fas fa-flag fa-xxs'></span>
            <?php endif; ?>
        </td>
        </tr>
    <?php endforeach; ?>
    <?php else: ?>
    <tr>
        <td class='text-center' colspan='7' style='height: 150px;'>
            <?php if ($isSearch == true): ?>
            <?= "Can't find anything by search string"; ?>
            <?php else: ?>
            <?= lang('there_is_no_emails_yet'); ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endif; ?>

    </tbody>
</table>
