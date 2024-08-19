<?php

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
header('Content-Type: application/json');

require 'vendor/autoload.php';
require 'src/Utils/config.php';

use App\Routing\Router;

$router = new Router();
echo $router->run();