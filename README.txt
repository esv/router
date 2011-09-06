Django-like url routing

First, define rules - url patterns and their controllers.
Controller (called view in Django) may be class or callable:

$rules = array(
  '/url_pattern/(\w+)/(\d+)' => 'Controller_Class_Name',
  '/another_url/(\w+)' => array('Callable_Controller_Name', function ($param) {return $param;}),
);

Controller class must implement methods according to used HTTP request methods:

class Controller_Class_Name 
{
  public function get($name, $id) { return 'method GET!'; }
  public function post($name, $id) { return 'method POST!'; }
}

Run:

$r = new Router($rules);
$r->run();

Make urls from controller name and params to stay DRY without hardcodes:

$r->url_for('Controller_Class_Name', 'admin', 1);
$r->url_for('Callable_Controller_Name', 'word');
