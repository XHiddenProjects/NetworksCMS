$(document).ready(()=>{
    $("select.form-select").each(((e,s)=>{$(s).on("focus",(e=>{$(e.target).is(":focus")?e.target.classList.add("opened"):e.target.classList.remove("opened")})),$(s).on("blur",(e=>{$(e.target).is(":focus")?e.target.classList.add("opened"):e.target.classList.remove("opened")}))}));
    $(".psw-container .toggle-psw-visible").each(((t,e)=>{$(e).on("click",(t=>{const e=$(t.target).parent().children("input");"password"===e.attr("type")?($(t.target).text("visibility_off"),e.attr("type","text")):($(t.target).text("visibility"),e.attr("type","password"))}))}));
    $("[data-checklist]").each(((e,t)=>{$('[type="submit"]').attr("disabled",!0),$('[type="submit"]').on("click",(e=>e.preventDefault())),$(t).on("input",(function(){var e=!0;if($(this).val().length>=8){const e=$(this).parent().parent().find(".checklist li").eq(0);e.attr("class",e.attr("class").replace("list-group-item-danger","list-group-item-success")),e.html(e.html().replace("close","check").replace("text-danger","text-success"))}else{const t=$(this).parent().parent().find(".checklist li").eq(0);t.attr("class",t.attr("class").replace("list-group-item-success","list-group-item-danger")),t.html(t.html().replace("check","close").replace("text-success","text-danger")),e=!1}if($(this).val().match(/[A-Z]/)){const e=$(this).parent().parent().find(".checklist li").eq(1);e.attr("class",e.attr("class").replace("list-group-item-danger","list-group-item-success")),e.html(e.html().replace("close","check").replace("text-danger","text-success"))}else{const t=$(this).parent().parent().find(".checklist li").eq(1);t.attr("class",t.attr("class").replace("list-group-item-success","list-group-item-danger")),t.html(t.html().replace("check","close").replace("text-success","text-danger")),e=!1}if($(this).val().match(/[a-z]/)){const e=$(this).parent().parent().find(".checklist li").eq(2);e.attr("class",e.attr("class").replace("list-group-item-danger","list-group-item-success")),e.html(e.html().replace("close","check").replace("text-danger","text-success"))}else{const t=$(this).parent().parent().find(".checklist li").eq(2);t.attr("class",t.attr("class").replace("list-group-item-success","list-group-item-danger")),t.html(t.html().replace("check","close").replace("text-success","text-danger")),e=!1}if($(this).val().match(/[0-9]/)){const e=$(this).parent().parent().find(".checklist li").eq(3);e.attr("class",e.attr("class").replace("list-group-item-danger","list-group-item-success")),e.html(e.html().replace("close","check").replace("text-danger","text-success"))}else{const t=$(this).parent().parent().find(".checklist li").eq(3);t.attr("class",t.attr("class").replace("list-group-item-success","list-group-item-danger")),t.html(t.html().replace("check","close").replace("text-success","text-danger")),e=!1}if($(this).val().match(/[^a-zA-Z0-9]/)){const e=$(this).parent().parent().find(".checklist li").eq(4);e.attr("class",e.attr("class").replace("list-group-item-danger","list-group-item-success")),e.html(e.html().replace("close","check").replace("text-danger","text-success"))}else{const t=$(this).parent().parent().find(".checklist li").eq(4);t.attr("class",t.attr("class").replace("list-group-item-success","list-group-item-danger")),t.html(t.html().replace("check","close").replace("text-success","text-danger")),e=!1}e?($('[type="submit"]').removeAttr("disabled"),$('[type="submit"]').off("click")):($('[type="submit"]').attr("disabled",!0),$('[type="submit"]').on("click",(e=>e.preventDefault())))}))}));
    $('input[type="password"]').each((e,t)=>{
        $(t).on('keydown',(event)=>{
            const key = event.keyCode||event.which;
            if((event.ctrlKey&&key===67)) event.preventDefault();
        });
    });
});

function sendRequest(url, method, async=false, isJSON=false, body={}){
    const xhr = new XMLHttpRequest();
    xhr.open(method.toLocaleUpperCase(), url, async);
    var response;
    xhr.onreadystatechange = ()=>{
        if(xhr.readyState==xhr.DONE&&xhr.status==200){
            response = xhr.responseText;
        }else response=false;
    }
    if(method.toLocaleLowerCase()==='get') xhr.send();
    else xhr.send(body)
    return isJSON ? JSON.parse(response) : response;
}