<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP STRING - SEARCH
# -----------------------------------------------------------------------------
# Exemplos de pesquina e manipulação em string
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
$myString = "Hello, world! The beautiful world.";
echo substr( $myString, 0, 5   ) . "<br/>";                                     // Displays 'Hello'
echo substr( $myString, 0, -21 ) . "<br/>";                                     // Displays 'Hello, world!'
echo substr( $myString, 7      ) . "<br/>";                                     // Displays 'world! The beautiful world.'
echo substr( $myString, -1     ) . "<br/>";                                     // Displays '.'
echo substr( $myString, -5, -1 ) . "<br/>";                                     // Displays 'orld'

echo "<hr>";

echo $myString[0 ] . "<br/>";                                                   // Displays 'H'
echo $myString[7 ] . "<br/>";                                                   // Displays 'w'
     $myString[33] = '?'    ;                                                   // Substring . => ?
echo $myString . "<br/>"    ;                                                   // Displays 'Hello, world! The beautiful world?'

echo "<hr>";


# 5.2  Searching Strings
/*--------------+-----------------------------+
| Function      | Case-Insensitive Equivalent |
+---------------+-----------------------------+
| strstr()      | stristr()                   |
+---------------+-----------------------------+
| strpos()      | stripos()                   |
+---------------+-----------------------------+
| strrpos()     | strripos()                  |
+---------------+-----------------------------+
| str_replace() | str_ireplace()              |
+---------------+----------------------------*/
echo strstr  ( $myString, "wor" ) . "<br/>";                                    // Displays 'world! The beautiful world?'
echo ( strstr( $myString, "xtz" ) ? "Yes" : "No" ) . "<br/>";                   // Displays 'No'
# Returns the portion from the start of the string to the character before the found text:
echo strstr  ( $myString, "wor", true ) . "<br/>";                              // Displays 'Hello,'
echo strpos  ( $myString, "wor" ) . "<br/>";                                    // Displays '7'
echo var_dump(strpos( $myString, "xyz" )). "<br/>";                             // Displays '' (false)
echo !strpos ( $myString, "Hel" ) ? "Not found <br/>" : TRUE;                   // Displays 'Not found'
echo strpos  ( $myString, "o" ) . "<br />";                                     // Displays '4'
echo strrpos ( $myString, "o" ) . "<br />";                                     // Displays '29'

$pos = 0;
while(($pos = strpos($myString, "l", $pos)) !== FALSE){
    
    echo "The letter 'l' was found at position: {$pos}<br/>";
    $pos++;
    
}

echo "<hr>";

$text = "The quick brown fox jumped over the lazy dog.";
$newtext = wordwrap($text, 20, "<br />\n");
echo $newtext;

echo "<hr>";

echo str_word_count($text);                                                     // Displays '9'

echo "<hr>";

echo strtr("Hilla Warld","ia","eo");                                            // Hello World

echo "<hr>";

# Search a string for "world", and return all characters from this position to the end of the string:
echo strrchr("Hello world!","world");                                           // world!

echo "<hr>";

$str = "abcdabcdabdc"; 
echo substr_count($str,"abc")."<br />";           
$myString = "I say, nay, nay, and thrice nay!";
echo strlen($myString)."<br>";
echo substr_count( $myString, "nay", 9    ) . "<br />";                         // Displays '2'
echo substr_count( $myString, "nay", 9, 6 ) . "<br />";                         // Displays '1'
$str = "This is nice";
echo strlen($str)."<br>";                                                       // Using strlen() to return the string length
echo substr_count($str,"is"    ) . "<br/>";                                     // The number of times "is" occurs in the string
echo substr_count($str,"is",2  ) . "<br/>";                                     // The string is now reduced to "is is nice"
echo substr_count($str,"is",3  ) . "<br/>";                                     // The string is now reduced to "s is nice"
echo substr_count($str,"is",3,3) . "<br/>";                                     // The string is now reduced to "s i"

echo "<hr>";

# Replacing
echo str_replace("world", "country", $myString). "<br/>";                       // Hello, country! The beautiful country?
     str_replace("world", "country", $myString, $num). "<br/>"; 
echo "O texto foi subscrito $num <br/>";                                        // O texto foi subscrito 2
echo substr_replace($myString, " country", 5). "<br/>";                         // Hello country
echo substr_replace($myString, "country", 7, 10). "<br/>";                      // Hello, country beautiful world?
echo substr_replace($myString, "country", 7, -7). "<br/>";                      // Hello, country world?

echo "<hr>";

$religion = 'Hebrew';
$myString = <<<END_TEXT
"'I am a $religion,' he cries - and then - 'I fear the Lord the God of Heaven who hath made the sea and the dry land!'"
END_TEXT;
echo "<pre>$myString</pre>";

echo "<hr>";

?>