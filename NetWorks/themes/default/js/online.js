$(document).ready(()=>{
    setInterval(()=>{
        (new Request(`${toRelPath()}assets/php/user.php?action=online`)).send();
    },1000);
});