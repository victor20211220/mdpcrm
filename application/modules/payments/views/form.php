<script type="text/javascript">
    $(function () {
        $('#invoice_id').focus();

        amounts = JSON.parse('<?= $amounts; ?>');
        invoice_payment_methods = JSON.parse('<?= $invoice_payment_methods; ?>');
        $('#invoice_id').change(function () {
            var invoice_identifier = "invoice" + $('#invoice_id').val();
            $('#payment_amount').val(amounts[invoice_identifier]);
            $('#payment_method_id option[value="' + invoice_payment_methods[invoice_identifier] + '"]').prop('selected', true);
            if (invoice_payment_methods[invoice_identifier] != 0) {
                $('#payment_method_id').prop('disabled', true);
            } else {
                $('#payment_method_id').prop('disabled', false);
            }
            ;
        });

    });
</script>
<style>
    .custom-select {
        position: relative;
        font-family: Arial;
        border: 1px solid lightgrey;
        border-radius: 6px;
        height: 30px;
        padding: 2px;
    }
    .custom-select select {
        display: none;
    }
    .select-selected {
        background-color: white;
        padding:4px !important;
    }
    .select-selected:after {
        position: absolute;
        content: "";
        top: 14px;
        right: 10px;
        width: 0;
        height: 0;
        border: 6px solid transparent;
        border-color: darkslategrey transparent transparent transparent;
    }
    .select-selected.select-arrow-active:after {
        border-color: transparent transparent darkslategrey transparent;
        top: 7px;
    }
    .select-items div,.select-selected {
        color: darkslategrey;
        padding: 0px;
        height:24px;
        cursor: pointer;
        user-select: none;
    }
    .option_style{
        float: left;
        font-size:10px;
        text-indent : 10px;
    }
    /*style items (options):*/
    .select-items {
        position: absolute;
        background-color: white;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 99;
    }
    /*hide the items when the select box is closed:*/
    .select-hide {
        display: none;
    }
    .select-items div:hover, .same-as-selected {
        background-color: rgba(0, 0, 0, 0.1);
    }
</style>
<form method="post" class="form-horizontal">
    <div class="lter wrapper-md">
        <h1 class="m-n font-thin h3"><?= lang('payment_form'); ?></h1>
    </div>
    <?php if ($payment_id) { ?>
        <input type="hidden" name="payment_id" value="<?= $payment_id; ?>">
    <?php } ?>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">

                <?= $this->layout->load_view('layout/alerts'); ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="invoice_id" class="control-label">
                            <?= lang('invoice'); ?>
                            <i style="color:red">*</i>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="custom-select">
                            <select name="invoice_id" id="invoice_id">
                                <?php if (!$payment_id) : ?>
                                    <option value=""></option>
                                    <?php foreach ($open_invoices as $invoice) : ?>
                                        <option value="<?= $invoice->invoice_id; ?>"
                                                <?= $this->Mdl_payments->form_value('invoice_id') == $invoice->invoice_id ? 'selected="selected"' : ""; ?>
                                        >
                                            <?= "{$invoice->invoice_number} - {$invoice->client_name} - " . format_currency($invoice->invoice_balance); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="<?= $payment->invoice_id; ?>">
                                        <?= "{$payment->invoice_number} - {$payment->client_name} - " . format_currency($payment->invoice_balance); ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="payment_date" class="control-label"><?= lang('date'); ?></label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <input name="payment_date" id="payment_date" class="form-control datepicker"
                                   value="<?= date_from_mysql($this->Mdl_payments->form_value('payment_date')); ?>">
                            <label for="payment_date" class="input-group-btn">
                                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="payment_amount" class="control-label">
                            <?= lang('payment_amount'); ?>
                            <i style="color:red">*</i>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                               value="<?= format_amount($this->Mdl_payments->form_value('payment_amount')); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="payment_method_id" class="control-label">
                            <?= lang('payment_method'); ?>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">

                        <select id="payment_method_id" name="payment_method_id" class="form-control"
                            <?= ($this->Mdl_payments->form_value('payment_method_id') ? 'readonly="readonly"' : ''); ?>>

                            <?php foreach ($payment_methods as $payment_method) : ?>
                                <option value="<?= $payment_method->payment_method_id; ?>"
                                        <?php if ($this->Mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>
                                >
                                    <?= $payment_method->payment_method_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="payment_note" class="control-label"><?= lang('note'); ?></label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <textarea name="payment_note" class="form-control"><?= $this->Mdl_payments->form_value('payment_note'); ?></textarea>
                    </div>
                </div>

                <?php foreach ($custom_fields as $custom_field) : ?>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                            <label><?= $custom_field->custom_field_label; ?>: </label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <input type="text" name="custom[<?= $custom_field->custom_field_column; ?>]"
                                   id="<?= $custom_field->custom_field_column; ?>"
                                   class="form-control"
                                   value="<?= html_escape($this->Mdl_payments->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                        <ul class="nav nav-pills nav-sm">
                            <?php $this->layout->load_view('layout/header_buttons'); ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    var x, i, j, selElmnt, a, b, c;

    x = document.getElementsByClassName("custom-select");
    for (i = 0; i < x.length; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];

        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;

        if(a.innerHTML == "") {
            a.innerHTML = "Please Select";
        }
        x[i].appendChild(a);
        /*for each element, create a new DIV that will contain the option list:*/
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");

        for (j = 1; j < selElmnt.length; j++) {
            /*for each option in the original select element,
            create a new DIV that will act as an option item:*/
            c = document.createElement("DIV");

            targetOption = selElmnt.options[j].innerHTML;
            targetArray = targetOption.split(" - ");

            first_elem = "<div class='option_style' style='width:20% !important;'>" + targetArray[0] + "</div>";
            secnd_elem = "<div class='option_style' style='width:60% !important;'>" + targetArray[1] + "</div>";
            third_elem = "<div class='option_style' style='width:20% !important;'>" + targetArray[2] + "</div>";


            c.innerHTML = first_elem + secnd_elem + third_elem;

            c.addEventListener("click", function (e) {
                /*when an item is clicked, update the original select box,
                and the selected item:*/
                var y, i, k, s, h;
                s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                h = this.parentNode.previousSibling;
                for (i = 0; i < s.length; i++) {
                    if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        y = this.parentNode.getElementsByClassName("same-as-selected");
                        for (k = 0; k < y.length; k++) {
                            y[k].removeAttribute("class");
                        }
                        this.setAttribute("class", "same-as-selected");
                        break;
                    }
                }
                h.click();
            });
            b.appendChild(c);
        }
        x[i].appendChild(b);
        a.addEventListener("click", function (e) {
            e.stopPropagation();
            closeAllSelect(this);
            this.nextSibling.classList.toggle("select-hide");
            this.classList.toggle("select-arrow-active");
        });
    }

    function closeAllSelect(elmnt) {

        var x, y, i, arrNo = [];
        x = document.getElementsByClassName("select-items");
        y = document.getElementsByClassName("select-selected");
        for (i = 0; i < x.length; i++) {
            if (arrNo.indexOf(i)) {
                x[i].classList.add("select-hide");
            }
        }
    }

    $(".select-items div").click(function(){
        var index = $(".select-items div").not(".option_style").index(this);
        if(index != -1){
            index = parseInt(index) + 1;
            $('#invoice_id').val($('#invoice_id option:eq('+index+')').attr('value'));
        }
        $(".select-selected").html($(this).html());
    });

    document.addEventListener("click", closeAllSelect);
</script>
