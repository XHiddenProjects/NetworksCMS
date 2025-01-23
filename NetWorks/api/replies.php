<?php
namespace networks\api;
use SSQL;
include_once dirname(__DIR__).'/libs/ssql.lib.php';
include_once dirname(__DIR__).'/init.php';
class replies_api{
    public static function list(array $queries=[]){
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if(!empty($queries)){
                    if(isset($queries['type'])&&isset($queries['value'])){
                        if($db->selectData('replies',['*'],'WHERE '.strtolower($queries['type']).'="'.$queries['value'].'"')){
                            $data = $db->selectData('replies',isset($queries['selections']) ? array_filter(explode(',',$queries['selections']),function($i){return !preg_match('/psw|OnlineStat|public_key|ip/i',$i);}) : ['*'],'WHERE '.strtolower($queries['type']).'="'.$queries['value'].'"');
                            return json_encode(['status'=>200,'success'=>$data??[]],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                        }else
                            return json_encode(['status'=>404,'error'=>'Query not found'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                    }else{
                        $data = $db->selectData('replies',isset($queries['selections']) ? array_filter(explode(',',$queries['selections']),function($i){return !preg_match('/psw|OnlineStat|public_key|ip/i',$i);}) : ['*']);
                        return json_encode(['status'=>200,'success'=>$data??[]],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                    }
                }else $count = $db->selectData('replies',['*']);
                    return json_encode(['status'=>200,'success'=>$count],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                }else return json_encode(['status'=>400],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
            }else return json_encode(['status'=>400,'error'=>'SQL credentials not found'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
}
?>