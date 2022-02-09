<div id='email-alert'></div>
<table class='email-item-table'>
    <thead style='border-bottom: 12px solid #F2F2F2;'>
    <tr class='email-item-head-tr'>
        <th class='email-item-col-check text-left text-normal' style='font-weight: normal; padding: 4px 13px !important;'>
            <b>Mail from:</b> <?= $data->from_email; ?><br>
            <b>Date:</b> <?= date('d/m/Y H:i', strtotime($data->date)); ?><br>
            <b>Subject:</b> <?= $data->subject; ?>
            <span style="display: none">
                <span id="email-message-id"><?= $data->id; ?></span>
            </span>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <?php if ($data->content_html == true): ?>
                <iframe style="width: 100%; height: 445px; border: 0;" src="/email/body/<?= $id; ?>"></iframe>
            <?php else: ?>
                <div style='width: 100%; height: 100%; padding: 15px;'>
                    <?= $data->content; ?>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    </tbody>
</table>
