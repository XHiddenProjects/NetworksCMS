<?php
namespace NetWorks\api;
include_once dirname(path: __DIR__,levels: 2).'/init.php';
class Controller{
    /**
     * Call magic method
     */
    public function __call($name, $arguments): void{
        $this->sendOutput(data: '', httpHeaders: ['HTTP/1.1 404 Not Found']);
    }
    /** 
    * Get URI elements. 
    * 
    * @return array 
    */
    protected function getUriSegments(): array|bool{
        $uri = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);
        $uri = explode( separator: '/', string: $uri );
        return $uri;
    }
    /** 
    * Get querystring params. 
    * 
    * @return array 
    */
    protected function getQueryStringParams(): array{
        parse_str(string: $_SERVER['QUERY_STRING'], result: $query);
        return $query;
    }
    /** 
    * Send API output. 
    * 
    * @param mixed $data 
    * @param string $httpHeader 
    * @return never
    */
    protected function sendOutput($data, $httpHeaders=[]): never{
        header_remove(name: 'Set-Cookie');
        if (is_array(value: $httpHeaders) && count(value: $httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header(header: $httpHeader);
            }
        }
        echo $data;
        exit;
    }
}
?>