--TEST--
Basic Router::route usage
--FILE--
<?php
require_once('init.php');
$c = $r->route('/static');
var_dump($c);
$c = $r->route('/static1');
var_dump($c);
?>
--EXPECT--
array(2) {
  [0]=>
  string(6) "Static"
  [1]=>
  array(0) {
  }
}
array(2) {
  [0]=>
  object(Closure)#1 (0) {
  }
  [1]=>
  array(0) {
  }
}
