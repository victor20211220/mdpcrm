<?php if ($forMenu == true): ?>
    <?php foreach ($boxes as $b): ?>
        <div class='btn btn-sm btn-light col text-left email-nav-btn mb-1' data-id=<?= $b->id; ?>>
            <span class='fas fa-inbox fa-xxs mr-1'></span>
            <?= $b->box_name; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
        <a class="dropdown-item email-menu-dropdown-item active" href="#" data-id="0" data-name="All mailboxes">All mailboxes</a>
    <?php foreach ($boxes as $b): ?>
        <a class="dropdown-item email-menu-dropdown-item" href="#" data-id="<?= $b->id; ?>" data-name="<?= $b->box_name; ?>"><?= $b->box_name; ?></a>
    <?php endforeach; ?>
<?php endif; ?>
