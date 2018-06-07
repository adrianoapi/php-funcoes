<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Funcoes nativas do PHP
------------------------------------------------------------------------------*/

$myString = "Hello, world! The beautiful world.";

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
printf( "%d<br/>",   123 );                                                     // Displays "123"
printf( "%d<br/>",  -123 );                                                     // Displays "-123"
printf( "%+d<br/>",  123 );                                                     // Displays "+123"
printf( "%+d<br/>", -123 );                                                     // Displays "-123"

# Padding the Output
printf( "%06d<br/>", 123    );                                                  // Displays "000123"
printf( "%06d<br/>", 4567   );                                                  // Displays "004567"
printf( "%06d<br/>", 123456 );                                                  // Displays "123456"

print "<pre>";
printf( "%15s\n",    "Hi"            );
printf( "%15s\n",    "Hello"         );
printf( "%15s\n",    "Hello, world!" );
printf( "%'#15s\n",  "Hi"            );                                         // Displays #############"Hi"
printf( "%'#-15s\n", "Hi"            );                                         // Displays "Hi#############"
print "</pre>";

# Specifying Number Precision
print "<pre>";
printf( "%f\n",    123.4567 );                                                  // Displays "123.456700" (default precision)
printf( "%.2f\n",  123.4567 );                                                  // Displays "123.46"
printf( "%.0f\n",  123.4567 );                                                  // Displays "123"
printf( "%.10f\n", 123.4567 );                                                  // Displays "123.4567000000"
print "</pre>";

echo "<pre>";
printf( "%.2f\n",    123.4567     );                                            // Displays "123.46"
printf( "%012.2f\n", 123.4567     );                                            // Displays "000000123.46"
printf( "%12.4f\n",  123.4567     );                                            // Displays "    123.4567"
printf( "%.8s\n", "Hello, world!" );                                            // Displays "Hello, w"
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
echo "|" . trim ( $myString ) . "|\n";                                          // Displays "|What a lot of space!|"
echo "|" . ltrim( $myString ) . "|\n";                                          // Displays "|What a lot of space!    |";
echo "|" . rtrim( $myString ) . "|\n";                                          // Displays "|   What a lot of space!|";
echo "</pre>";