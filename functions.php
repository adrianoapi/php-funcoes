<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Funcoes nativas do PHP
------------------------------------------------------------------------------*/

# substr
$myString = "Hello, world!";
echo substr( $myString, 0, 5   ) . "<br/>"; // Displays 'Hello'
echo substr( $myString, 0, -5  ) . "<br/>"; // Displays 'Hello, w'
echo substr( $myString, 7      ) . "<br/>"; // Displays 'world!'
echo substr( $myString, -1     ) . "<br/>"; // Displays '!'
echo substr( $myString, -5, -1 ) . "<br/>"; // Displays 'orld'

echo "<hr>";

$myString = "Hello, world!";
echo $myString[0 ] . "<br/>"; // Displays 'H'
echo $myString[7 ] . "<br/>"; // Displays 'w'
     $myString[12] = '?'    ; // Substring ! => ?
echo $myString . "<br/>"    ; // Displays 'Hello, world?'

echo "<hr>";

# 5.2  Searching Strings
$myString = "Hello, world!";
echo strstr( $myString, "wor" ) . "<br/>";                    // Displays 'world!'
echo ( strstr( $myString, "xtz" ) ? "Yes" : "No" ) . "<br/>"; // Displays 'No'
echo strstr( $myString, "wor", true );                        // Displays 'Hello,'
# Returns the portion from the start of the string to the character before the found text:

echo "<hr>";

$religion = 'Hebrew';
$myString = <<<END_TEXT
"'I am a $religion,' he cries - and then - 'I fear the Lord the God of Heaven who hath made the sea and the dry land!'"
END_TEXT;
echo "<pre>$myString</pre>";