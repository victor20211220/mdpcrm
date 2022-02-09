<?php if (function_exists('validation_errors')) {
    if (validation_errors()) {
        echo validation_errors(
            '<div class="alert alert-danger"><span class="icons icons-alert-danger"></span>',
            '</div>'
        );
    }
} ?>

<?php if ($this->session->flashdata('alert_success')) : ?>
    <div id="alert-success-block" class="alert alert-success">
        <span class="icons icons-alert-success"></span>
        <?= $this->session->flashdata('alert_success'); ?>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('alert_info')) : ?>
    <div class="alert alert-info">
        <span class="icons icons-alert-info"></span>
        <?= $this->session->flashdata('alert_info'); ?>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('alert_error')) : ?>
    <div class="alert alert-danger">
        <span class="icons icons-alert-warning"></span>
        <?= $this->session->flashdata('alert_error'); ?>
    </div>
<?php endif; ?>

<script>
    setTimeout(function () {
        $('#alert-success-block').slideUp(400);
    }, 2000);
</script>
