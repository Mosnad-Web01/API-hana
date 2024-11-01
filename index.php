<?php
require 'vendor\autoload.php';
//require_once __DIR__ . '/vendor/autoload.php';

use Core\App;
use Core\LoggerUtility;
const BASE_PATH = __DIR__ . '/';
require BASE_PATH . 'Core/utils.php';


require 'Router.php';
require 'view/response.php';


spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once base_path($class . '.php');
});

require base_path("starter.php");

$pdo = App::resolve('Core/Database');

// تسجيل بداية الـ API
LoggerUtility::logInput('API Start');

header("Content-type: application/json");;
//dd(getallheaders());

$router = new Router($pdo);

$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$headers = getallheaders();

$authorization = $headers['Authorization'] ?? '';


//
//
$jwt = str_replace('Bearer ', '', $authorization);


$routerInfo = $router->handleRequest($method, $uri);

switch ($routerInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        jsonResponse(['message' => 'Not Found!',404],'error');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        jsonResponse(['message' => 'Method Not Allowed!',405],'error');
    case FastRoute\Dispatcher::FOUND:
        $handler = $routerInfo[1];
        $var = $routerInfo[2];
        $router->invoke($handler, $var,$jwt);
        break;
}