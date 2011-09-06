--TEST--
Basic Router::url_for usage
--FILE--
<?php
require_once('init.php');
print $r->url_for('Static') . "\n";
print $r->url_for('Static1') . "\n";
print $r->url_for('Dyn', 'user1', 9876) . "\n";
print $r->url_for('Dyn1', 'user2', 1245) . "\n";
try {
    print $r->url_for('Dyn1', 'user2') . "\n";
}
catch (Router_Exception $e) {
    print "ok\n";
}
try {
    print $r->url_for('Dyn12', 'user2') . "\n";
}
catch (Router_Exception $e) {
    print "ok\n";
}
?>
--EXPECT--
/static
/static1
/user1/post/9876
/user2/postc/1245
ok
ok