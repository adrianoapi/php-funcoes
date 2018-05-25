<?php

require '_config.php';

/*
 * Function extract
 */
$a = "Original";
$my_array = array( "a" => "Cat","b" => "Dog", "c" => "Horse" );
extract( $my_array );
debug  ( "\$a = $a; \$b = $b; \$c = $c \n" );                                   // $a = Cat; $b = Dog; $c = Horse

/*
 * Passando parametro EXTR_PREFIX_SAME na funcao extract
 */
extract( $my_array, EXTR_PREFIX_SAME, 'dup' );
debug  ( "\$a = $a; \$b = $b; \$c = $c \$dup_b = $dup_b \n" );                  // $a = Cat; $b = Dog; $c = Horse $dup_b = Dog  

/*
 * Ordena o array pelo indice
 */
$product = array( "C"=>"Pen", "D"=>"Pencil", "A"=>"Copy", "B"=>"Book" );
ksort( $product );
debug( $product );                                                              // [A] => Copy, [B] => Book, [C] => Pen, [D] => Pencil

/*
 * Ordena o array pelo valor
 */
$array = array("a1"=>'x',"a2"=>'e',"a3"=>'z');
asort( $array );
debug( $array );                                                                // [a2] => e, [a1] => x, [a3] => z


?>