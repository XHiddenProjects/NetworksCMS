/**
  * Calls an action from callback
  * @param {Function} callback Callback on form submit
  */
var FormAction = (callback)=>{
    $('document').ready(()=>{
        $('form').each((index, elem)=>{
            $(elem).on('submit',(e)=>{
                e.preventDefault();
                callback(e.target);
            });
        });
    });
}
let responseCode = '';
// Google ReCaptcha
function gSubmit(response){
    responseCode = response;
    document.querySelector('.g-recaptcha').classList.remove('err');
}

function gExpire(){
    responseCode = '';
    document.querySelector('.g-recaptcha').classList.add('err');
}

window.onload = function() {
    const url = (new URLParse()).getPath();
    if(url[url.length-1]==='login'&&Cookie.check('user')) window.open('../dashboard', '_self');
}

//Sign up
FormAction((form)=>{
    let passed=true, captchaActive=false;
    (new Request(`${toRelPath()}/assets/php/config.php?type=recaptcha&value=reCAPTCHA_active`)).send().onSuccess((d)=>{
        d = JSON.parse(d);
        if(parseInt(d['success'])) captchaActive = true;
    });
    $(form.querySelectorAll('[required]')).each((index, input)=>{
        if($(input).val()===''||(responseCode===''&&captchaActive)) {
            $(input).addClass('err');
            passed = false;
        }else $(input).removeClass('err');

        $(input).on('input',(e)=>{
            if($(e.target).val()!=='') $(e.target).removeClass('err');
        });
    });
    if($(form).hasClass('nw_signup')){
        if(passed){
            (new Request(`${toRelPath()}assets/php/user.php?action=add&username=${form.querySelector('#username').value}&email=${form.querySelector('#email').value}&psw=${form.querySelector('#psw').value}&cpsw=${form.querySelector('#cpsw').value}&fname=${form.querySelector('#fname').value}&mint=&lname=${form.querySelector('#lname').value}&perm=guest`))
            .send().onSuccess((d)=>{
                let getIP ='', secretKey='';
                    (new Request(`${toRelPath()}assets/php/user.php?action=get&type=ip`)).send().onSuccess((d)=>{
                        d = JSON.parse(d);
                        getIP = d['success'];
                });
                (new Request(`${toRelPath()}assets/php/config.php?type=recaptcha&value=reCAPTCHA_secretKey`)).send().onSuccess((d)=>{
                    secretKey = d['success'];
                });

                // Send a POST request to the reCAPTCHA API using jQuery
                $.ajax({
                    url: 'https://www.google.com/recaptcha/api/siteverify',
                    type: 'POST',
                    data: {
                        secret: secretKey,
                        response: responseCode,
                        remoteip: getIP
                    },
                    success: function(response) {
                        if(response.success) {
                            // reCAPTCHA validation passed
                            console.log('reCAPTCHA validation passed');
                        } else {
                            // reCAPTCHA validation failed
                            console.error('reCAPTCHA validation failed');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error if needed
                        console.error('Error occurred:', error);
                    }
                });

                const e = JSON.parse(d);
                if(e['err']){
                    $(form.querySelector('.errmsg')).parent().removeClass('d-none');
                    $(form.querySelector('.errmsg')).text(e['err']);
                }else {
                    (new Request(`${toRelPath()}assets/php/mail.php?type=verify&email=${form.querySelector('#email').value}&username=${form.querySelector('#username').value}`)).send();
                    console.log('success');
                }
            });
        }
    } 
    if($(form).hasClass('nw_login')){
        (new Request(`${toRelPath()}assets/php/user.php?action=login&username=${form.querySelector('#usernameEmail').value}&psw=${form.querySelector('#psw').value}`)).send()
        .onSuccess((d)=>{
            const e = JSON.parse(d);
            if(e['err']){
                $(form.querySelector('.errmsg')).parent().removeClass('d-none');
                $(form.querySelector('.errmsg')).text(e['err']);
            }else{
                window.open('../dashboard', '_self');
            }
        })
    }
});