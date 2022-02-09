<div class="bg-light lter b-b wrapper-md">
	  <h1 class="m-n font-thin h3"><?php echo lang('sales_by_client'); ?></h1>
	</div>

 <div class="panel panel-default">
	<div class="panel-body">
	   <div id="report_options" class="panel panel-default">
	        <div class="panel-heading">
	            <h3 class="panel-title">
	                <i class="fa fa-file-pdf-o"></i>
	                <?php echo lang('report_options'); ?>
	            </h3>
	        </div>
	        <div class="panel-body">
	            <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" target="_blank">

                <div class="row">
	                <div class="col-xs-12 col-sm-3">
		                <label for="from_date">
			                <?php echo lang('from_date'); ?>
		                </label>

		                <div class="input-group">
			                <input name="from_date" id="from_date" class="form-control datepicker" required>
			                <label for="from_date" class="input-group-btn">
				                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
			                </label>
		                </div>
	                </div>

	                <div class="col-xs-12 col-sm-3">
		                <label for="to_date">
			                <?php echo lang('to_date'); ?>
		                </label>

		                <div class="input-group">
			                <input name="to_date" id="to_date" class="form-control datepicker" required>
			                <label for="to_date" class="input-group-btn">
				                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
			                </label>
		                </div>
	                </div>
                </div>

	            <br>

                <input type="submit" class="btn btn-success" name="btn_submit" value="<?php echo lang('run_report'); ?>">
            </form>
	        </div>
	    </div>
    </div>
</div>
