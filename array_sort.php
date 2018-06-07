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

echo <<<END_COMMENT
/*
 * [sort] Ordenando Associative Arrays with asort() and arsort()
 * Notice how sort() has reindexed the associative array, replacing the original
 * string keys with numeric keys and effectively turning the array into an
 * indexed array
 */
END_COMMENT;
$myBook = array( "title"  => "Bleak House",
                 "author" => "Dickens",
                 "year"   => 1853 );
sort ( $myBook );
debug( $myBook ); # Array( [0] => Bleak House [1] => Dickens [2] => 1853 )

echo <<<END_COMMENT
/*
 * [asort] This is where asort() and arsort() come in. They work just like sort()
 * and rsort(), but they preserve the association between each element's key and
 * its value:
 */
END_COMMENT;
$myBook = array( "title"  => "Bleak House",
                 "author" => "Dickens",
                 "year"   => 1853 );
asort ( $myBook );
debug ( $myBook ); # Array( [title] => Bleak House [author] => Dickens [year] => 1853 )
arsort( $myBook );
debug ( $myBook ); # Array( [year] => 1853 [author] => Dickens [title] => Bleak House )

echo <<<END_COMMENT
/*
 * [ksort] Ordena pelo indice mantendo os valores originais
 */
END_COMMENT;
$myBook = array( "title"  => "Bleak House",
                 "author" => "Dickens",
                 "year"   => 1853 );
ksort ( $myBook );
debug ( $myBook );
krsort( $myBook );
debug ( $myBook );

echo <<<END_COMMENT
/*
 * [array_multisort] Ordena multiplos arrays pelo indice mantendo os valores originais
 */
END_COMMENT;
$authors  = array( "Steinbeck", "Kafka", "Tolkien", "Dickens" );
$titles   = array( "The Grapes of Wrath", "The Trial", "The Hobbit", "A Tale of Two Cities" );
$pubYears = array( 1939, 1925, 1937, 1859 );
array_multisort( $authors, $titles, $pubYears );
debug( $authors  ); # Array( [0] => Dickens [1] => Kafka [2] => Steinbeck [3] => Tolkien )
debug( $titles   ); # Array( [0] => A Tale of Two Cities [1] => The Trial [2] => The Grapes of Wrath [3] => The Hobbit )
debug( $pubYears ); # Array( [0] => 1859 [1] => 1925 [2] => 1939 [3] => 1937 )

echo <<<END_COMMENT
/*
 * [array_multisort] Ordenou o array pelo valor do primeiro indice de cada um
 */
END_COMMENT;
$myBooks = array(
  array(
    "title" => "The Grapes of Wrath",
    "author" => "John Steinbeck",
    "pubYear" => 1939
  ),
  array(
    "title" => "Travels With Charley",
    "author" => "John Steinbeck",
    "pubYear" => 1962
  ),
  array(
    "title" => "The Trial",
    "author" => "Franz Kafka",
    "pubYear" => 1925
  ),
  array(
    "title" => "The Hobbit",
    "author" => "J. R. R. Tolkien",
    "pubYear" => 1937
  ),
  array(
    "title" => "A Tale of Two Cities",
    "author" => "Charles Dickens",
    "pubYear" => 1859
  ),
);

array_multisort( $myBooks );
debug( $myBooks );

?>