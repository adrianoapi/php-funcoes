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

echo "<hr>";

echo <<<END_COMMENT
/*
 * O php trata Strings da mesma forma que arrays, permitindo que caracteres específicos
 * sejam acessados via notação offset de array.
 */
END_COMMENT;
$color = "maroon";
$var   = $color[2];
echo "$var";

echo "<hr>";

echo <<<END_COMMENT
/*
 * Como var count é estático, ele retém seu valor anterior sempre que a função é executada.
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