<div id="headerbar">

    <h1><?= lang('quotes'); ?></h1>

    <div class="pull-right">
        <?= pager(site_url('guest/quotes/status/' . $this->uri->segment(3)), 'Mdl_quotes'); ?>
    </div>

    <div class="pull-right">
        <ul class="nav nav-pills index-options">
            <li <?php if ($status == 'open') { ?>class="active"<?php } ?>><a
                    href="<?= site_url('guest/quotes/status/open'); ?>"><?= lang('open'); ?></a></li>
            <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
                    href="<?= site_url('guest/quotes/status/approved'); ?>"><?= lang('approved'); ?></a>
            </li>
            <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
                    href="<?= site_url('guest/quotes/status/rejected'); ?>"><?= lang('rejected'); ?></a>
            </li>
        </ul>
    </div>

</div>

<div id="content" class="table-content">

    <div id="filter_results">
        <?= $this->layout->load_view('layout/alerts'); ?>

        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?= lang('quote'); ?></th>
                    <th><?= lang('created'); ?></th>
                    <th><?= lang('due_date'); ?></th>
                    <th><?= lang('client_name'); ?></th>
                    <th><?= lang('amount'); ?></th>
                    <th><?= lang('options'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($quotes as $quote) { ?>
                    <tr>
                        <td>
                            <a href="<?= site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                               title="<?= lang('edit'); ?>">
                                <?= $quote->quote_number; ?>
                            </a>
                        </td>
                        <td>
                            <?= date_from_mysql($quote->quote_date_created); ?>
                        </td>
                        <td>
                            <?= date_from_mysql($quote->quote_date_expires); ?>
                        </td>
                        <td>
                            <?= $quote->client_name; ?>
                        </td>
                        <td>
                            <?= format_currency($quote->quote_total); ?>
                        </td>
                        <td>
                            <a href="<?= site_url('guest/quotes/view/' . $quote->quote_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-search"></i>
                                <?= lang('view'); ?>
                            </a>

                            <a href="<?= site_url('guest/quotes/generate_pdf/' . $quote->quote_id); ?>"
                               class="btn btn-default btn-sm">
                                <i class="icon ion-printer"></i>
                                <?= lang('pdf'); ?>
                            </a>
                            <?php if (in_array($quote->quote_status_id, array(2, 3))) { ?>
                                <a href="<?= site_url('guest/quotes/approve/' . $quote->quote_id); ?>"
                                   class="btn btn-success btn-sm">
                                    <i class="glyphicon glyphicon-check"></i>
                                    <?= lang('approve'); ?>
                                </a>
                                <a href="<?= site_url('guest/quotes/reject/' . $quote->quote_id); ?>"
                                   class="btn btn-default btn-sm">
                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                    <?= lang('reject'); ?>
                                </a>
                            <?php } elseif ($quote->quote_status_id == 4) { ?>
                                <a href="#" class="btn btn-success btn-sm disabled"><?= lang('approved'); ?></a>
                            <?php } elseif ($quote->quote_status_id == 5) { ?>
                                <a href="#" class="btn btn-danger btn-sm disabled"><?= lang('rejected'); ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

</div>
