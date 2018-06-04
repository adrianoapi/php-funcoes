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
 * Como var count é estático, ele retém seu valor anterior sempre que a função é
 * executada.
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

echo "<hr>";

echo <<<END_COMMENT
/*
 * Como o número inteiro é "300" não está no intervalo especificado e a saída do
 * código acima será: "Integer is not valid".
 */
END_COMMENT;
$var         = 300;
$int_options = array( "options" => array ( "min_range" => 0, "max_range" => 256 ) );
if (!filter_var( $var, FILTER_VALIDATE_INT, $int_options ))
    echo("Integer is not valid");
else
    echo("Integer is valid");

echo "<hr>";

echo ord ("hi");