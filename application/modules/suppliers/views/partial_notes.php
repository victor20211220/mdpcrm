<?php foreach ($client_notes as $client_note) { ?>
    <div class="alert alert-default">
        <p><b><?php echo $client_note->user_name;?> (<?php echo date_from_mysql($client_note->client_note_date, TRUE); ?>):</b>&nbsp;
            <?php echo nl2br($client_note->client_note); ?>
        </p>
    </div>
<?php } ?>