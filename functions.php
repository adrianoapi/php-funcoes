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
$myString = "Hello, world! The beautiful world.";
echo substr( $myString, 0, 5   ) . "<br/>";                                             // Displays 'Hello'
echo substr( $myString, 0, -21 ) . "<br/>";                                             // Displays 'Hello, world!'
echo substr( $myString, 7      ) . "<br/>";                                             // Displays 'world! The beautiful world.'
echo substr( $myString, -1     ) . "<br/>";                                             // Displays '.'
echo substr( $myString, -5, -1 ) . "<br/>";                                             // Displays 'orld'

echo "<hr>";

echo $myString[0 ] . "<br/>";                                                           // Displays 'H'
echo $myString[7 ] . "<br/>";                                                           // Displays 'w'
     $myString[33] = '?'    ;                                                           // Substring . => ?
echo $myString . "<br/>"    ;                                                           // Displays 'Hello, world! The beautiful world?'

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
echo strstr( $myString, "wor" ) . "<br/>";                                              // Displays 'world! The beautiful world?'
echo ( strstr( $myString, "xtz" ) ? "Yes" : "No" ) . "<br/>";                           // Displays 'No'
# Returns the portion from the start of the string to the character before the found text:
echo strstr( $myString, "wor", true ) . "<br/>";                                        // Displays 'Hello,'
echo strpos( $myString, "wor" ) . "<br/>";                                              // Displays '7'
echo var_dump(strpos( $myString, "xyz" )). "<br/>";                                     // Displays '' (false)
echo !strpos( $myString, "Hel" ) ? "Not found <br/>" : TRUE;                            // Displays 'Not found'
echo strpos( $myString, "o" ) . "<br />";                                               // Displays '4'
echo strrpos( $myString, "o" ) . "<br />";                                              // Displays '29'

$pos = 0;
while(($pos = strpos($myString, "l", $pos)) !== FALSE){
    
    echo "The letter 'l' was found at position: {$pos}<br/>";
    $pos++;
    
}

echo "<hr>";

# Replacing
echo str_replace("world", "country", $myString). "<br/>";                               // Hello, country! The beautiful country?
     str_replace("world", "country", $myString, $num). "<br/>"; 
echo "O texto foi subscrito $num <br/>";                                                // O texto foi subscrito 2
echo substr_replace($myString, " country", 5). "<br/>";                                 // Hello country
echo substr_replace($myString, "country", 7, 10). "<br/>";                              // Hello, country beautiful world?
echo substr_replace($myString, "country", 7, -7). "<br/>";                              // Hello, country world?

echo "<hr>";

$religion = 'Hebrew';
$myString = <<<END_TEXT
"'I am a $religion,' he cries - and then - 'I fear the Lord the God of Heaven who hath made the sea and the dry land!'"
END_TEXT;
echo "<pre>$myString</pre>";

echo "<hr>";

# printf — Mostra uma string formatada
/*---------------+----------------------------------------------------------------------------------------+
| Type Specifier | Meaning                                                                                |
+----------------+----------------------------------------------------------------------------------------+
| b              | Treat the argument as an integer and format it as a binary number.                     |
+----------------+----------------------------------------------------------------------------------------+
| c              | Treat the argument as an integer and format it as a character with that ASCII value.   |
+----------------+----------------------------------------------------------------------------------------+
| d              | Treat the argument as an integer and format it as a signed decimal number.             |
+----------------+----------------------------------------------------------------------------------------+
| e              | Format the argument in scientific notation (for example, 3.45e+2).                     |
+----------------+----------------------------------------------------------------------------------------+
| f              | Format the argument as a floating-point number, taking into account the current locale |
|                | settings (for example, many European locales use a comma for the decimal point,        |
|                | rather than a period).                                                                 |
+----------------+----------------------------------------------------------------------------------------+
| F              | Format the argument as a floating-point number, ignoring the locale settings.          |
+----------------+----------------------------------------------------------------------------------------+
| o              | Treat the argument as an integer and format it as an octal number.                     |
+----------------+----------------------------------------------------------------------------------------+
| s              | Format the argument as a string.                                                       |
+----------------+----------------------------------------------------------------------------------------+
| u              | Treat the argument as an integer and format it as an unsigned decimal number.          |
+----------------+----------------------------------------------------------------------------------------+
| x              | Treat the argument as an integer and format it as a lowercase hexadecimal number.      |
+----------------+----------------------------------------------------------------------------------------+
| X              | Treat the argument as an integer and format it as an uppercase hexadecimal number.     |
+----------------+----------------------------------------------------------------------------------------+
| %              | Display a literal percent (% ) symbol. This doesn't require an argument.               |
+----------------+---------------------------------------------------------------------------------------*/

