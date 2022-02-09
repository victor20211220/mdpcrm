<div class="">
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <div class="col-sm-12">
                      <div class="table-responsive">
                        <table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer" id="DataTables_Table_0"
                        role="grid" aria-describedby="DataTables_Table_0_info" >
                            <thead>
                                <tr role="row">
                                    <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0"
                                    rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending"
                                    aria-sort="ascending"><?php echo lang('supplier_name'); ?></th>
                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="Browser: activate to sort column ascending"><?php echo lang('email_address'); ?></th>
                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="Platform(s): activate to sort column ascending"><?php echo lang('phone_number'); ?></th>
                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="Engine version: activate to sort column ascending"><?php echo lang('balance'); ?></th>
                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="CSS grade: activate to sort column ascending"><?php echo lang('active'); ?></th>
                                    <th width="10%" class="min_width_160" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"
                                    colspan="1" aria-label="CSS grade: activate to sort column ascending" ><?php echo lang('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php $class='odd'; foreach ($records as $supplier) {  $class = ($class=='odd')?'even':'odd';    ?>
                                        <tr role="row" class="<?php echo $class;?>">
                                            <td class="sorting_1"><?php echo anchor('suppliers/view/' . $supplier->supplier_id, $supplier->supplier_name); ?></td>
                                            <td><?php echo $supplier->supplier_email; ?></td>
                                            <td><?php echo(($supplier->supplier_phone ? $supplier->supplier_phone : ($supplier->supplier_mobile ? $supplier->supplier_mobile : ''))); ?></td>
                                            <td style="text-align: right;"><?php echo format_currency($supplier->supplier_invoice_balance); ?></td>
                                            <td><?php echo ($supplier->supplier_active) ? lang('yes') : lang('no'); ?></td>
                                            <td>
                                                <div class="options btn-group">
                                                    <a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                                        <i class="fa fa-cog blueheader"></i> <?php echo lang('options'); ?>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="<?php echo site_url('suppliers/view/' . $supplier->supplier_id); ?>">
                                                                <i class="fa fa-eye fa-margin"></i> <?php echo lang('view'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="<?php echo site_url('suppliers/form/' . $supplier->supplier_id); ?>">
                                                                <i class="fa fa-pencil fa-margin"></i> <?php echo lang('edit'); ?>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a href="#" class="supplier-create-invoice"
                                                               data-supplier-id="<?php echo $supplier->supplier_id; ?>">
                                                                <i class="fa fa-file-text fa-margin"></i> <?php echo lang('create_received_invoice'); ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="<?php echo site_url('suppliers/delete/' . $supplier->supplier_id); ?>"
                                                               onclick="return confirm('<?php echo lang('delete_supplier_warning'); ?>');">
                                                                <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                        </table>
                      </div>
                    </div>
            </div>
        </div>
    </div>
</div>




