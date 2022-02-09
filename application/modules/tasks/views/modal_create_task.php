<script type="text/javascript">
    //ehay nahuy 2
    $(function () {

        // Display the create task modal

        $('#create-task').modal('show');

        $('#create-task').on('shown', function () {
            $("#client_name").focus();
        });


        $().ready(function () {

            $("[name='client_name']").select2({

                createSearchChoice: function (term, data) {

                    if ($(data).filter(function () {

                            return this.text.localeCompare(term) === 0;

                        }).length === 0) {

                        return {id: term, text: term};

                    }

                },

                multiple: false,

                allowClear: true,

                placeholder: 'Search by client name or type new client and press enter ...',

                initSelection: function(element, callback) {



                	<?php if ($client_id){ ?>



			        var data = {id: '<?php echo html_escape($client_id);?>', text: '<?php echo html_escape($client_name);?>'};

					callback(data);



					<?php } ?>

			    },

                data: [

                    <?php

                    $i=0;

                    foreach ($clients as $client){

                        echo "{

                        id: '".str_replace("'","\'",$client->client_id)."',

                        text: '".str_replace("'","\'",$client->client_name)."',

                        client_reg_number: '".str_replace("'","\'",$client->client_reg_number)."',

                        client_address_1: '".str_replace("'","\'",$client->client_address_1)."',

                        client_vat_id: '".str_replace("'","\'",$client->client_vat_id)."'

                        }";

                        if (($i+1) != count($clients)) echo ',';

                        $i++;

                    }

                    ?>

                ]

            });



            $("[name='client_name']").on("select2-selecting", function (e) {



            	if(e.choice.client_vat_id == undefined){



            		    //$('#new_client_on_task').val(1);



            		    $('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");;

	            		$('#client_id').val('-1');

	            		$('#client_reg_number').val('').prop('disabled', false).attr("placeholder", "Type value here");;

	            		$('#client_address_1').val('').prop('disabled', false).attr("placeholder", "Type value here");;

	            		$('#client_vat_id').val('').prop('disabled', false).attr("placeholder", "Type value here");;



            		    $("#client_reg_number").focus();





            	}else{

            		    //$('#new_client_on_task').val(0);



            		    $('#client_id').val(e.choice.id);





	            		$('#client_name_input').val(e.choice.text).prop('disabled', true).attr("placeholder", "");



	            		if(e.choice.client_reg_number=='') $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', false).attr("placeholder", "Type value here");

	            									 else $('#client_reg_number').val(e.choice.client_reg_number).prop('disabled', true).attr("placeholder", "");



	            		if(e.choice.client_address_1=='') $('#client_address_1').val(e.choice.client_address_1).prop('disabled', false).attr("placeholder", "Type value here");

	            									 else $('#client_address_1').val(e.choice.client_address_1).prop('disabled', true).attr("placeholder", "");



	            		if(e.choice.client_vat_id=='') $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', false).attr("placeholder", "Type value here");

	            									 else $('#client_vat_id').val(e.choice.client_vat_id).prop('disabled', true).attr("placeholder", "");







            	}



            	});



          if($('#client_reg_number').val()=='') $('#client_reg_number').prop('disabled', false).attr("placeholder", "Type value here");

	            						   else $('#client_reg_number').prop('disabled', true).attr("placeholder", "");



	      if($('#client_address_1').val()=='') $('#client_address_1').prop('disabled', false).attr("placeholder", "Type value here");

	            									 else $('#client_address_1').prop('disabled', true).attr("placeholder", "");



	      if($('#client_vat_id').val()=='') $('#client_vat_id').prop('disabled', false).attr("placeholder", "Type value here");

	            									 else $('#client_vat_id').prop('disabled', true).attr("placeholder", "");





            $('#client_name_input').prop('disabled', true).attr("placeholder", "");



            $("#client_name").focus();



        });



        // Creates the task

        $('#task_create_confirm').click(function () {

            // Posts the data to validate and create the task;

            // will create the new client if necessar

            $.post("<?php echo site_url('tasks/ajax/create'); ?>", {



                    client_id: $('#client_id').val(),

                    client_name: $('#client_name').val(),

                    client_reg_number: $('#client_reg_number').val(),

                    client_address_1: $('#client_address_1').val(),

                    client_vat_id: $('#client_vat_id').val(),

                    task_name: $('#task_name').val(),

                    task_finish_date: $('#task_finish_date').val(),
                    task_status: $('#task_status').val(),

                    task_date_created:'',

                    task_description: $('#task_description').val(),

                    user_id: '<?php echo $this->session->userdata('user_id'); ?>'

                },

                function (data) {

                    var response = JSON.parse(data);

                    if (response.success == '1') {

                        // The validation was successful and task was created

                        window.location = "<?php echo site_url('tasks/assign'); ?>";



                    }

                    else {

                        // The validation was not successful

                        //$('.control-group').removeClass('has-error');

                        $('.form-group').removeClass('has-error');

                        for (var key in response.validation_errors) {

                            $('#' + key).parent().addClass('has-error');

                        }

                    }

                });

        });

    });
