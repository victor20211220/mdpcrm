<div class="app-content-body ">

         <div class="bg-light lter b-b wrapper-md menu-header-page">
   <div class="row">
     <div style="margin-bottom:5px !important" class="col-sm-3 col-xs-12 custom-auto-width-submenu-slv">
		<h1 class="m-n font-thin h3">Email Details</h1>
    </div>

   </div>
</div>





    <div id="filter_results">
        <div class="">
	<div class="panel panel-default">
		<div class="panel-body">
      <p><b><?php echo lang('email_from'); ?>:</b> <?php echo $data['from_email']; ?></p>
      <p><b><?php echo lang('email_date'); ?>:</b> <?php echo $data['date']; ?></p>
      <br />
      <h3><?php echo $data['subject']; ?></h3>
      <p><?php echo $data['content']; ?></p>
      <p><a class="btn btn-sm btn-success" href="<?php echo site_url('clients/view/'.$data['client_id'].'#tab_tab7'); ?>">
		            <i class="fa fa-arrow-left"></i> Back		 </a>
		</div>
	</div>
</div>




    </div>

    </div>
