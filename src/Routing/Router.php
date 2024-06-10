<?php

declare(strict_types=1);

namespace App\Routing;

use App\Service\HttpRequest;
use Exception;

class Router
{

    public Routes $routesCollection ;
    public $routes ;
    public $request = null ;
    
    public function __construct() 
    {
        $this->routesCollection = new Routes();
        $this->routes = $this->getAllRoutes() ;
        $this->request = new HttpRequest() ;

    }
   
    public function run()
    {
        $query = $this->request->getQueryStringParams();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {

            if ($route['method'] !== $method) {
                continue;
            }

            if ($query['url'] === trim($route['path'], '/') &&  $method === $route['method']) {

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

                        $class = new $fullClassName();

                        if(isset($query['id'])) {
                            return $class->$action($query['id']);
                        } else if (isset($query['name'])) {
                            return $class->$action((string) $query['name']);
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

    /** affiche toutes les routes crées */
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
}
