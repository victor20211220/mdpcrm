<div class=" lter wrapper-md menu-header-page">

	<div class="row">

		<div class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">

			<h1 class="m-n font-thin h3"><?php echo lang('payment_methods'); ?></h1>

		</div>

		<div class="col-sm-3 col-xs-12">

			<a class="btn btn-sm btn-success" href="<?php echo site_url('payment_methods/form'); ?>">

				<i class="fa fa-plus"></i> <?php echo lang('new'); ?>

			</a>

		</div>

	</div>

</div>

<?php $this->layout->load_view('layout/alerts'); ?>

<div class="table-responsive">

	<div class="panel panel-default">

		<div id="DataTables_Table_0_wrapper"
		     class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive" style="">

			<div class="col-sm-12">

				<table ui-jq="dataTable" class="table table-striped m-b-none dataTable no-footer"
				       id="DataTables_Table_0"

				       role="grid" aria-describedby="DataTables_Table_0_info">

					<thead>

					<tr>

						<th width="100%" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"

						    colspan="1"
						    aria-label="Browser: activate to sort column ascending"><?php echo lang('payment_method'); ?></th>

						<th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1"

						    colspan="1"
						    aria-label="Browser: activate to sort column ascending"><?php echo lang('options'); ?></th>

					</tr>

					</thead>

					<tbody>

					<?php $class = 'odd';
					foreach ($payment_methods as $payment_method) {
						$class = ($class == 'odd') ? 'even' : 'odd'; ?>

						<tr role="row" class="<?php echo $class; ?>">

							<td><?php echo $payment_method->payment_method_name; ?></td>

							<td>

								<div class="options btn-group">

									<a class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" href="#">

										<i class="fa fa-cog blueheader"></i>

										<?php echo lang('options'); ?>

									</a>

									<ul class="dropdown-menu">

										<li>

											<a href="<?php echo site_url('payment_methods/form/' . $payment_method->payment_method_id); ?>">

												<i class="fa fa-pencil fa-margin"></i>

												<?php echo lang('edit'); ?>

											</a>

										</li>

										<li>

											<a href="<?php echo site_url('payment_methods/delete/' . $payment_method->payment_method_id); ?>"

											   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">

												<i class="fa fa-trash-o fa-margin"></i>

												<?php echo lang('delete'); ?>

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

			<br clear="all">
		</div>

	</div>

</div>

<!--</div>-->
