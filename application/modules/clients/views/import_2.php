<link rel="stylesheet" href="<?= base_url(); ?>assets/responsive/css/select2.min.css" type="text/css"/>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<style>
    .table {
        width: 200px;
        margin-bottom: 20px;
        float: left;
    }

    .table > tbody > tr > td, .table > tfoot > tr > td {
        height: 42px;
    }

    .table_script_between {
        width: 20px;
        margin-bottom: 20px;
        float: left;
    }

    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 200px;
    }

    #sortable td {
        margin: 0 5px 5px 5px;
        padding: 5px;
        font-size: 1.2em;
        height: 1.5em;
    }

    html > body #sortable td {
        height: 42px;
        line-height: 1.2em;
        width: 200px
    }

    .ui-state-highlight {
        height: 42px;
        line-height: 1.2em;
    }

    .import_to_update_table > tbody > tr > td {
        padding: 1px;
        border-top: 1px solid #eaeff0;
        font-size: 12px;
    }

    .import_to_update_table > thead > tr > td {
        padding: 3px;
        border-top: 1px solid #eaeff0;
        font-size: 13px;
        font-weight: bolder;
    }

    .import_to_update_table {
        width: 100%;
    }

    .table-responsive {
        padding-bottom: 20px;
    }
</style>
<script>

    var csv_data = {<?php

        $data = unserialize($csv_data);
        $array_piece = [];
        foreach ($csv_headers as $h_index => $header_row) {


            $aux = [];
            $total_t = 0;

            foreach ($data as $row) {

                if ($total_t > 30) {
                    continue;
                }

                $aux[] = '"' . $row[$h_index] . '"';
                $total_t++;
            }

            $array_piece[] = '"' . $header_row . '":[' . implode(',', $aux) . "]";
        }

        echo implode(',', $array_piece);
        ?>}


    function go_refresh_table(event, ui) {

        var data = "";
        var dataAr = [];
        $("#sortable tr").each(function (i, el) {
            var p = $(el).attr('id');
            dataAr.push(p);
            data += p + ',';

            if (p == '0')
                for (var j = 0; j < <?= count($data);?>; j++) {
                    $("#tr_" + j + " >#td_" + i).html('');
                }
            else

                for (var j = 0; j < csv_data[p].length; j++) {
                    $("#tr_" + j + " >#td_" + i).html(csv_data[p][j]);
                }
        });
        $("#new_world_order").val(dataAr.join(','));

    }

    $(function () {
        $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            stop: function (event, ui) {
                go_refresh_table(event, ui);
            },
            over: function (e, ui) {
                sortableIn = 1;
            },
            out: function (e, ui) {
                sortableIn = 0;
            },
            beforeStop: function (e, ui) {
                if (sortableIn == 0) {
                    if ($('#sortable tr').length > $('#sys_rows tr').length) {
                        ui.item.remove();
                    } else {
                        ui.item.closest('tr').attr('id', '0');
                        ui.item.closest('tr').children().children().remove();
                    }
                }
            }
        });

        //$( "#sortable" ).disableSelection(data);

        $('.form-imp').submit(function () {

            // Get the Login Name value and trim it
            var m_fields = [<?php
                $array_piece = [];
                foreach ($mysql_cols as $h_index => $mysql_row) {

                    if ($mysql_row['required'] == 1) {
                        $array_piece[] = '"' . $mysql_row['name'] . '"';
                    } else {
                        $array_piece[] = '""';
                    }

                }

                echo implode(',', $array_piece);
                ?>]

            ready_for_import = $('#new_world_order').val();
            ready_for_import = ready_for_import.split(",");

            for (index = 0; index < m_fields.length; ++index) {
                if (ready_for_import[index] == 0 && m_fields[index].length > 0) {
                    alert('Please match <' + m_fields[index] + '> column or add it to your file and restart the proccess.');
                    return false;
                }

            }

        });

        $('.rem_this_col').click(function (e) {
            e.preventDefault();

            if ($('#sortable tr').length > $('#sys_rows tr').length) {
                $(this).closest('tr').fadeOut('slow', function () {
                    $(this).remove();
                    go_refresh_table(null, null);
                });

            } else {
                $(this).closest('tr').attr('id', '0');
                $(this).closest('tr').children().children().fadeOut('slow', function () {
                    $(this).remove();
                    go_refresh_table(null, null);
                });

            }

        })

    });


