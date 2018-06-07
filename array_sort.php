<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP ARRAY - SORT
# -----------------------------------------------------------------------------
# Exemplos de ordenacao de array's
------------------------------------------------------------------------------*/


/*------------------------------------------------------------------+
| sorting arrays                                                    |
+-------------------------------------------------------------------+
| sort() and rsort()   | For sorting indexed arrays                 |
+----------------------+--------------------------------------------+
| asort() and arsort() | For sorting associative arrays             |
+----------------------+--------------------------------------------+
| ksort() and krsort() | For sorting associative arrays by key      |
|                      | rather than by value                       |
+----------------------+--------------------------------------------+
| array_multisort()    | A powerful function that can sort multiple |
|                      | arrays at once, or multidimensional arrays |
+----------------------+-------------------------------------------*/

$games = array(
    "Winning Eleven Final Version 3",
    "Ridge Racer Type 4",
    "Tony Hawk Pro Skater 2",
    "Gran Turismo 2"
);

echo <<<END_COMMENT
/*
 * [sort] Ordena uma lista de autores por ordem alfabética ascendente e descendente
 */
END_COMMENT;
$authors = array( "Steinbeck", "Kafka", "Tolkien", "Dickens" );
sort ( $authors );
debug( $authors ); # Displays Array( [0] => Dickens [1] => Kafka [2] => Steinbeck [3] => Tolkien )
rsort( $authors );
debug( $authors ); # Displays Array( [0] => Tolkien [1] => Steinbeck [2] => Kafka [3] => Dickens )
?>