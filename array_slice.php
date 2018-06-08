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
array_unshift($authors, "Hardy", "Melville"); # Displays 6
debug( $authors ); # Array( [0] => Hardy [1] => Melville [2] => Steinbeck [3] => Kafka [4] => Tolkien [5] => Dickens )

echo <<<END_COMMENT
/*
 * [array_shift]  Removes the first element from an array, and returns its value,
 * but not its key
 */
END_COMMENT;
$myBook = array( "title" => "The Grapes of Wrath",
                 "author" => "John Steinbeck",
                 "pubYear" => 1939 );
array_shift( $myBook ); # Displays "The Grapes of Wrath"
debug      ( $myBook ); # Array( [author] => John Steinbeck [pubYear] => 1939 )
?>