</script>

<?php
if ($import_has_header == 1) {
    $data = unserialize($csv_data);
    $total_t = 0;
    $HTML_DATA = "";
    $TD_COUNTER = 0;
    foreach ($data as $k => $row) {
        if ($total_t > 30) {
            continue;
        }
        $HTML_DATA .= "<tr id='tr_" . $k . "'>";

        $new_index = 0;
        foreach ($mysql_cols as $kk => $mysql_row) {
            if (in_array($mysql_row['name'], $csv_headers)) {
                $HTML_DATA .= '<td id="td_' . $kk . '" class="text-left">' . $row[$new_index] . '</td>';
                $new_index++;
                $TD_COUNTER++;
            } else {
                $HTML_DATA .= '<td id="td_' . $kk . '" class="text-left"></td>';
            }
        }
        $HTML_DATA .= "</tr>";
        $total_t++;
    }
} else {
    $data = unserialize($csv_data);
    $total_t = 0;
    $HTML_DATA = "";
    $TD_COUNTER = 0;
    foreach ($data as $k => $row) {
        if ($total_t > 30) {
            continue;
        }
        $HTML_DATA .= "<tr id='tr_" . $k . "'>";

        foreach ($row as $kk => $mysql_row_l) {
            if (!empty($row[$kk])) {
                if ($k == 0) {
                    $TD_COUNTER++;
                }
                $HTML_DATA .= '<td id="td_' . $kk . '" class="text-left">' . $mysql_row_l . '</td>';
            }
        }

        for ($jj = 1; $jj < (count($mysql_cols) - $kk); $jj++) {
            $inc = $kk + 1;
            $HTML_DATA .= '<td id="td_' . $inc . '" class="text-left"></td>';
        }

        $HTML_DATA .= "</tr>";
        $total_t++;
    }
}
?>

<div class="bg-light lter b-b wrapper-md">
    <h1 class="m-n font-thin h3"><?= lang('import_clients'); ?></h1>
</div>

