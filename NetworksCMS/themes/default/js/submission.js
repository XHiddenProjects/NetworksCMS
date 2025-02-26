$(document).ready(()=>{
    $('.installation form').on('submit',(event)=>{
        let pass=true;
        event.preventDefault();
        const fname = $(event.target).find('#fname'),
        mname = $(event.target).find('#mname'),
        lname = $(event.target).find('#lname'),
        username = $(event.target).find('#username'),
        email = $(event.target).find('#email'),
        psw = $(event.target).find('#password'),
        confirm_psw = $(event.target).find('#confirm_password'),
        token = $(event.target).find('[name="_token"]');

        if(fname.val()===''){
            pass=false;
            fname.parent().find('.invalid-msg').attr('style','display:flex;');
        }else fname.parent().find('.invalid-msg').attr('style','display:none;');
        if(lname.val()===''){
            pass=false;
            lname.parent().find('.invalid-msg').attr('style','display:flex;');
        }else lname.parent().find('.invalid-msg').attr('style','display:none;');
        if(username.val()===''){
            pass=false;
            username.parent().find('.invalid-msg').attr('style','display:flex;');
        }else username.parent().find('.invalid-msg').attr('style','display:none;');
        if(email.val()===''||!email.val().match(/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/)){
            pass=false;
            email.parent().find('.invalid-msg').attr('style','display:flex;');
        }else email.parent().find('.invalid-msg').attr('style','display:none;');
        if(psw.val()===''){
            pass=false;
            psw.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }else psw.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(confirm_psw.val()===''||confirm_psw.val()!==psw.val()) {
            pass=false;
            confirm_psw.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }
        else confirm_psw.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(pass){
            const tokens = sendRequest(`./requests/install.php?token=${token.val()}`,'GET',false,true);
            if(!tokens['token_valid'])
                window.open('./install?err_token=true','_self');
            else{
                $('[type="submit"]').html('<span class="nw-loader"></span>');
                setTimeout(()=>{
                    const steps = sendRequest(`./requests/install.php?install=true&${encodeURIComponent(`fname=${fname.val()}&mname=${mname.val()}&lname=${lname.val()}&username=${username.val()}&email=${email.val()}&psw=${psw.val()}`).replace(/%3D/g,'=').replace(/%26/g,'&')}`,'GET',false,true);
                    if(steps['success']) window.open('./','_self');
                },8000);
                
            }
        }
    });
    $('.signup-nwform').on('submit',(event)=>{
        let pass=true;
        event.preventDefault();
        const fname = $(event.target).find('#fname'),
        mname = $(event.target).find('#mname'),
        lname = $(event.target).find('#lname'),
        username = $(event.target).find('#username'),
        email = $(event.target).find('#email'),
        psw = $(event.target).find('#password'),
        confirm_psw = $(event.target).find('#confirm_password'),
        token = $(event.target).find('[name="_token"]'),
        toc = $(event.target).find('#TermsAndConditions'),
        pp = $(event.target).find('#PrivacyPolicy');

        if(fname.val()===''){
            pass=false;
            fname.parent().find('.invalid-msg').attr('style','display:flex;');
        }else fname.parent().find('.invalid-msg').attr('style','display:none;');
        if(lname.val()===''){
            pass=false;
            lname.parent().find('.invalid-msg').attr('style','display:flex;');
        }else lname.parent().find('.invalid-msg').attr('style','display:none;');
        if(username.val()===''){
            pass=false;
            username.parent().find('.invalid-msg').attr('style','display:flex;');
        }else username.parent().find('.invalid-msg').attr('style','display:none;');
        if(email.val()===''||!email.val().match(/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/)){
            pass=false;
            email.parent().find('.invalid-msg').attr('style','display:flex;');
        }else email.parent().find('.invalid-msg').attr('style','display:none;');
        if(psw.val()===''){
            pass=false;
            psw.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }else psw.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(confirm_psw.val()===''||confirm_psw.val()!==psw.val()) {
            pass=false;
            confirm_psw.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }else confirm_psw.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(!toc.is(':checked')) {
            pass=false;
            toc.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }else toc.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(!pp.is(':checked')) {
            pass=false;
            pp.parent().parent().find('.invalid-msg').attr('style','display:flex;');
        }else pp.parent().parent().find('.invalid-msg').attr('style','display:none;');
        if(pass){
            const tokens = sendRequest(`../requests/auth.php?token=${token.val()}`,'GET',false,true),
            authorized = sendRequest(`../requests/auth.php?check=true&username=${username.val()}&email=${email.val()}`,'GET',false,true);
            console.log(authorized);
            if(!tokens['token_valid'])
                window.open(`${window.location.href.replace(/\?.*$/,'')}?err_token=true`,'_self');
            if(!authorized['canPass'])
                window.open(`${window.location.href.replace(/\?.*$/,'')}?user_exists=true`,'_self');
            else{
                $('[type="submit"]').html('<span class="nw-loader"></span>');
                setTimeout(()=>{
                    const registered = sendRequest(`../requests/auth.php?signup=true&fname=${fname.val()}&mname=${mname.val()}&lname=${lname.val()}&username=${username.val()}&email=${email.val()}&psw=${psw.val()}`,'GET',false,true)
                    //window.open('../dashboard','_self');
                },8000);
                
            }
        }
    });
});