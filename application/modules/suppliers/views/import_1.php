<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?php echo lang('import_clients'); ?></h1>
</div>


<form method="post" class="form-horizontal" enctype="multipart/form-data">
  <div class="wrapper-md">
   <div class="panel panel-default">
       <div class="panel-heading font-bold">
      <?php echo lang('import_step_1'); ?>
    </div>
    <div class="panel-body">
         <?php $this->layout->load_view('layout/alerts'); ?>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                 <label for="invoice_id" class="control-label"><?php echo lang('select_csv'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input ui-jq="filestyle" type="file" data-icon="false" data-classButton="btn btn-default" name="fileUpload" data-classInput="form-control inline v-middle input-s">
            </div>
        </div>
        <div class="form-group">
             <div class="col-lg-offset-2 col-lg-10">
              <div class="radio">
                  <label class="i-checks">
                    <input type="radio" name="duplicate_rec" value="update" checked>
                    <i></i>
                    <?php echo lang('client_imp_1'); ?>
                  </label>
            </div>
            <div class="radio">
                  <label class="i-checks">
                    <input type="radio" name="duplicate_rec" value="ignore">
                    <i></i>
                    <?php echo lang('client_imp_2'); ?>
                  </label>
            </div>
            <span class="help-block m-b-none"><?php echo lang('client_imp_2_help'); ?></span>
           </div>
            </div>
        <div class="form-group">
              <div class="col-lg-offset-2 col-lg-10">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" id="checkbox-3" class="checkbox11" name="import_has_header" checked='checked' value="1"><i></i><?php echo lang('f_row_is_header'); ?><br>
                           <span><?php echo lang('if_checked_ignore'); ?></span>
                  </label>
                </div>
              </div>
            </div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">

            </div>
            <div class="col-xs-12 col-sm-6">
                <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1"><i class="fa fa-times"></i> <?php echo lang('cancel'); ?></button>
                <button type="submit" id="btn-submit" name="btn_submit_1" class="btn btn-info" value="1"><i class="fa fa-check"></i> <?php echo lang('next_step'); ?></button>
            </div>
        </div>
        </div>
    </div>
 </div>
</form>

