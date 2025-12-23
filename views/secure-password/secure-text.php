<?php
/** @var string $name */
/** @var string $value */
/** @var string $displayValue */
/** @var int $configId */
$hasPassword = !empty($value);
$hasAccess = acl()->canViewIntegrationsPassword();
?>
<div class="secure-text-block">
    <span class="secure-text" data-name="<?= $name ?>" <?= $hasPassword ? 'style="display:none;"' : '' ?>><?= $displayValue ?></span>
    <?php if ($hasPassword): ?>
        <div class="secure-text-placeholder" data-name="<?= $name ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <span>Password is hidden</span>
        </div>
    <?php endif; ?>
    <?php if ($hasAccess): ?>
        <input type="hidden" name="<?= $name ?>" value="<?= $value ?>" />
        <?php if ($hasPassword): ?>
            <div class="secure-text-buttons">
                <button type="button" class="secure-text-get-password secure-text-btn" data-name="<?= $name ?>" data-config-id="<?= $configId ?>" title="Get Password">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <span class="secure-text-tooltip">Get Password</span>
                </button>
                <button type="button" class="secure-text-hide secure-text-btn" data-name="<?= $name ?>" style="display: none;" title="Hide">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    <span class="secure-text-tooltip">Hide</span>
                </button>
                <button type="button" class="secure-text-copy secure-text-btn" data-name="<?= $name ?>" style="display: none;" title="Copy">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span class="secure-text-tooltip">Copy</span>
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>