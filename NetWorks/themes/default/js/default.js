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
                            console.log(d);
                            if (d['err']) {
                                $(this).find('.errmsg').parent().removeClass('d-none');
                                $(this).find('.errmsg').text(d['err']);
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
                            if (d['err']) {
                                $(this).find('.errmsg').parent().removeClass('d-none');
                                $(this).find('.errmsg').text(d['err']);
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
                $(input).text('visibility_off');
            } else {
                $(input).prev().attr('type', 'password');
                $(input).text('visibility');
            }
        });
    });
    //measure password
    const validations=[/.{8,}/,/[a-z]/,/[A-Z]/,/[0-9]/,/[^A-Za-z0-9]/];$(".psw_container input[type=password]").each(((s,a)=>{const r=getDict().pswStats;$(a).parent().find(".psw-measure").length>0&&$(a).on("input",(s=>{let t=0;const e=$(s.target).val(),d=$(a).parent().find(".psw-measure"),n=d.find(".psw-strength-txt"),i=$('[type="submit"],[psw-str-check]');if(validations.forEach((s=>{s.test(e)&&(t+=20)})),t<20){d.find(".progress-bar").width("0%");const s=d.find(".progress-bar").attr("class").match(/bg-(.*)/);s?d.find(".progress-bar").removeClass(s[0]).addClass("bg-danger"):d.find(".progress-bar").addClass("bg-danger"),i.attr("disabled",!0),n.text(r.weak),n.attr("style","display:block")}else if(t>=20&&t<=40){d.find(".progress-bar").width(`${t}%`);const s=d.find(".progress-bar").attr("class").match(/bg-(.*)/);s?d.find(".progress-bar").removeClass(s[0]).addClass("bg-danger"):d.find(".progress-bar").addClass("bg-danger"),i.attr("disabled",!0),n.text(r.weak),n.attr("style","display:block")}else if(t>40&&t<=80){d.find(".progress-bar").width(`${t}%`);const s=d.find(".progress-bar").attr("class").match(/bg-(.*)/);s?d.find(".progress-bar").removeClass(s[0]).addClass("bg-warning"):d.find(".progress-bar").addClass("bg-warning"),i.attr("disabled",!1),n.text(r.ok),n.attr("style","display:block")}else{d.find(".progress-bar").width(`${t}%`);const s=d.find(".progress-bar").attr("class").match(/bg-(.*)/);s?d.find(".progress-bar").removeClass(s[0]).addClass("bg-success"):d.find(".progress-bar").addClass("bg-success"),i.attr("disabled",!1),n.text(r.good),n.attr("style","display:block")}}))}));

    // logout
    $('#nwlogout').on('click', function() {
        $.ajax({
            url: `./assets/php/user.php`,
            data: {
                action: 'logout'
            },
            success: function() {
                window.open('./', '_self');
            },
            error: function(e, c) {
                console.log(`${e}: ${c}`);
            }
        });
    });
    
    $(".color-picker").each(((e,o)=>{new Alwan(o,{parent:o.parentNode,popover:!0,classname:"color-picker-ui btn",theme:"light",disabled:!!o.hasAttribute("disabled"),closeOnScroll:!0,copy:!0,swatches:["white","red","orange","yellow","green","blue","black"]}).on("color",(function(e){e.source.config.parent.querySelector("input").value=e.hex}))}));
    $(document).ready(()=>{setTimeout(()=>{setInterval(()=>{(new Request(`${toRelPath()}assets/php/user.php?action=online`)).send();},1000);},3000);});
    $(document).ready(()=>{if(!Cookie.check('user')&&(new URLParse()).getPath().includes('dashboard')) window.location.href = './auth/login';});
});