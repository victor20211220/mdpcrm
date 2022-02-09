<link rel="stylesheet" href="/assets/responsive/css/select2.min.css" type="text/css" />


<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>


<style>
  .table {
        width: 200px;
        margin-bottom: 20px;
        float: left;
    }

.table > tbody > tr > td, .table > tfoot > tr > td {
    height:42px;
}
.table_script_between{
    width: 20px;
    margin-bottom: 20px;
    float: left;
}
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 200px; }
  #sortable td { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; }
  html>body #sortable td { height: 42px; line-height: 1.2em; width:200px }
  .ui-state-highlight { height: 42px; line-height: 1.2em; }

  .import_to_update_table > tbody > tr > td{
    padding:1px;
    border-top: 1px solid #eaeff0;
    font-size:12px;
}
  .import_to_update_table > thead > tr > td{
    padding:3px;
    border-top: 1px solid #eaeff0;
    font-size:13px;
    font-weight: bolder;
}
 .import_to_update_table{width: 100%;}
.table-responsive {
    padding-bottom: 20px;
}
  </style>
  <script>

 var csv_data={<?php

 $data = unserialize($csv_data);
 $array_piece = array();
foreach($csv_headers as $h_index=>$header_row){


    $aux = array();
    $total_t = 0;

    foreach($data as $row){

              if($total_t>30) continue;

                 $aux[] = '"'.$row[$h_index].'"';
              $total_t++;
    }

    $array_piece[]='"'.$header_row.'":['.implode(',',$aux)."]";
    }

echo implode(',',$array_piece);
?>}

 function go_refresh_table(event, ui){

            var data = "";
            var dataAr = [];
            $("#sortable tr").each(function(i, el){
                var p = $(el).attr('id');
                dataAr.push(p);
                data += p+',';

                if(p=='0')
                for (var j = 0; j < <?php echo count($data);?>; j++) {
                    $("#tr_"+j+" >#td_"+i).html('');
                }
                else

                for (var j = 0; j < csv_data[p].length; j++) {
                    $("#tr_"+j+" >#td_"+i).html(csv_data[p][j]);
                }
            });
            $("#new_world_order").val(dataAr.join(','));

 }

  $(function() {
    $( "#sortable" ).sortable({
      placeholder: "ui-state-highlight",
      stop: function(event, ui) {
              go_refresh_table(event, ui);
        },
        over: function(e, ui) { sortableIn = 1; },
        out: function(e, ui) { sortableIn = 0; },
        beforeStop: function(e, ui) {
            if (sortableIn == 0) {

                 if($('#sortable tr').length>$('#sys_rows tr').length){
                          ui.item.remove();
                     }else{
                          ui.item.closest('tr').attr('id','0');
                          ui.item.closest('tr').children().children().remove();
                     }

            }
        }
    });

    //$( "#sortable" ).disableSelection(data);

  $('.form-imp').submit(function () {

    // Get the Login Name value and trim it
    var m_fields=[<?php
$array_piece=array();
foreach($mysql_cols as $h_index=>$mysql_row){

             if($mysql_row['required']==1)
                $array_piece[]='"'.$mysql_row['name'].'"';
             else
                 $array_piece[]='""';

    }

echo implode(',',$array_piece);
?>]

    ready_for_import = $('#new_world_order').val();
    ready_for_import = ready_for_import.split(",");

for (index = 0; index < m_fields.length; ++index) {
         if(ready_for_import[index]==0 && m_fields[index].length>0)
           {
                alert('Please match <'+m_fields[index]+'> column or add it to your file and restart the proccess.');
             return false;
           }

}

});


$('.rem_this_col').click(function(e){
         e.preventDefault();

         if($('#sortable tr').length>$('#sys_rows tr').length){
              $(this).closest('tr').fadeOut('slow', function(){ $(this).remove();go_refresh_table(null, null); });

         }else{
              $(this).closest('tr').attr('id','0');
              $(this).closest('tr').children().children().fadeOut('slow', function(){ $(this).remove(); go_refresh_table(null, null); });

         }

    })

  });





  </script>

<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?php echo lang('import_clients'); ?></h1>
</div>


<form method="post" class="form-horizontal form-imp" enctype="multipart/form-data">
  <div class="wrapper-md">
   <div class="panel panel-default">
       <div class="panel-heading font-bold">
      <?php echo lang('import_step_2'); ?>
    </div>
    <div class="panel-body">


