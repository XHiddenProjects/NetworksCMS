<?php
namespace networks\api;
use SSQL;
include_once dirname(__DIR__).'/libs/ssql.lib.php';
include_once dirname(__DIR__).'/init.php';
class users_api{
    /**
     * Lists all the created accounts
     * @param array{startYear: int|null, endYear: int|null} $queries Search queries to limit 
     * @return string
     */
    public static function limit(array $queries=[]): string{
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if(!empty($queries)){
                    switch(strtolower($queries['by'])){
                        case 'year':
                            $count = isset($queries['endYear']) ? $db->selectData('users',['username','accCreated'],'WHERE YEAR(accCreated) BETWEEN '.$queries['startYear'].' AND '.$queries['endYear']) : $db->selectData('users',['username','accCreated'],'WHERE YEAR(accCreated) = '.$queries['startYear']);
                            return json_encode(['status'=>200,'success'=>$count],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                        default:
                            return json_encode(['status'=>400,'error'=>'Invalid query'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                    }
                }else return json_encode(['status'=>400],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
            }else return json_encode(['status'=>400,'error'=>'SQL credentials not found'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
        }
    }
    /**
     * Returns users information
     * @param array{type: string|null, value: string|null, selections: string|null} $queries Queries to limit the result
     * @return string
     */
    public static function list(array $queries=[]):string{
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if(!empty($queries)){
                    if(isset($queries['type'])&&isset($queries['value'])){
                        if($db->selectData('users',['UserID','username','email','fname','mname','lname','icon','permission','isOnline','accCreated'],'WHERE '.strtolower($queries['type']).'="'.$queries['value'].'"')){
                            $data = $db->selectData('users',isset($queries['selections']) ? array_filter(explode(',',$queries['selections']),function($i){return !preg_match('/psw|OnlineStat|public_key|ip/i',$i);}) : ['UserID','username','email','fname','mname','lname','icon','permission','isOnline','accCreated'],'WHERE '.strtolower($queries['type']).'="'.$queries['value'].'"');
                            return json_encode(['status'=>200,'success'=>$data??[]],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                        }else
                            return json_encode(['status'=>404,'error'=>'Query not found'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                    }else{
                        $data = $db->selectData('users',isset($queries['selections']) ? array_filter(explode(',',$queries['selections']),function($i){return !preg_match('/psw|OnlineStat|public_key|ip/i',$i);}) : ['UserID','username','email','fname','mname','lname','icon','permission','isOnline','accCreated']);
                        return json_encode(['status'=>200,'success'=>$data??[]],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                    }
                }else{
                    $data = $db->selectData('users',isset($queries['selections']) ? array_filter(explode(',',$queries['selections']),function($i){return !preg_match('/psw|OnlineStat|public_key|ip/i',$i);}) : ['UserID','username','email','fname','mname','lname','icon','permission','isOnline','accCreated']);
                    return json_encode(['status'=>200,'success'=>$data??[]],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
                }
            }else return '';
        }else return json_encode(['status'=>400,'error'=>'SQL credentials not found'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
}
?>