</script>



<div id="create-task" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3"

     role="dialog" aria-labelledby="modal_create_task" aria-hidden="true">

    <form class="modal-content">

        <div class="modal-header">

            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>


            <h3 class="blueheader"><?php echo lang('create_task'); ?></h3>

        </div>

        <div class="modal-body row">

            <input class="hidden" name='new_client_on_task' id="new_client_on_task" value='0'>

            <input class="hidden" name='client_id' id="client_id" <?php if ($client_id) echo 'value="' . html_escape($client_id) . '"'; ?>>

            <input class="hidden" id="payment_method_id"

                   value="<?php echo $this->Mdl_settings->setting('task_default_payment_method'); ?>">



          <div class="col-md-6">

            <div class="form-group">

                <label for="client_name"><?php echo lang('client'); ?></label>

                <input type="text" name="client_name" id="client_name" class="form-control"

                       autofocus="autofocus"

                    <?php if ($client_name) echo 'value="' . html_escape($client_name) . '"'; ?>>

            </div>

            <div class="form-group">

                <label for="client_name_input"><?php echo lang('client_name'); ?></label>

                <input type="text" name="client_name_input" id="client_name_input" class="form-control"

                       <?php if ($client_name) echo 'value="' . html_escape($client_name) . '"'; ?> >

            </div>

            <div class="form-group">

                <label for="client_reg_number"><?php echo lang('client_reg_number'); ?></label>

                <input type="text" name="client_reg_number" id="client_reg_number" class="form-control"

                       <?php if ($client_reg_number) echo 'value="' . html_escape($client_reg_number) . '"'; ?>>

            </div>

            <div class="form-group">

                <label for="client_address_1"><?php echo lang('street_address'); ?></label>

                <input type="text" name="client_address_1" id="client_address_1" class="form-control"

                       <?php if ($client_address_1) echo 'value="' . html_escape($client_address_1) . '"'; ?>>

            </div>

            <div class="form-group">

                <label for="client_vat_id"><?php echo lang('vat_id'); ?></label>

                <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"

                       <?php if ($client_vat_id) echo 'value="' . html_escape($client_vat_id) . '"'; ?>>

            </div>

          </div>

          <div class="col-md-6">

            <div class="form-group">

                <label for="task_password"><?php echo lang('task_name'); ?></label>

                <input type="text" name="task_name" id="task_name" class="form-control"

                       value="" style="margin: 0 auto;" autocomplete="off">

            </div>

            <div class="form-group">

                 <label class="control-label"><?php echo lang('task_description'); ?>: </label>

                 <textarea name="task_description" id="task_description" class="form-control" style="height: 100px;"></textarea>

            </div>

            <div class="form-group has-feedback">

                <label><?php echo lang('task_finish_date'); ?></label>



                <div class="input-group">
                    <input name="task_finish_date" id="task_finish_date" class="form-control datepicker" value="<?php echo date(date_format_setting()); ?>">
	                <label for="task_finish_date" class="input-group-btn">
		                <span class="btn btn-default"><i class="fa fa-calendar fa-fw"></i></span>
	                </label>
                </div>

            </div>

            <div class="form-group">

                  <label class="control-label"><?php echo lang('status'); ?>: </label>

                  <select name="task_status" id="task_status" class="form-control">

                                <?php foreach ($task_statuses as $key => $status) { ?>

                                    <option value="<?php echo $key; ?>" ><?php echo $status['label']; ?></option>

                                <?php } ?>

                  </select>

           </div>

        </div>

      </div>

        <div class="modal-footer">


            <div class="pull-right">
                <button class="btn btn-gray padder" type="button" data-dismiss="modal">
                    <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success padder ajax-loader" id="task_create_confirm" type="button">
                    <?php echo lang('create_task'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
