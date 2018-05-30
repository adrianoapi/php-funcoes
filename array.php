<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP ARRAY
# -----------------------------------------------------------------------------
# Exemplos de manipulacao de array's
------------------------------------------------------------------------------*/

echo <<<END_COMMENT
/*
 * [pos, next, prev, end] Navega pelos itens do array
 */
END_COMMENT;
$people = array("Peter", "Susan", "Edmund", "Lucy");
debug( pos( $people ) );
debug( next( $people ) );
debug( end( $people ) );
debug( prev( $people ) );

echo <<<END_COMMENT
/*
 * [array_replace] Substitui elementos do primeiro array com os elementos do seugndo.
 */
END_COMMENT;
$city_west = array( "NYC", "London" );
$city_east = array( "Mumbai", "Beijing" );
debug( array_replace( $city_west, $city_east ) );

echo <<<END_COMMENT
/*
 * [array_search] Procura por um valor em um array e retorna sua chave correspondente
 * caso seja encontrado.
 */
END_COMMENT;
$a = array("a" => "Jaguar", "b" => "Land Rover", "c" => "Audi", "d" => "Maseratti");
debug( array_search( "Audi", $a ) );

echo <<<END_COMMENT
/*
 * [array_product] Calcula o produto dos valores de um array, multiplicando da
 * direita para a esquerda.
 */
END_COMMENT;
$a = array(5, 5, 2, 2);
debug( array_product( $a ) );

echo <<<END_COMMENT
/*
 * [array_change_key_case] Funcao array_change_key_case usada para converter o 
 * indice do array para CASE_LOWER ou CASE_UPPER
 */
END_COMMENT;
$age = array( "Peter"=>"35", "Ben"=>"37", "Joe"=>"43" );
debug( array_change_key_case( $age, CASE_UPPER ) );

echo <<<END_COMMENT
/*
 * [array_chunk] Divide um array em size peda�os. O �ltimo peda�o pode conter menos
 * elementos que o par�metro size.
 */
END_COMMENT;
$cars = array( "Volvo", "BMW", "Toyota", "Honda", "Mercedes", "Opel" );
debug( array_chunk($cars, 2));

echo <<<END_COMMENT
/*
 * [array_combine] Cria um array usando um array para chaves e outro para valores 
 */
END_COMMENT;
$fname = array( "Peter", "Ben", "Joe" );
$age   = array( "35", "37", "43"      );
debug( array_combine( $fname, $age )  );                                        // [Peter] => 35, [Ben] => 37, [Joe] => 43

echo <<<END_COMMENT
/*
 * [array_count_values] Conta todos os valores de um array
 */
END_COMMENT;
$a = array( "A", "Cat", "Dog", "A", "Dog" );
debug( array_count_values( $a ) );                                              // Array ( [A] => 2 [Cat] => 1 [Dog] => 2

echo <<<END_COMMENT
/*
 * [array_diff] Compara os valores do array1 com array2 e retorna a diferen�a
 */
END_COMMENT;
$a1 = array( "a"=>"red","d"=>"yellow", "b"=>"green",  "c"=>"blue"  );
$a2 = array( "e"=>"red", "f"=>"green", "g"=>"blue" );
debug( array_diff( $a1, $a2 ) );                                                // [d] => yellow

echo <<<END_COMMENT
/*
 * [array_fill] Preenche um array com num elementos com o valor do par�metro value
 * e chaves come�ando a partir de start_index.
 */
END_COMMENT;
$a1 = array_fill( 3, 4, "blue" );                                               // [3] => blue, [4] => blue, [5] => blue, [6] => blue
$b1 = array_fill( 0, 1, "red"  );                                               // [0] => red
debug( $a1 );
echo "<br>";
debug( $b1 );

echo <<<END_COMMENT
/*
 * [array_pop] Remove o ultimo item do array
 */
END_COMMENT;
$a = array("red", "green", "blue");
array_pop ( $a );
debug     ( $a );

echo <<<END_COMMENT
/*
 * Function extract
 */
END_COMMENT;
$a = "Original";
$my_array = array( "a" => "Cat","b" => "Dog", "c" => "Horse" );
extract( $my_array );
debug  ( "\$a = $a; \$b = $b; \$c = $c \n" );                                   // $a = Cat; $b = Dog; $c = Horse

echo <<<END_COMMENT
/*
 * Passando parametro EXTR_PREFIX_SAME na funcao extract
 */
END_COMMENT;
extract( $my_array, EXTR_PREFIX_SAME, 'dup' );
debug  ( "\$a = $a; \$b = $b; \$c = $c \$dup_b = $dup_b \n" );                  // $a = Cat; $b = Dog; $c = Horse $dup_b = Dog  

echo <<<END_COMMENT
/*
 * Essa fun��o ordena um array. Os elementos ser�o ordenados do menor para o maior
 * ao final da execu��o dessa fun��o.
 */
