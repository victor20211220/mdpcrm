<div id='email-alert'></div>
<table class='email-item-table'>
    <tbody id="success-send-email-tbody" style="display: none">
    <tr>
        <td>
            <div class="row mt-5 mb-5 pt-5 pb-5">
                <div class="col-12 text-center mt-5 pt-5 mb-5 pb-5">
                    <i class="fas fa-check fa-5x"></i>
                    <h4>YOUR EMAIL WAS SUCCESSFULLY SENT</h4>
                </div>
            </div>
        </td>
    </tr>
    </tbody>

    <tbody id="standart-send-email-tbody" style="padding: 15px;">
    <tr>
        <td style="padding: 25px;">
            <div class="row mb-2">
                <div class="col-6">
                    <input id="send-email-to" class="form-control form-control-sm" type="text" name="email_to" value="<?= $mailTo; ?>" placeholder="Email-to (recipient)">
                </div>
                <div class="col-6 mb-2">
                    <input id="send-email-cc" class="form-control form-control-sm" type="text" name="email-cc" placeholder="CC (carbon copy)">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6 mb-2">
                    <input id="send-email-subject" class="form-control form-control-sm" type="text" name="subject" value="<?= $subject; ?>" placeholder="Subject">
                </div>
                <div class="col-6 mb-2">
                    <input id="send-email-bcc" class="form-control form-control-sm" type="text" name="email-cc" placeholder="BCC (blind carbon copy)">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <?php if ($isReply): ?>
                    <textarea id="send-email-message" class="form-control form-control-sm" name="message" rows="16" placeholder="Type your text here..."></textarea>
                    <?php endif; ?>

                    <?php if ($isForward): ?>
                        <?php if ($mailBody): ?>
                            <?php if ($mailBody->is_html == 0): ?>
                                <?php
                                    $mailBody->content = preg_replace('/\<br(\s*)?\/?\>/i', "", $mailBody->content);
                                ?>
                                <textarea id="send-email-message" class="form-control form-control-sm" name="message" rows="16" placeholder="" readonly><?= $mailBody->content; ?></textarea>
                            <?php else: ?>
                                This message will be sent as a HTML. Preview is not available
                                <textarea id="send-email-message" class="form-control form-control-sm hidden" name="message" rows="16" placeholder="">
                                    <?= $mailBody->content; ?>
                                </textarea>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($isReply == false && $isForward == false): ?>
                    <textarea id="send-email-message" class="form-control form-control-sm" name="message" rows="16" placeholder="Type your text here..."></textarea>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <button class="btn btn-sm btn-outline-primary email-send-message-btn">Send message</button>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
