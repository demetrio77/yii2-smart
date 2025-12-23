
function getPassword($this, name, configId, responseFunc) {

    let csrfParam = $('meta[name="csrf-param"]').attr('content');
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let passwordId = $('input[name="' + name + '"]').val();

    $.ajax({
        url: '/secure-password/get-password?passwordId=' + passwordId + '&configId=' + configId,
        data: {
            csrfParam:csrfToken
        },
        method: 'GET',
        success: (resp) => {
            if (resp.twofa_status !== undefined) {
                switch(resp.twofa_status) {
                    case 'success':
                        location.reload();
                        break;
                    case 'user_is_pending':
                        alert('User status "pending". Access Denied');
                        break;
                    case '2fa':
                    case '2fa_wait':
                    case 'validation_failed':
                        afterTwoFaAuthFunction = function () {
                            getPassword($this, name, configId, responseFunc);
                        };
                        getTwoFaForm(configId); // 2fa-login.js
                        break;
                }
            } else {
                responseFunc(resp);
            }
        },
        error: function(xhr, status, error) {
            $this.removeClass('loading');
            if (xhr.responseJSON !== undefined) {
                alert(xhr.responseJSON.message);
            } else {
                alert(xhr.responseText);
            }
        }
    });
}

$(document).on('click', '.secure-text-get-password', function() {
    let $this = $(this);
    let name = $this.data('name');
    let configId = $this.data('config-id');
    if (configId === "") {
        configId = 0;
    }
    getPassword($this, name, configId, function (resp) {
        $('.secure-text[data-name="' + name + '"]').text(resp.password);
        $this.hide();
    });

    return false;
});

$(document).on('click', '.secure-input-get-password', function() {
    let $this = $(this);
    let name = $this.data('name');
    let configId = $this.data('config-id');
    if (configId === "") {
        configId = 0;
    }
    $this.addClass('loading');
    getPassword($this, name, configId, function (resp) {
        $this.removeClass('loading');
        if (resp.password.length > 0) {
            let $input = $('.secure-input[data-name="' + name + '"]');
            let $placeholder = $('.secure-input-placeholder[data-name="' + name + '"]');
            $placeholder.hide();
            $input.val(resp.password).show();
            $this.hide();
            $('.secure-input-copy[data-name="' + name + '"]').show();
            let $setBtn = $('.secure-input-set-password[data-name="' + name + '"]');
            $setBtn.attr('title', 'Edit Password').find('.secure-input-tooltip').text('Edit Password');
            $setBtn.show();
            checkSecureInputOverflow($input);
        }
    });

    return false;
});

$(document).on('click', '.secure-input-set-password', function() {
    let $btn = $(this);
    let name = $btn.data('name');
    let configId = $btn.data('config-id');
    if (configId === "") {
        configId = 0;
    }

    let modal = new dkmodal({ width: '500px' });
    modal.message({
        title: 'Set Password',
        html: '<section style="margin-bottom: 15px;">' +
              '<input type="text" class="form-control" name="modal-password" value="" placeholder="Loading...">' +
              '</section>' +
              '<div class="secure-modal-warning" style="color: #8a6d3b; background: #fcf8e3; padding: 10px; border-radius: 3px; margin-bottom: 10px; font-size: 12px;">' +
              '<strong>Warning:</strong> The password will be saved immediately. Previous value will be overwritten even if the integration form is not saved.' +
              '</div>' +
              '<div class="secure-modal-errors" style="color: red; margin-bottom: 10px;"></div>',
        buttons: [
            {
                type: 'function',
                caption: 'Save',
                id: 'save-password',
                loading: 'Saving...',
                action: function() {
                    savePassword(modal, name, configId);
                }
            },
            {
                type: 'dismiss',
                caption: 'Close'
            }
        ],
        afterLoad: function() {
            let $input = modal.body.find('input[name="modal-password"]');
            let passwordId = $('input[type="hidden"][name="' + name + '"]').val();

            if (passwordId && passwordId.length > 0) {
                $btn.addClass('loading');
                getPassword($btn, name, configId, function(resp) {
                    $btn.removeClass('loading');
                    $input.val(resp.password).attr('placeholder', '');
                });
            } else {
                $input.val('').attr('placeholder', 'Enter password');
            }
        }
    });

    return false;
});

