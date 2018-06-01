<?php

require '_config.php';

echo <<<END_COMMENT
/*
 * O signal de & irá referenciar a variavel bar.
 */
END_COMMENT;
$foo = 'Bob';              
$bar = &$foo;              
$bar = "My name is $bar";  
echo $bar;
echo $foo;