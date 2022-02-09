<form method="post" class="form-horizontal">

    <div class="lter wrapper-md">
        <h1 class="font-thin h3"><?= lang('add_family'); ?></h1>
    </div>

    <div class="">
        <div class="panel panel-default">

            <div class="panel-body">
                <?= $this->layout->load_view('layout/alerts'); ?>

                <input class="hidden" name="is_update" type="hidden"
                    <?php
                        if ($this->Mdl_families->form_value('is_update')) {
                            echo 'value="1"';
                        } else {
                            echo 'value="0"';
                        }
                    ?>
                />

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                        <label for="family_name" class="control-label">
                            <?php echo lang('family_name'); ?>:
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" name="family_name" id="family_name" class="form-control"
                               value="<?php echo $this->Mdl_families->form_value('family_name'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    </div>
                    <div class="col-xs-12 col-sm-6 row">
                        <ul class="nav nav-pills nav-sm">
                            <?php $this->layout->load_view('layout/header_buttons'); ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

</form>


<script>
    $(document).ready(function() {
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>