function savePassword(modal, name, configId) {
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let password = $.trim(modal.body.find('input[name="modal-password"]').val());
    let passwordId = $('input[type="hidden"][name="' + name + '"]').val();

    let url = '';
    if (passwordId && passwordId.length) {
        url = '/secure-password/update-password?passwordId=' + passwordId + '&configId=' + configId;
    } else {
        url = '/secure-password/create-password?configId=' + configId;
    }

    modal.body.find('.secure-modal-errors').text('');

    $.ajax({
        url: url,
        data: {
            csrfParam: csrfToken,
            password: password
        },
        method: 'POST',
        success: (resp) => {
            if (resp.twofa_status !== undefined) {
                switch(resp.twofa_status) {
                    case 'success':
                        location.reload();
                        break;
                    case 'user_is_pending':
                        alert('User status "pending". Access Denied');
                        break;
                    case '2fa':
                    case '2fa_wait':
                    case 'validation_failed':
                        afterTwoFaAuthFunction = function () {
                            savePassword(modal, name, configId);
                        };
                        getTwoFaForm(configId);
                        break;
                }
            } else {
                if (!passwordId || passwordId.length === 0) {
                    $('input[type="hidden"][name="' + name + '"]').val(resp.password_id);
                }

                let $secureInput = $('.secure-input[data-name="' + name + '"]');
                let $placeholder = $('.secure-input-placeholder[data-name="' + name + '"]');
                let $block = $secureInput.closest('.secure-input-block');
                $placeholder.hide();
                $secureInput.val(password).show();
                checkSecureInputOverflow($secureInput);

                let $setBtn = $('.secure-input-set-password[data-name="' + name + '"]');
                $setBtn.attr('title', 'Edit Password').find('.secure-input-tooltip').text('Edit Password');

                $('.secure-input-copy[data-name="' + name + '"]').show();

                $block.addClass('updated');
                setTimeout(function() {
                    $block.removeClass('updated');
                }, 3000);

                modal.close();
            }
        },
        error: function(xhr) {
            modal.buttons.get('save-password').button('reset');
            if (xhr.responseJSON !== undefined) {
                modal.body.find('.secure-modal-errors').text(xhr.responseJSON.message);
            } else {
                modal.body.find('.secure-modal-errors').text('An error occurred');
            }
        }
    });
}

$(document).on('click', '.secure-input-copy', function() {
    let $this = $(this);
    let name = $this.data('name');
    let password = $('.secure-input[data-name="' + name + '"]').val();

    navigator.clipboard.writeText(password).then(function() {
        let $tooltip = $this.find('.secure-input-tooltip');
        let originalText = $tooltip.text();
        $tooltip.text('Copied!');
        setTimeout(function() {
            $tooltip.text(originalText);
        }, 1500);
    });

    return false;
});

function checkSecureInputOverflow($input) {
    let $block = $input.closest('.secure-input-block');
    if ($input[0].scrollWidth > $input[0].clientWidth) {
        $block.addClass('has-overflow');
    } else {
        $block.removeClass('has-overflow');
    }
}

$(document).on('mouseenter', '.secure-input', function() {
    let $input = $(this);
    let $block = $input.closest('.secure-input-block');
    if (!$block.hasClass('has-overflow')) return;

    let scrollAmount = $input[0].scrollWidth - $input[0].clientWidth;
    let duration = Math.max(scrollAmount * 15, 500);

    $block.addClass('scrolled');
    $input.stop().animate({ scrollLeft: scrollAmount }, duration);
});

$(document).on('mouseleave', '.secure-input', function() {
    let $input = $(this);
    let $block = $input.closest('.secure-input-block');

    $input.stop().animate({ scrollLeft: 0 }, 300, function() {
        $block.removeClass('scrolled');
    });
});
