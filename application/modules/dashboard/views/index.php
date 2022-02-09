<?php if ($display_dashboard == true) : ?>

<div id="content" style="padding-top: 15px;">
    <?= $this->layout->load_view('layout/alerts'); ?>
    <? $overdue_invoices_total = 0; ?>
    <div class="row" style="margin-top: 15px;">

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('dash_total_rev'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/6.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?= format_currency($invoiceStatusTotalsThis[4]['sum_total']); ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?= format_currency($invoiceStatusTotalsPast[4]['sum_total']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('dash_avrg_inv'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/2.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?php
                                if (($invoiceStatusTotalsThis[4]['num_total'] + $invoiceStatusTotalsThis[2]['num_total']) != 0)
                                    echo format_currency(
                                            ($invoiceStatusTotalsThis[2]['sum_total'] + $invoiceStatusTotalsThis[4]['sum_total']) /
                                            ($invoiceStatusTotalsThis[2]['num_total'] + $invoiceStatusTotalsThis[4]['num_total'])
                                    );
                                else
                                    echo format_currency(0);
                                ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?php
                                if (($invoiceStatusTotalsThis[4]['num_total'] + $invoiceStatusTotalsThis[2]['num_total']) != 0)
                                    echo format_currency(
                                            ($invoiceStatusTotalsThis[2]['sum_total'] + $invoiceStatusTotalsThis[4]['sum_total']) /
                                            ($invoiceStatusTotalsThis[2]['num_total'] + $invoiceStatusTotalsThis[4]['num_total'])
                                    );
                                else
                                    echo format_currency(0);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('dash_outstanding'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/5.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?= format_currency($overdue_total); ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?= format_currency($overdue_total); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('tasks_in_progress'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/1.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?= $tasks; ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?= $tasksPast; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('tasks_not_started'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/3.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?= $tasksNotStarted; ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?= $tasksNotStartedPast; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default no-right-border-radius">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <span class="spandashboardtitle">
                        <?= lang('unpaid_invoices'); ?>
                    </span>
                </div>
                <div class="panel-body" style="border-top: inherit !important;">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="/assets/responsive/img/3.png" class="in-image">
                        </div>
                        <div class="col-md-8" style="margin-top: -15px">
                            <span class="spandashboardtotaltitle">THIS MONTH</span><br>
                            <span class="spandashboardtotal">
                                <?= format_currency($unpaid_total); ?>
                            </span>
                            <br>
                            <span class="spandashboardtotalsmalltitle">previous month</span><br>
                            <span class="spandashboardtotalsmall">
                                <?= format_currency($unpaid_total); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row" style="margin-top: 15px;">
        <div class="col-xs-12 col-md-6">
            <div id="panel-quote-overview" class="panel panel-default overview">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <i class="fa fa-bar-chart fa-margin"></i>
                    <span class="spandashboardtitle"><?= lang('quote_overview'); ?></span>
                    <span class="pull-right text-muted in-bold">
                        <?= lang($quote_status_period); ?>
                    </span>
                </div>
                <table class="table table-bordered table-condensed no-margin">
                    <?php foreach ($quote_status_totals as $total) : ?>
                    <tr class="troverview">
                        <td>
                            <a href="<?= site_url($total['href']); ?>">
                                <?= $total['label']; ?> </a>
                        </td>
                        <td class="amount">
                            <span class="">
                                <?= format_currency($total['sum_total']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div id="panel-invoice-overview" class="panel panel-default overview">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <i class="fa fa-bar-chart fa-margin"></i>
                    <span class="spandashboardtitle"><?= lang('invoice_overview'); ?></span>
                    <span class="pull-right text-muted in-bold">
                        <?= lang($invoice_status_period); ?>
                    </span>
                </div>
                <table class="table table-bordered table-condensed no-margin">

                    <?php foreach ($invoiceStatusTotalsThis as $total) : ?>
                    <tr class="troverview">
                        <td>
                            <a href="<?= site_url($total['href']); ?>">
                                <?= $total['label']; ?>
                            </a>
                        </td>
                        <td class="amount">
                            <span class="">
                                <?= format_currency($total['sum_total']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </table>
            </div>
            <?php if (empty($overdue_invoices)) : ?>
            <div class="panel panel-default panel-heading">
                <span class="text-muted"><?= lang('no_overdue_invoices'); ?></span>
            </div>
            <?php else : ?>
            <?php $overdue_invoices_total=0; ?>
            <?php foreach ($overdue_invoices as $invoice) { $overdue_invoices_total +=$invoice->invoice_balance; } ?>
            <div id="panel-invoice-overdue" class="panel panel-danger panel-heading">
                <?= anchor('invoices/status/overdue', '<i class="fa fa-external-link"></i> ' . lang('overdue_invoices'), 'class="text-danger"'); ?>
                <span class="pull-right text-danger">
                    <?= format_currency($overdue_invoices_total); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row padding-fixer">
        <div class="col-xs-12 col-md-6">
            <div id="panel-recent-quotes" class="panel panel-default">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;">
                    <i class="fa fa-history fa-margin"></i>
                    <span class="spandashboardtitle"><?= lang('recent_quotes'); ?></span>
                    <span class="spandashboardtitle pull-right" style="font-size: 14px !important;">
                        <i class="fa fa-fw fa-angle-down blueheader"></i>
                        <?= anchor('quotes/status/all', lang('view_all')); ?>
                    </span>
                </div>
                <div class="table-responsive">
                  <table class="table table-striped table-condensed no-margin" role="grid">
                        <thead>
                            <tr>
                                <th style="min-width: 20%"><?= lang('status'); ?></th>
                                <th style="min-width: 15%;"><?= lang('date'); ?></th>
                                <th style="min-width: 35%;"><?= lang('client'); ?></th>
                                <th style="text-align: right;"><?= lang('balance'); ?></th>
                                <th><?= lang('pdf'); ?></th>
                            </tr>
                        </thead>
                      <tbody>
                      <?php foreach ($quotes as $q) : ?>
                          <?php if ($this->db->get_where('ip_quote_items', ['quote_id' => $q->quote_id])->num_rows() > 0) : ?>
                              <tr>
                                  <td>
                                      <?php if ($quote_statuses[$q->quote_status_id]['label'] == 'Draft') : ?>
                                          <i class="fa fa-circle yellow"></i>
                                      <?php endif; ?>
                                      <?php if ($quote_statuses[$q->quote_status_id]['label'] == 'Approved') : ?>
                                          <i class="fa fa-circle green"></i>
                                      <?php endif; ?>
                                      <?php if ($quote_statuses[$q->quote_status_id]['label'] == 'Sent') : ?>
                                          <i class="fa fa-circle green"></i>
                                      <?php endif; ?>
                                      <?= $quote_statuses[$q->quote_status_id]['label']; ?>
                                  </td>
                                  <td>
                                      <?= date_from_mysql($q->quote_date_created); ?> </td>
                                  <td>
                                      <?= anchor('clients/view/' . $q->client_id, $q->client_name); ?> </td>
                                  <td class="amount">
                                      <?= format_currency($q->quote_total); ?> </td>
                                  <td style="text-align: center;">
                                      <a href="<?= site_url('quotes/generate_pdf/' . $q->quote_id); ?>"
                                         title="<?= lang('download_pdf'); ?>" target="_blank">
                                         <i class="fa fa-file-pdf-o"></i>
                                      </a>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      <?php endforeach; ?>

                      </tbody>
                  </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div id="panel-recent-invoices" class="panel panel-default">
                <div class="panel-heading" style="border-top: 2px solid limegreen !important;"> <i class="fa fa-history fa-margin"></i>
                    <span class="spandashboardtitle"><?= lang('recent_invoices'); ?></span>
                    <span class="spandashboardtitle pull-right" style="font-size: 14px !important;">
                        <i class="fa fa-fw fa-angle-down"></i>
                        <?= anchor('invoices/status/all', lang('view_all')); ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed no-margin" class="table table-striped m-b-none no-footer" role="grid">
                        <thead>
                            <tr>
                                <th style="min-width: 20%;"><?= lang('status'); ?></th>
                                <th style="min-width: 15%;"><?= lang('due_date'); ?></th>
                                <th style="min-width: 35%;"><?= lang('client'); ?></th>
                                <th style="text-align: right;"><?= lang('balance'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice) : ?>
                            <?php if ($this->config->item('disable_read_only') == true) { $invoice->is_read_only = 0; } ?>
                            <?php if ($this->db->get_where('ip_invoice_items', array('invoice_id' => $invoice->invoice_id))->num_rows() > 0) : ?>
                            <tr>
                                <td>
                                    <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Draft') : ?>
                                        <i class="fa fa-circle" style="color: #f05050"></i>
                                    <?php endif; ?>
                                    <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Sent') : ?>
                                        <i class="fa fa-circle" style="color: #21a8e0"></i>
                                    <?php endif; ?>
                                    <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Paid') : ?>
                                        <i class="fa fa-circle green"></i>
                                    <?php endif; ?>
                                    <?php if ($invoice_statuses[$invoice->invoice_status_id]['label'] == 'Viewed'): ?>
                                        <i class="fa fa-circle yellow"></i>
                                    <?php endif; ?>
                                    <?= $invoice_statuses[$invoice->invoice_status_id]['label']; ?>
                                </td>
                                <td>
                                    <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                        <?= date_from_mysql($invoice->invoice_date_due); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= anchor('clients/view/' . $invoice->client_id, $invoice->client_name); ?>
                                </td>
                                <td class="amount">
                                    <?= format_currency($invoice->invoice_balance * $invoice->invoice_sign); ?>
                                    </td>
                                <td style="text-align: center;">
                                    <a href="<?= "/invoices/generate_pdf/{$invoice->invoice_id}"; ?>" title="<?= lang('download_pdf'); ?>" target="_blank">
                                        <span class="label label-success">PDF</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<?php endif ?>
