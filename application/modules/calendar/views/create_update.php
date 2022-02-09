<script>
    $(function() {
        $('#colorpicker').colorpicker({
            format: 'hex'
        });

        $('.form_datetime').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true,
            todayBtn: true,
            minuteStep: 30
        });

        $('body').on('click', '.pick-color', function () {
            $('#colorpicker').val($(this).data('color'));
        })
    });
</script>

<?php if ($success === true): ?>
<script>
    window.location.reload();
</script>
<?php endif; ?>

<div class="modal-header">
    <h4 class="modal-title">
        <?php if ($action == 'create'): ?>
        Create new event
        <?php else: ?>
        Update event
        <?php endif; ?>
    </h4>
</div>

<div class="modal-body">

    <form id="modalCreateUpdateForm">
        <?= validation_errors("<div class='alert alert-danger' style='font-size: smaller'>", "</div>"); ?>

        <div class="form-group">
            <label for="modalCreateTitle">
                Title
            </label>
            <input id="modalCreateTitle" class="form-control" type="text" name="title" placeholder="Enter event name"
                   value="<?= $this->Mdl_calendar->form_value('title'); ?>"/>
        </div>
        <div class="form-group">
            <label for="modalCreateStart">Start</label>
            <input id="modalCreateStart" class="form-control form_datetime" type="text" name="date_start"
                   placeholder="Enter start date/time" value="<?= $this->Mdl_calendar->form_value('date_start'); ?>"/>
        </div>
        <div class="form-group">
            <label for="modalCreateEnd">End</label>
            <input id="modalCreateEnd" class="form-control form_datetime" type="text" name="date_end"
                   placeholder="Enter end date/time (optional)" value="<?= $this->Mdl_calendar->form_value('date_end'); ?>"/>
        </div>
        <div class="form-group">
            <label>Color</label>
            <input id="colorpicker" type="text" value="<?= $this->Mdl_calendar->form_value('color'); ?>"
                   class="form-control" name="color"
                   placeholder="Pick a color from badge below or from colorpicker (click field)"/>
            <span class="pick-color badge badge-pill ml-0" style="background: grey;" data-color="#808080">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: darkgreen" data-color="#006400">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: darkred" data-color="#8B0000">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: blue" data-color="#0000FF">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: darkcyan" data-color="#008B8B">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: deeppink" data-color="#FF1493">&nbsp;</span>
            <span class="pick-color badge badge-pill ml-2" style="background: darkorange" data-color="#FF8C00">&nbsp;</span>
        </div>
        <div class="form-group">
            <label for="modalCreateDescription">Event description</label>
            <textarea id="modalCreateDescription" class="form-control" name="description" maxlength="128"
                      placeholder="Event description (optional)"><?= $this->Mdl_calendar->form_value('description'); ?></textarea>
        </div>
        <div class="form-check">
            <input id="modalCreateFullday" class="form-check-input" name="fullday" type="checkbox" value="1" <?= $this->Mdl_calendar->form_value('fullday') == true ? 'checked' : null; ?>>
            <label for="modalCreateFullday" class="form-check-label">Full day event</label>
        </div>
    </form>

</div>
<div class="modal-footer">
    <?php if ($action == 'create'): ?>
    <button id="modalCreateSubmit" class="btn btn-primary">
        Create
    </button>
    <button class="btn btn-default" data-dismiss="modal">
        Close
    </button>
    <?php endif; ?>

    <?php if ($action == 'update'): ?>
    <button id="modalUpdateDelete" class="btn btn-danger" data-id="<?= $id; ?>">
        Delete
    </button>
    <button id="modalUpdateSubmit" class="btn btn-primary" data-id="<?= $id; ?>">
        Update
    </button>
    <button class="btn btn-default" data-dismiss="modal">
        Close
    </button>
    <?php endif; ?>
</div>