<div class="row">
    <div class="panel-heading font-bold">
      1:<?php echo lang('step_1_order'); ?>
    </div>
    <br>
    <div class="panel-heading ">
      <?php echo lang('step_1_details'); ?>
    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 col-md-offset-3">
                        <div class="table-responsive">
                            <textarea name='csv_data' style='display:none'><?php echo $csv_data;?></textarea>
                                <input type='hidden' name='csv_headers' value='<?php echo serialize($csv_headers);?>'>
                                <input type='hidden' name='mysql_cols' value='<?php echo serialize($mysql_cols);?>'>
                                <input type='hidden' name='import_has_header' value='<?php echo $import_has_header;?>'>
                                <input type='hidden' name='action_on_duplicate' value='<?php echo $action_on_duplicate;?>'>
                                <input id='new_world_order' type='hidden' name='new_world_order' value=''>

                            <table id='sys_rows' class="table table_script">
                                <thead>
                                    <tr>
                                        <th class="text-right"><span>DB Field</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($mysql_cols as $mysql_row){
                                            $mand = '';
                                            if($mysql_row['required']==1) $mand = '<b style="color:red"><sup>*</sup></d>';
                                            ?>

                                            <tr>
                                                <td class="text-right">
                                                   <?php echo $mysql_row['name'].$mand;?>
                                                </td>
                                            </tr>

                                        <?php } ?>
                                </tbody>
                            </table>
                            <table class="table table_script_between">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span>-</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($mysql_cols as $mysql_row){ ?>
                                            <tr>
                                                <td class="text-right"><i class="fa fa-arrows-h"></i></td>
                                            </tr>
                                        <?php } ?>
                                </tbody>
                            </table>
                                <!-- table redips drag -->
                                <table id="table1" class="table table_script">
                                    <colgroup>
                                        <col width="200">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><?php echo lang('import_field'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable">
                                        <?php

                                        foreach($csv_headers as $header_row){ ?>

                                            <tr id='<?php echo $header_row;?>'>
                                                <td class="text-left">
                                                <div class="label label-warning redips-drag" style="border-style: solid; cursor: move;">
                                                    <?php echo $header_row;?>
                                                </div></td>
                                                <td>
                                                    <span class="pull-right" >
                                                        <a href='' title='Remove this field from the import batch' class='rem_this_col'><i class="fa fa-times fa-fw"></i></a>
                                                     </span>
                                                </td>
                                            </tr>

                                        <?php }

                                        if(count($mysql_cols)>count($csv_headers)){

                                            for($i=0;$i<(count($mysql_cols)-count($csv_headers));$i++){ ?>

                                                <tr id='0'>
                                                    <td class="text-left">
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                         ?>
                                    </tbody>
                                </table>
                                <div id="redips_clone" style="height: 1px; width: 1px;"></div>

                        </div>
                    </div>
                </div>

<?php

//print_r($csv_headers);
//print_r($mysql_cols);
//print_r(unserialize($data));
//print_r($import_has_header);



?>
<div class="row">
    <div class="panel-heading font-bold">
      2: <?php echo lang('step_2_preview'); ?>
    </div>
    </br>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
      <div class="table-responsive">
         <table class="table table-bordered table-condensed no-margin table-striped m-b-none dataTable no-footer import_to_update_table">
            <thead>
                <tr>
                    <?php
                        foreach($mysql_cols as $mysql_row){ ?>
                            <td class="text-left">
                                <?php echo $mysql_row['name'];?>
                            </td>
                    <?php } ?>
                </tr>
            </thead>

            <tbody id='preview_import'>
                <?php

                $data = unserialize($csv_data);
                $total_t=0;
                foreach($data as $k=>$row){
                    if($total_t>30)continue;
                        echo "<tr id='tr_".$k."'>";

                                foreach($mysql_cols as $kk=>$mysql_row){
                                      echo '<td id="td_'.$kk.'" class="text-left">';
                                      echo '</td>';
                                   }

                        echo "</tr>";
                        $total_t++;
                }

                ?>
            </tbody>
     </table>
    </div>
   </div>
</div>

<div class="row">
    <div class="panel-heading font-bold">
        3: <?php echo lang('step_3_import'); ?>
    </div>
    </br>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
      <div class="wrapper bg-white but-wrapper">
            <button type="submit" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1"><i class="fa fa-times"></i> Cancel</button>
            <button type="submit" id="btn-submit" name="btn_submit_2" class="btn btn-info" value="1"><i class="fa fa-check"></i> Add to database</button>
        </div>
   </div>
</div>
        </div>
    </div>
 </div>
</form>