END_COMMENT;
$arr = array ( "picture1.JPG", "picture2.jpg", "Picture10.jpg", "picture20.jpg" );
sort ( $arr );
debug( $arr );                                                                  // [0] => Picture10.jpg, [1] => picture1.JPG, [2] => picture2.jpg, [3] => picture20.jpg

/*----------------------------------------------------------------------------------------+
| sort_flags                                                                              |
+-----------------------------------------------------------------------------------------+
| SORT_REGULAR       | compara os itens normalmente (n�o modifica o tipo)                 |
+--------------------+--------------------------------------------------------------------+
| SORT_NUMERIC       | compara os items numericamente                                     |
+--------------------+--------------------------------------------------------------------+
| SORT_STRING        | compara os itens como strings                                      |
+--------------------+--------------------------------------------------------------------+
| SORT_LOCALE_STRING | compara os itens como strings, utilizando o locale atual. Utiliza  |
|                    | o locale que pode ser modificado com setlocale()                   |
+--------------------+--------------------------------------------------------------------+
| SORT_NATURAL       | compara os itens como strings utilizando "ordena��o natural" tipo  |
|                    | natsort()                                                          |
+--------------------+--------------------------------------------------------------------+
| SORT_FLAG_CASE     | pode ser combinado (bitwise OR) com SORT_STRING ou SORT_NATURAL    |
|                    | para ordenar strings sem considerar mai�sculas e min�sculas        |
+--------------------+-------------------------------------------------------------------*/
        

echo <<<END_COMMENT
/*
 * Exemplo de sort() utilizando compara��es naturais ignorando mai�sculas e min�culas
 */
END_COMMENT;
$fruits = array( "Orange1", "orange20", "orange2", "Orange3"  );
sort ( $fruits, SORT_NATURAL | SORT_FLAG_CASE );
debug( $fruits );

echo <<<END_COMMENT
/*
 * Ordena o array pelo indice
 */
END_COMMENT;
$product = array( "C"=>"Pen", "D"=>"Pencil", "A"=>"Copy", "B"=>"Book" );
ksort( $product );
debug( $product );                                                              // [A] => Copy, [B] => Book, [C] => Pen, [D] => Pencil

echo <<<END_COMMENT
/*
 * [asort] Ordena o array pelo valor
 */
END_COMMENT;
$array = array( "a1"=>'x', "a2"=>'e', "a3"=>'z' );
asort( $array );
debug( $array );                                                                // [a2] => e, [a1] => x, [a3] => z

echo <<<END_COMMENT
/*
 * [array_flip] Retorna um array com suas rela��es trocadas, ou seja, as chaves 
 * de array passam a ser os valores e os valores de array passam a ser as chaves.
 */
END_COMMENT;
$fruits = array( "mango", "apple", "pear", "peach" );
$fruits = array_flip( $fruits );
debug( $fruits );                                                               // [mango] => 0, [apple] => 1, [pear] => 2, [peach] => 3

echo <<<END_COMMENT
/*
 * [array_merge] Funde os elementos de dois ou mais arrays de forma que os elementos
 * de um s�o colocados no final do array anterior. 
 */
END_COMMENT;
$face   = array ( "A", "J", "Q", "K" );
$number = array ( "2", "3", "4", "5", "6", "7", "8", "9", "10" );
$cards  = array_merge ( $face, $number );
debug( $cards );                                                                // [0] => A, [1] => J, [2] => Q, [3] => K, [4] => 2, [5] => 3...

echo <<<END_COMMENT
/*
 * [array_shift] Retira o primeiro elemento de um array
 */
END_COMMENT;
$a = array( "a"=>"red", "b"=>"green", "c"=>"blue" );
debug ( array_shift( $a ) );                                                    // red
debug ( ( $a ) );                                                               // [b] => green, [c] => blue

echo <<<END_COMMENT
/*
 * array_slice() retorna a sequ�ncia de elementos de um array delimitado pelos
 * par�metros offset e length.
 */
END_COMMENT;
$fruits = array ("apple", "mango", "peach", "pear", "orange");
debug( array_slice( $fruits,  2    ) );                                         // returns "peach", "pear", and "orange"
debug( array_slice( $fruits, -2, 1 ) );                                         // returns "pear"
debug( array_slice( $fruits,  0, 3 ) );                                         // returns "apple", "mango", and "peach"

echo <<<END_COMMENT
/*
 * Soma todas os valores numericos do array
 */
END_COMMENT;
$number = array ("4", "hello", 2);
debug( array_sum( $number ) );                                                  // 6

echo <<<END_COMMENT
/*
 * Retorna um array contendo todos os valores em array1 cujo existem em todos
 * os par�metros.
 */
END_COMMENT;
$array1 = array( "a" => "verde", "vermelho", "azul"    );
$array2 = array( "b" => "verde", "amarelo", "vermelho" );
debug( array_intersect( $array1, $array2 ) );                                   // [a] => verde, [0] => vermelho

?>