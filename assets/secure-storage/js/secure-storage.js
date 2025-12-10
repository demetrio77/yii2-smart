

function getPassword($this, name, configId, responseFunc) {

    let csrfParam = $('meta[name="csrf-param"]').attr('content');
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let passwordId = $('input[name="' + name + '"]').val();

    $.ajax({
        url: '/secure-storage/default/get-password?passwordId=' + passwordId + '&configId=' + configId,
        data: {
            csrfParam:csrfToken
        },
        method: 'GET',
        success: (resp) => {
            responseFunc(resp);
        },
        error: function(xhr, status, error) {
            console.log(error);
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
    getPassword($this, name, configId, function (resp) {
        if (resp.password.length > 0) {
            $('.secure-input[data-name="' + name + '"]').val(resp.password);
            $this.hide();
            $('.secure-input-set-password[data-name="' + name + '"]').text('Edit Password').show();
        }
    });

    return false;
});

$(document).on('click', '.secure-input-set-password', function() {
    let setPasswordModal = $('#setPasswordModal');

    setPasswordModal.attr('data-name', $(this).data('name'));
    setPasswordModal.attr('data-config-id', $(this).data('config-id'));

    setPasswordModal.modal({
        keyboard: true
    });

    return false;
});

$(document).on('click', '#setPasswordModal button[type="submit"]', function() {

    let setPasswordModal = $('#setPasswordModal');
    let csrfParam = $('meta[name="csrf-param"]').attr('content');
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let password = $.trim(setPasswordModal.find('input[name="password"]').val());

    let name = setPasswordModal.data('name');
    let passwordId = $('input[type="hidden"][name="' + name + '"]').val();
    let configId = setPasswordModal.data('config-id');
    if (configId === "") {
        configId = 0;
    }

    let url = '';
    if (passwordId.length) {
        url = '/secure-storage/default/update-password?passwordId=' + passwordId + '&configId=' + configId;
    } else {
        url = '/secure-storage/default/create-password?configId=' + configId;
    }
    setPasswordModal.find('#errors').text('');
    $.ajax({
        url: url,
        data: {
            csrfParam:csrfToken,
            password: password
        },
        method: 'POST',
        success: (resp) => {
            let name = setPasswordModal.data('name');

            if (passwordId.length === 0) {
                $('input[type="hidden"][name="' + name + '"]').val(resp.password_id);
            }

            let secureInput = $('.secure-input[data-name="' + name + '"]');
            secureInput.val(password);

            $('.secure-input-set-password[data-name="' + name + '"]').text('Edit Password');

            setPasswordModal.modal('hide');
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON !== undefined) {
                setPasswordModal.find('#errors').text(xhr.responseJSON.message);
            } else {
                setPasswordModal.find('#errors').text('an error occurred');
            }
        }
    });

    return false;
});
