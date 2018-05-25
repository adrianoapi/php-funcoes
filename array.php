<?php

require '_config.php';

$a = "Original";
$my_array = array("a" => "Cat","b" => "Dog", "c" => "Horse");
extract($my_array);
debug("\$a = $a; \$b = $b; \$c = $c \n");                                         // $a = Cat; $b = Dog; $c = Horse
extract($my_array, EXTR_PREFIX_SAME, 'dup');
debug("\$a = $a; \$b = $b; \$c = $c \$dup_b = $dup_b \n");                        // $a = Cat; $b = Dog; $c = Horse $dup_b = Dog  
$array = "Initially";
$arr1 = array("a" => "Rose","b" => "Lotus", "c" => "Marigold");
extract($arr1);
debug("\$a = $a; \$b = $b; \$c = $c");

?>