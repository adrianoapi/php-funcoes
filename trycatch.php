<?php

$a = ['apple', 'banana', 'lemon'];

echo "<pre>";
echo serialize($a)  .'<br/>';
echo implode($a,',').'<br/>';
echo implode(',',$a).'<br/>';
echo "</pre>";
echo 1 ^ 2;
?>