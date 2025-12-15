<?php
/** @var string $name */
/** @var string $value */
/** @var string $displayValue */
/** @var int $configId */
?>
<div class="secure-input-block" style="position: relative;">
    <input type="text" id="admin-username" data-name="<?= $name ?>" value="<?= $displayValue ?>" class="secure-input" disabled>
    <?php if (acl()->canViewIntegrationsPassword()) { ?>
        <input type="hidden" name="<?= $name ?>" value="<?= $value ?>" />
        <button class="secure-input-get-password btn" data-name="<?= $name ?>" <?= (empty($value) ? ' style="display: none;"' : '') ?> data-config-id="<?= $configId ?>">Get Password</button>
        <button class="secure-input-set-password btn" data-name="<?= $name ?>" <?= (!empty($value) ? ' style="display: none;"' : '') ?> data-config-id="<?= $configId ?>">Set Password</button>
    <?php } ?>
</div>
