<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Funcoes nativas do PHP
------------------------------------------------------------------------------*/


# 5.1	Creating and Accessing Strings

#\n	A line feed character (ASCII 10)
#\n	A carriage return character (ASCII 13)
#\t	A horizontal tab character (ASCII 9)
#\v	A vertical tab character (ASCII 11)
#\f	A form feed character (ASCII 12)
#\\	A backslash (as opposed to the start of an escape sequence)
#\$	A $ symbol (as opposed to the start of a variable name)
#\"	A double quote (as opposed to the double quote marking the end of a string)


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
# Returns the portion from the start of the string to the character before the found text:
echo strstr( $myString, "wor", true ) . "<br/>";              // Displays 'Hello,'
echo strpos( $myString, "wor" ) . "<br/>";                    // Displays '7'
echo var_dump(strpos( $myString, "xyz" ));                    // Displays '' (false)
echo !strpos( $myString, "Hel" ) ? "Not found <br/>" : TRUE;  // Displays 'Not found'
echo strpos( $myString, "o" ) . "<br />";  // Displays '4'
echo strrpos( $myString, "o" ) . "<br />"; // Displays '8'
echo "<hr>";

$religion = 'Hebrew';
$myString = <<<END_TEXT
"'I am a $religion,' he cries - and then - 'I fear the Lord the God of Heaven who hath made the sea and the dry land!'"
END_TEXT;
echo "<pre>$myString</pre>";