<?php

/**
 * Django-like url routing
 * 
 * First, define rules - url patterns and their controllers.
 * Controller (called view in Django) may be class or callable:
 * 
 * $rules = array(
 *   '/url_pattern/(\w+)/(\d+)' => 'Controller_Class_Name',
 *   '/another_url/(\w+)' => array('Callable_Controller_Name', function ($param) {return $param;}),
 * );
 * 
 * Controller class must implement methods according to used HTTP request methods:
 * 
 * class Controller_Class_Name 
 * {
 *   public function get($name, $id) { return 'method GET!'; }
 *   public function post($name, $id) { return 'method POST!'; }
 * }
 * 
 * Run:
 * 
 * $r = new Router($rules);
 * $r->run();
 * 
 * Make urls from controller name and params to stay DRY without hardcodes:
 * 
 * $r->url_for('Controller_Class_Name', 'admin', 1);
 * $r->url_for('Callable_Controller_Name', 'word');
 *
 * 
 * @author Evgeny Sverkunov
 * 
 */

class Router 
{
    /**
     * Prefix to add to classes in $rules
     * 
     * @var string
     */
    private $prefix;     
    
    /**
     * Controller that handles 404 error
     * 
     * @var mixed
     */
    private $ctl404;

    /**
     * Ralues table
     *     rule => class name
     *         or
     *     rule => (ctl name, callable)
     * 
     * @var array 
     */
    private $rules;
    
    /**
     * Reversed array to make links from ctl name
     * 
     * @var array
     */
    private $flipRules = array();
    

    public function __construct($rules, $ctl404, $prefix = '')
    {
        $this->rules = $rules;
        $this->ctl404 = $ctl404;
        $this->prefix = $prefix;
        
        // Get names from arrays
        foreach ($rules as &$r) {
            if (is_array($r)) {
                $r = $r[0];
            }
        }
        
        // Reversed array to make links from ctl name
        $this->flipRules = array_flip($rules);        
        foreach ($this->flipRules as &$rule) {
            $rule = preg_replace('/\([^\)]*\)/', '%s', $rule);
        }
        
    }
    
    /**
     * Detect controller
     * 
     * @param string $url Instead of REQUEST_URI
     * @param string $method Instead of REQUEST_METHOD
     */
    public function route($url = null, $method = null) 
    {
        $url = $url ? $url : $_SERVER['REQUEST_URI'];
        $url = strpos($url, '?') ? substr($url, 0, strpos($url, '?')) : $url; // Remove GET params
        $url = rtrim($url, '/');
        $url = urldecode($url);

        // Search can be optimized
        foreach ($this->rules as $route => $controller) {
            if (preg_match('?^' . $route . '$?u', $url, $matches)) {
                array_shift($matches);
                $controller = is_array($controller) ? $controller[1] : $controller;
                return array($controller, $matches);
            }
        }
        
        return array($this->ctl404, array());
    }
        
    /**
     * Detect controller and run
     * 
     * @param string $url Instead of REQUEST_URI
     * @param string $method Instead of REQUEST_METHOD
     */
    public function run($url = null, $method = null)
    {
        list($controller, $params) = $this->route($url, $method);
        
        if (is_string($controller)) {
            $method = $method ? $method : strtolower($_SERVER['REQUEST_METHOD']);
            $controller = $this->prefix . $controller;
            $controller = new $controller();
            $controller = array($controller, $method);
        }
        
        return call_user_func_array($controller, $params);
    }

    /**
     * Make link from ctl name and params
     *
     */
    public function url_for()
    {
        $args = func_get_args();
        $controller = (string) $args[0];
        array_shift($args);
        
        foreach ($args as &$a) {
            $a = urlencode($a);
        }
        
        if (!isset($this->flipRules[$controller])) {
            throw new Router_Exception('Controller not found');
        }
        
        $url = $this->flipRules[$controller];
        
        if (substr_count($url, '%s') != count($args)) {
            throw new Router_Exception('Params count mismatch');
        }
        
        return vsprintf($url, $args);
    }
        
}

class Router_Exception extends Exception {}