<form method="post" class="form-horizontal form-imp" enctype="multipart/form-data">
    <div>
        <div class="panel panel-default">
            <div class="panel-heading font-bold">
                <?= lang('import_step_2'); ?>
            </div>
            <div class="panel-body">
                <?php
                if($TD_COUNTER<4){ ?>
                    <div class="row">
                        <div class="panel-heading font-bold">
                            <?= lang('csv_error_msg'); ?>
                        </div>
                        <br>
                    </div>
                <?php }else{ ?>
                    <div class="row">
                        <div class="panel-heading font-bold">
                            1:<?= lang('step_1_order'); ?>
                        </div>
                        <br>
                        <div class="panel-heading ">
                            <?= lang('step_1_details'); ?>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 col-md-offset-3">
                            <div class="table-responsive">

                                <textarea name='csv_data' style='display:none'><?= $csv_data; ?></textarea>
                                <input type='hidden' name='csv_headers' value='<?= serialize($csv_headers); ?>'>
                                <input type='hidden' name='mysql_cols' value='<?= serialize($mysql_cols); ?>'>
                                <input type='hidden' name='import_has_header' value='<?= $import_has_header; ?>'>
                                <input type='hidden' name='action_on_duplicate' value='<?= $action_on_duplicate; ?>'>
                                <input type='hidden' name='prod_family' value='<?= $prod_family; ?>'>
                                <input type='hidden' name='prod_tax_cond' value='<?= $prod_tax_cond; ?>'>
                                <input id='new_world_order' type='hidden' name='new_world_order'
                                       value='<?= implode(",", $csv_headers); ?>'>

                                <table id="sys_rows" class="table table_script ">
                                    <thead>
                                    <tr>
                                        <th class="text-right"><span>DB Field</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($mysql_cols as $mysql_row) {
                                        $mand = '';
                                        if ($mysql_row['required'] == 1) {
                                            $mand = '<b style="color:red"><sup>*</sup></d>';
                                        }
                                        ?>

                                        <tr>
                                            <td class="text-right">
                                                <?= $mysql_row['name'] . $mand; ?>
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
                                    foreach ($mysql_cols as $mysql_row) { ?>
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
                                        <th>Import Field</th>
                                    </tr>
                                    </thead>
                                    <tbody id="sortable">
                                    <?php

                                    if ($import_has_header == 1) {
                                        $rex = 1;
                                        foreach ($mysql_cols as $mysql_row) {
                                            ?>

                                            <tr id='<?= in_array($mysql_row['name'], $csv_headers) ? "col-" . $rex : "0"; ?>'>
                                                <td class="text-left">
                                                    <div class="label label-warning redips-drag"
                                                         style="border-style: solid; cursor: move;">
                                                        <?=
                                                        in_array($mysql_row['name'], $csv_headers) ? $mysql_row['name'] : "-";
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
													<span class="pull-right">
												        <a href='' title='Remove this field from the import batch'
                                                           class='rem_this_col'><i class="fa fa-times fa-fw"></i></a>
												     </span>
                                                </td>
                                            </tr>

                                            <?php
                                            $rex++;
                                        }
                                    } else {

                                        foreach ($csv_headers as $header_row) { ?>

                                            <tr id='<?= $header_row; ?>'>
                                                <td class="text-left">
                                                    <div class="label label-warning redips-drag"
                                                         style="border-style: solid; cursor: move;">
                                                        <?= $header_row; ?>
                                                    </div>
                                                </td>
                                                <td>
													<span class="pull-right">
												        <a href='' title='Remove this field from the import batch'
                                                           class='rem_this_col'><i class="fa fa-times fa-fw"></i></a>
												     </span>
                                                </td>
                                            </tr>

                                        <?php }

                                        if (count($mysql_cols) > count($csv_headers)) {

                                            for ($i = 0; $i < (count($mysql_cols) - count($csv_headers)); $i++) { ?>

                                                <tr id='0'>
                                                    <td class="text-left">
                                                    </td>
                                                </tr>
                                            <?php }
                                        }

                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <div id="redips_clone" style="height: 1px; width: 1px;"></div>

                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="panel-heading font-bold">
                            2: <?= lang('step_2_preview'); ?>
                        </div>
                        </br>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed no-margin table-striped m-b-none dataTable no-footer import_to_update_table">
                                    <thead>
                                    <tr>
                                        <?php foreach ($mysql_cols as $mysql_row) : ?>
                                            <td class="text-left">
                                                <?= $mysql_row['name']; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    </thead>


                                    <tbody id='preview_import'>
                                    <?php
                                    echo $HTML_DATA;
                                    ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="panel-heading font-bold">
                            3: <?= lang('step_3_import'); ?>
                        </div>
                        <br>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                            <div class="wrapper bg-white but-wrapper">
                                <a href="<?= $this->agent->referrer(); ?>" id="btn-cancel" name="btn_cancel" class="btn btn-default" value="1">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                                <button type="submit" id="btn-submit" name="btn_submit_2" class="btn btn-info" value="1">
                                    <i class="fa fa-check"></i> Add to database
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</form>

<style type="text/css">
    span.group-span-filestyle.input-group-btn {
        padding-left: 10px !important;
    }

    .btn-default {
        padding-left: 10px !important;
        border-radius: 5px !important;
    }
</style>
