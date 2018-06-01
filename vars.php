<?php

require '_config.php';

echo <<<END_COMMENT
/*
 * O signal de & ir� referenciar a variavel bar.
 */
END_COMMENT;
$foo = 'Bob';              
$bar = &$foo;              
$bar = "My name is $bar";  
echo $bar;
echo $foo;

echo "<hr>";

echo <<<END_COMMENT
/*
 * O php trata Strings da mesma forma que arrays, permitindo que caracteres espec�ficos
 * sejam acessados via nota��o offset de array.
 */
END_COMMENT;
$color = "maroon";
$var   = $color[2];
echo "$var";

echo "<hr>";

echo <<<END_COMMENT
/*
 * Como var count � est�tico, ele ret�m seu valor anterior sempre que a fun��o � executada.
 */
END_COMMENT;
function track() {
    static $count = 0;
    $count++;
    echo $count;
}
track();
track();
track();