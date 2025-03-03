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
    $('.tab').on('click',(e)=>{
        e.preventDefault();
    });
});
/**
 * Sends a XMLHttpRequest 
 * @param {string} url URL to send
 * @param {string} method POST/GET methods
 * @param {boolean} [async=false] Async data
 * @param {boolean} [isJSON=false] Convert to JSON object 
 * @param {JSON} [body={}] Body request
 * @returns {string|JSON|false} Returns string/json object, otherwise false
 */
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
/**
 * Returns chart years based on starting year, updates every 5 years
 * @returns {number[]}
 */
function chartRangeYear() {
    const year = new Date().getFullYear();
    let startYear;
    if(sessionStorage.getItem('nw_chart_range')){
        if(year<=parseInt(sessionStorage.getItem('nw_chart_range'))+5){
            startYear = parseInt(sessionStorage.getItem('nw_chart_range'));
        }else{
            sessionStorage.setItem('nw_chart_range',year);
            startYear = year;
        }
    }else{
        sessionStorage.setItem('nw_chart_range',year);
        startYear = year;
    }
    const years = [];
    for(i=0;i<6;i++){
        years.push(startYear+i);
    }
    return years;
}
/**
 * Generates a random color
 * @param {'hex'|'rgb'} [type='hex'] Color type format
 * @param {number} [alpha=1] Transparency 0-1
 * @param {number} [amount=1] Amount of colors to generate
 * @returns {string[]} Color list
 */
function generateColor(type='hex', alpha=1, amount=1){
    const colors = [];
    for(let i = 0; i < amount; i++){
        let color;
        if(type === 'hex'){
            const hex = Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
            const alphaHex = Math.round(alpha * 255).toString(16).padStart(2, '0');
            color = `#${hex}${alphaHex}`;
        } else if(type === 'rgb'){
            const r = Math.floor(Math.random() * 256);
            const g = Math.floor(Math.random() * 256);
            const b = Math.floor(Math.random() * 256);
            color = `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }
        colors.push(color);
    }
    return colors;
}