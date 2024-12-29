$(document).ready(function() {
    $('.nw_install_form form').each(function() {
        $(this).on('submit', function(e) {
            e.preventDefault();
            let noErr = true;
            $('div form *[required]').each(function() {
                $(this).on('input', function() {
                    if ($(this).val() !== '') $(this).removeClass('err');
                });
                if (!$(this).closest('.noshow').length) {
                    if ($(this).val() === '') {
                        $(this).addClass('err');
                        noErr = false;
                    } else {
                        $(this).removeClass('err');
                    }
                }
            });
            if (noErr) {
                if ($(this).parent().hasClass('sqlform')) {
                    $.ajax({
                        url: `./assets/php/sql_check.php`,
                        data: {
                            server: $(this).find('#sqlserver').val(),
                            user: $(this).find('#sqlname').val(),
                            psw: $(this).find('#sqlpsw').val(),
                            db: $(this).find('#sqldb').val()
                        },
                        success: function(d) {
                            const e = JSON.parse(d);
                            if (e['err']) {
                                $(this).find('.errmsg').parent().removeClass('d-none');
                                $(this).find('.errmsg').text(e['err']);
                            } else {
                                $(this).find('.errmsg').parent().addClass('d-none');
                                $(this).parent().addClass('noshow');
                                $('.adminform').removeClass('noshow');
                            }
                        }.bind(this),
                        error: function(e, c) {
                            console.log(`${e}: ${c}`);
                        }
                    });
                }
                if ($(this).parent().hasClass('adminform')) {
                    $.ajax({
                        url: `./assets/php/user.php`,
                        data: {
                            action: 'add',
                            username: $(this).find('#uname').val(),
                            email: $(this).find('#email').val(),
                            psw: $(this).find('#psw').val(),
                            cpsw: $(this).find('#cpsw').val(),
                            fname: $(this).find('#fname').val(),
                            mint: $(this).find('#mint').val(),
                            lname: $(this).find('#lname').val(),
                            perm: 'admin'
                        },
                        success: function(d) {
                            const e = JSON.parse(d);
                            if (e['err']) {
                                $(this).find('.errmsg').parent().removeClass('d-none');
                                $(this).find('.errmsg').text(e['err']);
                            } else {
                                window.open('./', '_self');
                            }
                        }.bind(this),
                        error: function(e, c) {
                            console.log(`${e}: ${c}`);
                        }
                    });
                }
            }
        });
    });
    // Button link
    $('[btn-link]').each(function() {
        $(this).on('click', function() {
            window.open($(this).attr('btn-link'), '_self');
        });
    });
    // Show password
    $('.showPsw').each(function(index, input) {
        $(input).on('click', function() {
            if ($(input).prev().attr('type') === 'password') {
                $(input).prev().attr('type', 'text');
                $(input).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $(input).prev().attr('type', 'password');
                $(input).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
});