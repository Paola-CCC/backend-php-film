<?php

declare(strict_types=1);

namespace App\Routing;

use Exception;

class Router
{

    public Routes $routesCollection ;
    public $routes ;
    
    public function __construct() 
    {
        $this->routesCollection = new Routes();
        $this->routes = $this->getAllRoutes() ;
    }
   
    function run()
    {
        $urlID = '' ;
        $output = $_SERVER['QUERY_STRING'];
        parse_str($output, $query);

        $method = $_SERVER['REQUEST_METHOD'];
        $requestURL = $query['url'];
        

        foreach ($this->routes as $route) {

            if ($route['method'] !== $method) {
                continue;
            }

            if ($requestURL === trim($route['path'], '/') &&  $method === $route['method']) {

                if (is_callable($route['handler'])) {
                    return $route['handler']() ;
                }

                if (is_array($route['handler'])) {
                    foreach ($route['handler'] as $key_array => $actionFromArray) {
                        $className = ucfirst($key_array);
                        $action = $actionFromArray;
                    }

                    $fullClassName = 'App\\Controllers\\' . $className . 'Controller';

                    if (class_exists($fullClassName) && method_exists($fullClassName, $action)) {

                        if(isset($query['id'])){
                            $urlID = $query['id'] ;
                        }

                        $class = new $fullClassName();

                        if($urlID) {
                            return $class->$action($urlID);
                        } else {
                            return $class->$action();
                        }

                    } else {
                        http_response_code(404);
                        echo json_encode(['error 404' => "La page recherchée n'existe pas"]);
                    }
                }
            }
        }
    }

    public function getAllRoutes( )
    {  
        return $this->routesCollection->getRoutes() ;
    }

    public function handleException(\Throwable $exception): void
    {
        if ($exception instanceof Exception) {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => $exception->getMessage()]);
        } elseif ($exception instanceof Exception) {
            header('HTTP/1.0 405 Method Not Allowed');
            echo json_encode(['error' => $exception->getMessage()]);
        } else {
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode(['error' => 'An error occurred']);
        }
    }


    public function useRegexToGetID($nameController, $idParams)
    {

        $IDElement = null;
        $url = $_SERVER['REQUEST_URI'];
        $patterns = '\/'. $nameController . '\/'. $idParams;
        $pattern = '/' .  $patterns . '/';
        if (preg_match($pattern, $url, $matches)) {
            $IDElement = $matches[1]; 
            return $IDElement;
        };  
    }

}
