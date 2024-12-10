document.querySelectorAll('.showPsw').forEach(e=>{
    e.addEventListener('click',function(){
        const psw = this.parentNode.querySelector('input');
        if(psw.type==='password') {
            this.className = this.className.replace('fa-eye','fa-eye-slash');
            psw.type = 'text';
        }
        else {
            this.className = this.className.replace('fa-eye-slash','fa-eye');
            psw.type = 'password';
        }
    });
});

if(document.querySelector('#mint')){
    document.querySelector('#mint').addEventListener('input',(v)=>{
        v.target.value = v.target.value.toLocaleUpperCase();
    });
}

document.querySelectorAll('.nw_install_form form').forEach(f=>{
    f.addEventListener('submit',(e)=>{
        e.preventDefault();
        noErr=true;
        document.querySelectorAll('div form *[required]').forEach(e=>{
            e.addEventListener('input',()=>{
                if(e.value!=='') e.classList.remove('err');
            });
            if(!e.closest('.noshow')){
                if(e.value==='') {e.classList.add('err'); noErr=false;}
                else e.classList.remove('err');
            }
        });
        if(noErr){
            if(f.parentNode.classList.contains('sqlform')){
                (new Request(`./assets/php/sql_check.php?server=${f.querySelector('#sqlserver').value}&user=${f.querySelector('#sqlname').value}&psw=${f.querySelector('#sqlpsw').value}&db=${f.querySelector('#sqldb').value}`))
                .send().onSuccess((d)=>{
                    const e = JSON.parse(d);
                    if(e['err']){
                        f.querySelector('.errmsg').parentNode.classList.remove('d-none');
                        f.querySelector('.errmsg').innerText = e['err'];
                    }else {
                        f.querySelector('.errmsg').parentNode.classList.add('d-none');
                        f.parentNode.classList.add('noshow');
                        document.querySelector('.adminform').classList.remove('noshow');
                    }
                }).onError((e,c)=>{
                    console.log(`${e}: ${c}`);
                });
            }
            
            if(f.parentNode.classList.contains('adminform')){
                (new Request(`./assets/php/user.php?action=add&username=${f.querySelector('#uname').value}&email=${f.querySelector('#email').value}&psw=${f.querySelector('#psw').value}&cpsw=${f.querySelector('#cpsw').value}&fname=${f.querySelector('#fname').value}&mint=${f.querySelector('#mint').value}&lname=${f.querySelector('#lname').value}&perm=admin`))
                .send().onSuccess((d)=>{
                    const e = JSON.parse(d);
                    if(e['err']){
                        f.querySelector('.errmsg').parentNode.classList.remove('d-none');
                        f.querySelector('.errmsg').innerText = e['err'];
                    }else {
                        window.open('./','_self');
                    }
                }).onError((e,c)=>{
                    console.log(`${e}: ${c}`);
                })
            }
        }
    });
});

$(window).on('resize',function(){
    if (window.innerWidth > 768) {
        $('.navbar-collapse').collapse('hide');
    }
});