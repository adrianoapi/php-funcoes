<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP ARRAY - SORT
# -----------------------------------------------------------------------------
# Exemplos de manipulação de array
------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------+
| Adding and Removing Array Elements                                       |
+--------------------------------------------------------------------------+
| array_unshift() | Adds one or more new elements to the start of an array |
+-----------------+--------------------------------------------------------+
| array_shift()   | Removes the first element from the start of an array   |
+-----------------+--------------------------------------------------------+
| array_push()    | Adds one or more new elements to the end of an array   |
+-----------------+--------------------------------------------------------+
| array_pop()     | Removes the last element from the end of an array      |
+-----------------+--------------------------------------------------------+
| array_splice()  | Removes element(s) from and/or adds element(s) to any  |
|                 | point in an array                                      |
+-----------------+-------------------------------------------------------*/

echo <<<END_COMMENT
/*
 * [array_unshift] Insert an element or elements at the start of an array
 */
END_COMMENT;
$authors = array( "Steinbeck", "Kafka", "Tolkien", "Dickens" );
array_unshift($authors, "Hardy", "Melville" );                                  // Displays 6
debug( $authors ); # Array( [0] => Hardy [1] => Melville [2] => Steinbeck [3] => Kafka [4] => Tolkien [5] => Dickens )

echo <<<END_COMMENT
/*
 * [array_shift]  Removes the first element from an array, and returns its value,
 * but not its key
 */
END_COMMENT;
$myBook = array( "title"   => "The Grapes of Wrath",
                 "author"  => "John Steinbeck",
                 "pubYear" => 1939 );
array_shift( $myBook );                                                         // Displays "The Grapes of Wrath"
debug      ( $myBook ); # Array( [author] => John Steinbeck [pubYear] => 1939 )

echo <<<END_COMMENT
/*
 * [array_push] Insert an element or elements at the end of an array
 */
END_COMMENT;
$authors = array( "Steinbeck", "Kafka", "Tolkien", "Dickens" );
array_push( $authors, "Hardy", "Melville" );                                    // Displays "6"
debug     ( $authors ); # Array( [0] => Steinbeck [1] => Kafka [2] => Tolkien [3] => Dickens [4] => Hardy [5] => Melville )

$authors  = array( "Steinbeck", "Kafka", "Tolkien", "Dickens" );
$nAuthors = array( "Hardy", "Melville" );
array_push( $authors, $nAuthors );
debug     ( $authors );

echo <<<END_COMMENT
/*
 * [array_pop]  Removes the last element from an array, and returns its value,
 * but not its key
 */
END_COMMENT;
$myBook = array( "title"   => "The Grapes of Wrath",
                 "author"  => "John Steinbeck",
                 "pubYear" => 1939 );
array_pop( $myBook );                                                           // Displays "1939"
debug    ( $myBook ); # Array( [title] => The Grapes of Wrath [author] => John Steinbeck )

echo <<<END_COMMENT
/*
 * [array_splice]  Retorna elementos de uma array subscrevendo por novos e mudando
 * o indice dos novos elementos
 */
END_COMMENT;
$a1 = array( "a" => "red", "b" => "green", "c" => "blue", "d" => "yellow" );
$a2 = array( "a" => "purple", "b" => "orange" );
array_splice( $a1, 0, 2, $a2 );                                                 // Array ( [a] => red [b] => green ) 
debug       ( $a1 ); # Array([0] => purple [1] => orange [c] => blue [d] => yellow)


$a1 = array( "0" => "red", "1" => "green" );
$a2 = array( "0" => "purple", "1" => "orange" );
array_splice( $a1, 1, 0, $a2 );
debug       ( $a1 ); # Array( [0] => red [1] => purple [2] => orange [3] => green )

echo <<<END_COMMENT
/*
 * [array_merge] This function takes one or more arrays as arguments, and returns
 * the merged array. The original array(s) are not affected.
 */
END_COMMENT;
$authors     = array( "Steinbeck", "Kafka" );
$moreAuthors = array( "Tolkien", "Milton"  );
$newArray    = array_merge( $authors, $moreAuthors );
debug( $newArray ); # Array( [0] => Steinbeck [1] => Kafka [2] => Tolkien [3] => Milton )

$myBook = array( "title"   => "The Grapes of Wrath",
                 "author"  => "John Steinbeck",
                 "pubYear" => 1939 );
$myBook = array_merge( $myBook, array( "title" => "East of Eden", "pubYear" => 1952 ) );
debug($myBook); # Array( [title] => East of Eden [author] => John Steinbeck [pubYear] => 1952 )
?>