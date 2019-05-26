<?php

namespace application\core;

use application\infrastructure\Response;

class Router {

    protected $routes = [];
    protected $params = [];

    public function __construct() {

        $arr = require 'application/config/routes.php';

        foreach ($arr as $key => $val) {
            $this->add($key, $val);
        }
    }

    public function add($route, $params) {
        $route = '#^'.$route.'$#';
        $this->routes[$route] = $params;
    }

    public function match() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        foreach ($this->routes as $route => $params) {
            if(preg_match($route, $url, $match)){
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function run() {

        if($this->match()) {
            $path = 'application\controllers\\'.ucfirst($this->params['controller']).'Controller';
            if(class_exists($path)){
                if($_SERVER['REQUEST_METHOD'] !== strtoupper($this->params['method'])) {
                    Response::json(null, "Method not allowed");
                }
               $action = $this->params['action']. 'Action';
               if(method_exists($path, $action)) {
                   $controller = new $path($this->params);
                   $controller->$action();
               } else {
                   Response::json(null, "Action not found");
               }
            } else {
                Response::json(null, "Class not found");
            }
        }
        Response::json(null, "Wrong routing");
    }
}
