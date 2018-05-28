<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP ARRAY
# -----------------------------------------------------------------------------
# Exemplos de manipulacao de array's
------------------------------------------------------------------------------*/

/*
 * Funcao array_change_key_case usada para converter o indice do array para
 * CASE_LOWER ou CASE_UPPER
 */
$age = array( "Peter"=>"35", "Ben"=>"37", "Joe"=>"43" );
debug( array_change_key_case( $age, CASE_UPPER ) );

/*
 * Divide um array em size pedaчos. O њltimo pedaчo pode conter menos elementos
 * que o parтmetro size.
 */
$cars = array( "Volvo", "BMW", "Toyota", "Honda", "Mercedes", "Opel" );
debug( array_chunk($cars, 2));

/*
 * Cria um array usando um array para chaves e outro para valores 
 */
$fname = array( "Peter", "Ben", "Joe");
$age   = array( "35", "37", "43"     );
debug( array_combine( $fname, $age ) );                                         // [Peter] => 35, [Ben] => 37, [Joe] => 43

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




/*----------------------------------------------------------------------------------------+
| sort_flags                                                                              |
+-----------------------------------------------------------------------------------------+
| SORT_REGULAR       | compara os itens normalmente (nуo modifica o tipo)                 |
+--------------------+--------------------------------------------------------------------+
| SORT_NUMERIC       | compara os items numericamente                                     |
+--------------------+--------------------------------------------------------------------+
| SORT_STRING        | compara os itens como strings                                      |
+--------------------+--------------------------------------------------------------------+
| SORT_LOCALE_STRING | compara os itens como strings, utilizando o locale atual. Utiliza  |
|                    | o locale que pode ser modificado com setlocale()                   |
+--------------------+--------------------------------------------------------------------+
| SORT_NATURAL       | compara os itens como strings utilizando "ordenaчуo natural" tipo  |
|                    | natsort()                                                          |
+--------------------+--------------------------------------------------------------------+
| SORT_FLAG_CASE     | pode ser combinado (bitwise OR) com SORT_STRING ou SORT_NATURAL    |
|                    | para ordenar strings sem considerar maiњsculas e minњsculas        |
+--------------------+-------------------------------------------------------------------*/
        

/*
 * Essa funчуo ordena um array. Os elementos serуo ordenados do menor para o maior
 * ao final da execuчуo dessa funчуo.
 */
$arr = array ( "picture1.JPG", "picture2.jpg", "Picture10.jpg", "picture20.jpg" );
sort ( $arr );
debug( $arr );                                                                  // [0] => Picture10.jpg, [1] => picture1.JPG, [2] => picture2.jpg, [3] => picture20.jpg

/*
 * Exemplo de sort() utilizando comparaчѕes naturais ignorando maiњsculas e minњculas
 */
$fruits = array( "Orange1", "orange20", "orange2", "Orange3"  );
sort ( $fruits, SORT_NATURAL | SORT_FLAG_CASE );
debug( $fruits );

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

/*
 * retorna um array com suas relaчѕes trocadas, ou seja, as chaves de array passam
 * a ser os valores e os valores de array passam a ser as chaves.
 */
$fruits = array( "mango", "apple", "pear", "peach" );
$fruits = array_flip( $fruits );
debug( $fruits );                                                               // [mango] => 0, [apple] => 1, [pear] => 2, [peach] => 3

/*
 * Funde os elementos de dois ou mais arrays de forma que os elementos de um sуo
 * colocados no final do array anterior. 
 */
$face   = array ( "A", "J", "Q", "K" );
$number = array ( "2","3","4", "5", "6", "7", "8", "9", "10" );
$cards  = array_merge ( $face, $number );
debug( $cards );                                                                // [0] => A, [1] => J, [2] => Q, [3] => K, [4] => 2, [5] => 3...

/*
 * array_slice() retorna a sequъncia de elementos de um array delimitado pelos
 * parтmetros offset e length.
 */
$fruits = array ("apple", "mango", "peach", "pear", "orange");
debug( array_slice( $fruits,  2    ) );                                         // returns "peach", "pear", and "orange"
debug( array_slice( $fruits, -2, 1 ) );                                         // returns "pear"
debug( array_slice( $fruits,  0, 3 ) );                                         // returns "apple", "mango", and "peach"

/*
 * Soma todas os valores numericos do array
 */
$number = array ("4", "hello", 2);
debug( array_sum( $number ) );                                                  // 6

/*
 * Retorna um array contendo todos os valores em array1 cujo existem em todos
 * os parтmetros.
 */
$array1 = array( "a" => "verde", "vermelho", "azul"    );
$array2 = array( "b" => "verde", "amarelo", "vermelho" );
debug( array_intersect( $array1, $array2 ) );                                   // [a] => verde, [0] => vermelho

?>