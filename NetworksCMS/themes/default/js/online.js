$(document).ready(()=>{
    setInterval(()=>{
        sendRequest(`${DOMAIN}/requests/auth.php?online`,'POST');
    },1000);
});