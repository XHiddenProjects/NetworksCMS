$(document).ready(()=>{
    $('.pluginToggle').each((i,e)=>{
        if(!$(e).attr('disabled')){
            $(e).on('input',(i)=>{
                if($(i.target).is(':checked')){
                    (new Request(`${toRelPath()}assets/php/plugin.php?name=${$(i.target).attr('plugin-name')}&status=1`)).send().onSuccess((d)=>{
                        d = JSON.parse(d);
                        if(d['success']) window.location.reload();
                    });
                }else{
                    (new Request(`${toRelPath()}assets/php/plugin.php?name=${$(i.target).attr('plugin-name')}&status=0`)).send().onSuccess((d)=>{
                        d = JSON.parse(d);
                        if(d['success']) window.location.reload();
                    });
                }
            })
        }
    });
});