<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('import_payments'); ?></h1>
</div>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
    <div class="">
        <div class="panel panel-default">
            <div class="panel-heading font-bold">
                <?= lang('import_step_2'); ?>
            </div>
            <div class="panel-body">
                <?php $this->layout->load_view('layout/alerts'); ?>
                <div class="form-group">
                    <div class="table-responsive" style='padding-bottom:20px;'>
                        <table class="table table-striped b-t b-light">
                            <thead>
                            <tr>
                                <th><?= lang('client_name'); ?></th>
                                <th><?= lang('from_iban'); ?></th>
                                <th><?= lang('payment_description'); ?></th>
                                <th><?= lang('amount'); ?></th>
                                <th><?= lang('invoice'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($payments as $payment) { ?>
                                <tr>

                                    <input type='hidden' name='client_name[]' value='<?= $payment['client_name']; ?>'>
                                    <input type='hidden' name='IBAN[]' value='<?= $payment['IBAN']; ?>'>
                                    <input type='hidden' name='description[]' value='<?= $payment['description']; ?>'>
                                    <input type='hidden' name='amount[]' value='<?= $payment['amount']; ?>'>

                                    <td><?= $payment['client_name'] ?></td>
                                    <td><?= $payment['IBAN'] ?></td>
                                    <td><?= $payment['description'] ?></td>
                                    <td><?= $payment['amount'] ?></td>
                                    <td>
                                        <select style="max-width:250px" name='invoice_id[]'>
                                            <option value="-1">Select invoice</option>
                                            <?php foreach ($invoices as $invoice) : ?>
                                                <?php $selected = (strpos($payment['description'], $invoice->invoice_number) !== false) ?
                                                    ' selected ' : '';
                                                ?>
                                                <option <?= $selected; ?> value="<?= $invoice->invoice_id; ?>">
                                                    <?= $invoice->invoice_number . ' - ' . $invoice->client_name . ' - ' . format_currency($invoice->invoice_balance); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1">
                            <i class="fa fa-times"></i><?= lang('cancel'); ?>
                        </button>
                        <button type="submit" id="btn-submit" name="btn_submit_2" class="btn btn-info" value="1">
                            <i class="fa fa-check"></i><?= lang('next_step'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

