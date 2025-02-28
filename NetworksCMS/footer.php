<?php
use NetWorks\libs\Database;
use NetWorks\libs\Plugins;
use NetWorks\libs\Files;
use NetWorks\libs\Templates;
include_once 'init.php';
global $lang;
$plugins = new Plugins();
$f = new Files();
if($f->exists(path: NW_DATABASE.NW_DS.'NetworksCMS')){
    $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
    $results = $db->selectTable(name: 'settings')->select(mode:Database::ASSOC);
    $db->close();
}else $results=[];
$template = new Templates();
$v = $template->load(name: 'footer');
$footer=$v.'
<script>const DOMAIN = "'.NW_DOMAIN.'"</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
'.$plugins->hook(hookName: 'footerJS').'
'.$plugins->hook(hookName: 'afterLoad').'
</body>
</html>';
?>