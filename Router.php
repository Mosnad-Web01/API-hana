<?php

require 'controller\todo.php';
require 'controller\auth.php';
require 'middleware\auth.php';
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router
{
    private $todoController;
    private $authController;
    private  $authMiddleware;

    public function __construct($pdo)
    {
        // Link controller
        $this->todoController = new TodoController($pdo);
        $this->authController = new AuthController($pdo);
        $this->authMiddleware = new AuthMiddleware();
    }

    public function handleRequest($httpMethod, $httpUri)
    {
            $dispatcher = simpleDispatcher(function (RouteCollector $router) {
            $router->addRoute('GET', '/todos', 'getTodos');
            $router->addRoute('GET', '/todos/{id:\d+}', 'getTodo');
            $router->addRoute('POST', '/todos', 'createTodo');
            $router->addRoute('PATCH', '/todos/{id:\d+}', 'updateTodo');
            $router->addRoute('DELETE', '/todos/{id:\d+}', 'deleteTodo');

            $router->addRoute('POST', '/login', 'login');
            $router->addRoute('POST', '/register', 'register');
                $router->addRoute('POST', '/refresh', 'refresh');
//            $router->addRoute('POST', '/logout', 'logout');


        });
        return $dispatcher->dispatch($httpMethod, $httpUri);
    }

    public function invoke($handler, $vars, $jwt= null,$user_id= null)
    {
        if (strpos($handler, 'Todo') !== false) {
            $controller = $this->todoController;
        }else if (strpos($handler, 'login') !== false) {
            $controller = $this->authController;
//        }else if (strpos($handler, 'logout') !== false) {
//            $controller = $this->authController;
        }else if (strpos($handler, 'refresh') !== false) {
            $controller = $this->authController;
        }else if (strpos($handler, 'register') !== false) {
            $controller = $this->authController;
        } else {
            jsonResponse(['message' => 'Handler not found: ' . $handler], 404,'error');
            return;
        }


        if (in_array($handler, ['updateTodo'])){
            //$this->authMiddleware->validateJWT($jwt);
            $user_id= $this->authMiddleware->getUserFromJWT($jwt);

        }
        if (method_exists($controller, $handler)) {

            call_user_func_array([$controller, $handler], array_merge( [$user_id],array_values($vars)));
        } else {
            jsonResponse(['message' => 'Method not found in controller: ' . $handler], 404,'error');
        }
    }
}
