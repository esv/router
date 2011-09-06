--TEST--
Basic Router::run usage
--FILE--
<?php
require_once('init.php');
$r->run('/static', 'get');
$r->run('/static', 'delete');
$r->run('/static1', 'get');
$r->run('/user1/post/123', 'get');
$r->run('/user2/post/4564567', 'put');
$r->run('/user3/postc/9999999', 'get');
$r->run('/user3/postcdsf/9999999', 'get');
?>
--EXPECT--
Ctl_Static::get called
Ctl_Static::delete called
static1 callable
Ctl_Dyn::get user1, 123 called
Ctl_Dyn::put user2, 4564567 called
dyn1 callable user3, 9999999
404 handler called
