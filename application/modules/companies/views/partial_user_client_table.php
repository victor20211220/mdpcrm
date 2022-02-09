<div class="table-responsive">
    <table class="table table-striped no-margin">

        <thead>
        <tr>
            <th><?= lang('client'); ?></th>
            <th><?= lang('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($user_clients as $client) : ?>
            <tr>
                <td><?= $client->client_name; ?></td>
                <td>
                    <?php if ($id) : ?>
                        <a class=""
                           href="<?= "/users/delete_user_client/{$id}/{$client->user_client_id}"; ?>">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>