printf("Retorna Pi arredondado: %d", M_PI);
//echo "<br>";
//printf("%d times %d is ".var_dump("%s"), 2, 3, 2*3);
//echo "<br>";
//printf("%d times %d is %F", 2, 3, 2*3);
echo "<br/><br/>";

$myNumber = 123.45;
printf( "Binary: %b<br/>",           $myNumber );
printf( "Character: %c<br/>",        $myNumber );
printf( "Decimal: %d<br/>",          $myNumber );
printf( "Scientific: %e<br/>",       $myNumber );
printf( "Float: %f<br/>",            $myNumber );
printf( "Octal: %o<br/>",            $myNumber );
printf( "String: %s<br/>",           $myNumber );
printf( "Hex (lower case): %x<br/>", $myNumber );
printf( "Hex (upper case): %X<br/>", $myNumber );

# Specifying Signs
printf( "%d<br/>",   123 );                                                             // Displays "123"
printf( "%d<br/>",  -123 );                                                             // Displays "-123"
printf( "%+d<br/>",  123 );                                                             // Displays "+123"
printf( "%+d<br/>", -123 );                                                             // Displays "-123"

# Padding the Output
printf( "%06d<br/>", 123    );                                                          // Displays "000123"
printf( "%06d<br/>", 4567   );                                                          // Displays "004567"
printf( "%06d<br/>", 123456 );                                                          // Displays "123456"

print "<pre>";
printf( "%15s\n",    "Hi"            );
printf( "%15s\n",    "Hello"         );
printf( "%15s\n",    "Hello, world!" );
printf( "%'#15s\n",  "Hi"            );   // Displays #############"Hi"
printf( "%'#-15s\n", "Hi"            );   // Displays "Hi#############"
print "</pre>";

# Specifying Number Precision
print "<pre>";
printf( "%f\n",    123.4567 );                                                          // Displays "123.456700" (default precision)
printf( "%.2f\n",  123.4567 );                                                          // Displays "123.46"
printf( "%.0f\n",  123.4567 );                                                          // Displays "123"
printf( "%.10f\n", 123.4567 );                                                          // Displays "123.4567000000"
print "</pre>";

echo "<pre>";
printf( "%.2f\n",    123.4567     );                                                    // Displays "123.46"
printf( "%012.2f\n", 123.4567     );                                                    // Displays "000000123.46"
printf( "%12.4f\n",  123.4567     );                                                    // Displays "    123.4567"
printf( "%.8s\n", "Hello, world!" );                                                    // Displays "Hello, w"
echo "</pre>";


$mailbox = "Inbox";
$totalMessages = 36;
$unreadMessages = 4;
printf( file_get_contents( "template.txt" ), $totalMessages, $mailbox, $unreadMessages );


/*---------------------------------------------------------------------+
| Trimming Strings                                                     |
+----------------------------------------------------------------------+
| trim()  | removes white space from the beginning and end of a string |
+---------+------------------------------------------------------------+
| ltrim() | removes white space only from the beginning of a string    |
+---------+------------------------------------------------------------+
| rtrim() | removes white space only from the end of a string          |
+---------+-----------------------------------------------------------*/

$myString = "   What a lot of space!     ";
echo "<pre>";
echo "|" . trim ( $myString ) . "|\n";                                                  // Displays "|What a lot of space!|"
echo "|" . ltrim( $myString ) . "|\n";                                                  // Displays "|What a lot of space!    |";
echo "|" . rtrim( $myString ) . "|\n";                                                  // Displays "|   What a lot of space!|";
echo "</pre>";