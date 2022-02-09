<?php foreach ($client_notes as $client_note): ?>
    <div class="alert alert-info">
        <p>
            <b>
                Admin
                (<?= date_from_mysql($client_note->client_note_date, true); ?>) :
            </b>
            &nbsp;
            <?= nl2br($client_note->client_note); ?>
        </p>
    </div>
<?php endforeach; ?>
