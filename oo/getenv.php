<?php require '../_config.php'; 

debug(getenv('REMOTE_ADDR'));

echo "<hr>";

$ip = getenv('REMOTE_ADDR', true) ?: getenv('REMOTE_ADDR');
debug($ip);
?>