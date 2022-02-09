<div class="bg-light lter b-b wrapper-md">
	<h1 class="m-n font-thin h3"><?php echo lang('invoice_aging'); ?></h1>
</div>

<div class="">

	<?php $this->layout->load_view('layout/alerts'); ?>
	<div id="report_options" class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<i class="fa fa-file-pdf-o"></i>
				<?php echo lang('report_options'); ?>
			</h3>
		</div>
		<div class="panel-body">
			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" target="_blank">

				<div class="form-group">
					<input type="submit" class="btn btn-success"
					       name="btn_submit" value="<?php echo lang('invoice_aging'); ?>">
				</div>

			</form>
		</div>

	</div>
</div>
