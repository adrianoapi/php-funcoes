<?php

$a = "Original";
$my_array = array("a" => "Cat","b" => "Dog", "c" => "Horse");
extract($my_array);

echo "<pre>";
echo "\$a = $a; \$b = $b; \$c = $c";
echo "</pre>";

?>