<?php


declare(strict_types=1);

namespace App\Service;

use App\Service\JwtToken;

class HttpRequest
{

    private $request = [];

    public function __construct()
    {
        $this->request['method'] = $this->getMethod();
        $this->request['headers'] = $this->getHeader();
        $this->request['query'] = $this->getQueryStringParams();
        $this->request['body'] = $this->getBody();
        $this->request['uri'] = $this->getURI();
        $this->request['params'] = $_REQUEST;

    }
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }


    /** 
    * Get URI elements. 
    * @return array 
    */
    protected function getURI()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }


    /** 
    * Get queryString params. 
    * @return array 
    */
    protected function getQueryStringParams()
    {
        $output = $_SERVER['QUERY_STRING'];
        parse_str($output, $query);
        return $query;
    }


    /** 
    * Send API output. 
    * @param mixed $data 
    * @param string $httpHeader 
    */
    protected function sendOutput($data, $httpHeaders = array())
    {
        header_remove('get-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }


    public function getBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function getHeader(){
        return apache_request_headers();
    }

    public function getGlobalRequest()
    {
        return $this->request ;
    }


    function checkHeader()
    {
        $headers = apache_request_headers();
        $providerToken = new JwtToken();

        if (isset($headers['Authorization']) && !empty($headers['Authorization'])) {    
            $authToken = substr($headers['Authorization'], 7);
            return $providerToken->verifyToken($authToken);
        }
    }
    
}


