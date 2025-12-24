<?php
/** @var string $id */
/** @var string $name */
/** @var string $value */
/** @var string $hiddenInput */
/** @var int $configId */
/** @var bool $hasAccess */

$hasPassword = !empty($value);
?>
<div class="secure-input-block" data-input-id="<?= $id ?>">
    <input type="text" data-name="<?= $name ?>" value="" class="secure-input" disabled style="<?= $hasPassword ? 'display:none;' : '' ?>">
    <div class="secure-input-placeholder" data-name="<?= $name ?>" <?= $hasPassword ? '' : 'style="display:none;"' ?>>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        <span>Password is hidden</span>
    </div>
    <?php if ($hasAccess): ?>
        <?= $hiddenInput ?>
        <div class="secure-input-buttons">
            <button type="button" class="secure-input-get-password secure-input-btn" data-name="<?= $name ?>" <?= ($hasPassword ? '' : 'style="display: none;"') ?> data-config-id="<?= $configId ?>" title="Get Password">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span class="secure-input-tooltip">Get Password</span>
            </button>
            <button type="button" class="secure-input-copy secure-input-btn" data-name="<?= $name ?>" style="display: none;" title="Copy">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span class="secure-input-tooltip">Copy</span>
            </button>
            <button type="button" class="secure-input-set-password secure-input-btn" data-name="<?= $name ?>" <?= ($hasPassword ? 'style="display: none;"' : '') ?> data-config-id="<?= $configId ?>" title="Set Password">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                <span class="secure-input-tooltip">Set Password</span>
            </button>
        </div>
    <?php endif; ?>
</div>
