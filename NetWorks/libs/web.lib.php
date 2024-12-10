<?php
namespace networks\libs;
class Web{
    protected string $url;
    /**
     * Get web server informations
     * @param string|null $url [Optional] - Url to parse of change
     */
    public function __construct(String|null $url=null) {
        if($url)
            $this->url = $url;
        else
            $this->url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    /**
     * Gets the full URLs path
     *
     * @return string|null URL
     */
    public function getURL():string|null{
        return filter_var($this->url,FILTER_VALIDATE_URL);
    }
    /**
     * Returns the URLs host name
     *
     * @return string|null
     */
    public function getHost():string|null{
        return parse_url($this->url,PHP_URL_HOST);
    }
    /**
     * Returns the URL query
     *
     * @return array|false
     */
    public function getQuery():array|false{
        $parseURL = parse_url($this->url,PHP_URL_QUERY);
        $queries = array();
        foreach(explode('&',$parseURL) as $query){
            $e = explode('=',$query);
            $queries[$e[0]] = $e[1];
        }
        return $queries;
    }
    /**
     * Returns the URLs path
     *
     * @return array|null
     */
    public function getPath():array|null{
        return array_values(array_filter(explode('/',parse_url($this->url,PHP_URL_PATH)),function($i){
            return $i!=='';
        }));
    }
    /**
     * Returns the URLs scheme
     *
     * @return string|null
     */
    public function getScheme():string|null{
        return parse_url($this->url,PHP_URL_SCHEME);
    }
    /**
     * Returns the port number
     *
     * @return int|null
     */
    public function getPort():int|null{
        return parse_url($this->url,PHP_URL_PORT);
    }
    /**
     * Returns the password
     *
     * @return string|null
     */
    public function getPass():string|null{
        return parse_url($this->url,PHP_URL_PASS);
    }

    public function toAccessable():string|null{
        return trim(filter_var(str_replace(dirname(__DIR__,2).'\\',(empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]/",$this->url), FILTER_SANITIZE_URL),'/');
    }

}
?>