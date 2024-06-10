<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\JwtToken;

class HttpRequest
{

    public array $requestInfos = [];

    public function __construct()
    {
        $this->requestInfos['method'] = $this->getMethod();
        $this->requestInfos['headers'] = $this->getHeader();
        $this->requestInfos['query_params'] = $this->getQueryStringParams();
        $this->requestInfos['body'] = $this->getBody();
        $this->requestInfos['uri'] = $this->getURI();
    }

    public function getGlobalRequest():array
    {
        return $this->requestInfos ;
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /** 
    * Get URI elements. 
    * @return array 
    */
    public function getURI()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $uri;
    }

    /** 
    * Get queryString params. 
    * @return array 
    */
    public function getQueryStringParams(): array
    {
        $output = $_SERVER['QUERY_STRING'];
        parse_str($output, $query);
        return $query;
    }

    public function getBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function getHeader()
    {
        return apache_request_headers();
    }

    public function provideJwtToken(string $authToken)
    {
        $providerToken = new JwtToken();
        return $providerToken->verifyToken($authToken);
    }

    public function getJWTAuthorization(): string
    {

        $headers = $this->getHeader();

        if (isset($headers['Authorization']) && !empty($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                $this->provideJwtToken($matches[1]);
                return $matches[1];
            }
        } 
    }
}


