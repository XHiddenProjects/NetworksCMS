<?php
use networks\libs\Lang;
include_once dirname(__DIR__).'/autoloader.php';

/**
 * Grab the API
 * @param string $target Targeted API
 * @param string $method Specified method
 * @param array $queries Queries to limit the result
 * @return string
 */
function api(string $target,string $method,array $queries=[]): string{
    $nc = trim("networks\\api\\{$target}_api");
    if($method&&method_exists($nc,$method)){
        $queries = array_filter($queries,function($i){return $i!=='';});
            if(class_exists($nc)){
                return call_user_func([$nc,$method],$queries);
            }else{
                return json_encode(['status'=>403,'msg'=>"{$target} ".(new Lang())->get(['Errors','apiNotFound'])],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
            }
        }else return json_encode(['status'=>403,'msg'=>(new Lang())->get(['Errors','noRoute'])],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
    }
?>