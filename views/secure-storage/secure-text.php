<?php
/** @var string $name */
/** @var string $value */
/** @var string $displayValue */
/** @var int $configId */
?>
<div class="secure-text-block" style="position: relative;">
    <div class="secure-text" data-name="<?= $name ?>"><?= $displayValue ?></div>
    <?php if (acl()->canViewIntegrationsPassword()) { ?>
        <input type="hidden" name="<?= $name ?>" value="<?= $value ?>" />
        <?php if (!empty($value)) {  ?>
            <button class="secure-text-get-password btn" data-name="<?= $name ?>" data-config-id="<?= $configId ?>">Get Password</button>
        <?php } ?>
    <?php } ?>
</div>
