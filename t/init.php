<?php
require_once('../lib/Router.php');

class Ctl_Static {
    public function get() {
        echo __METHOD__ . " called\n";
    }
    public function delete() {
        echo __METHOD__ . " called\n";
    }
}
class Ctl_Dyn {
    public function get($nickname, $userid) {
        echo __METHOD__ . " $nickname, $userid called\n";
    }
    public function put($nickname, $userid) {
        echo __METHOD__ . " $nickname, $userid called\n";        
    }    
}
$rules = array(
    '/static' => 'Static',
    '/static1' => array('Static1', function () {echo "static1 callable\n";}),
    '/(\w+)/post/(\d+)' => 'Dyn',
    '/(\w+)/postc/(\d+)' => array('Dyn1', function ($nickname, $userid) {echo "dyn1 callable $nickname, $userid\n";}),            
);
$r = new Router($rules, function () {print "404 handler called\n";}, 'Ctl_');
