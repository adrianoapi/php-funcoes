<?php

require '_config.php';

/*-----------------------------------------------------------------------------
# PHP ARRAY - SORT
# -----------------------------------------------------------------------------
# Working with Variable Functions
------------------------------------------------------------------------------*/

echo <<<END_COMMENT
/*
 * Passando função através de uma variável
 */
END_COMMENT;
$squareRoot = "sqrt";
echo "The square root of 9 is: " . $squareRoot( 9 ) . ".<br/>";
echo "All done!<br/>";

echo <<<END_COMMENT
/*
 * Passando função através de um array
 */
END_COMMENT;
$trigFunctions = array( "sin", "cos", "tan" );
$degrees = 30;
 
foreach ( $trigFunctions as $trigFunction )
{
  echo "$trigFunction($degrees) = " . $trigFunction( deg2rad( $degrees ) ) . "<br/>";
}

echo <<<END_COMMENT
/*
 * Creating Anonymous Functions
 */
END_COMMENT;
echo "<br/>";
$mode = "+";
$processNumbers = create_function( '$a, $b', "return \$a $mode \$b;" );
debug( $processNumbers( 2, 3 ) );

echo "<hr>";

$foo = create_function('$x', 'return $x*$x;');
debug ( $foo(10) );

echo "<hr>";

$foo = create_function("\$x", "return \$x*\$x;");
debug ( $foo(10) );

echo "<hr>";

echo <<<END_COMMENT
/*
 * Static variavel
 */
END_COMMENT;
function nextNumber()
{
  static $counter = 0;
  return ++$counter;
}
echo "<br/>" . "I've counted to: " . nextNumber();
echo "<br/>" . "I've counted to: " . nextNumber();
echo "<br/>" . "I've counted to: " . nextNumber();

echo "<hr>";

echo <<<END_COMMENT
/*
 * Sorting words in a block of text by length
 */
END_COMMENT;

$myText = <<<END_TEXT
But think not that this famous town has
only harpooneers, cannibals, and
bumpkins to show her visitors. Not at
all. Still New Bedford is a queer place.
Had it not been for us whalemen, that
tract of land would this day perhaps
have been in as howling condition as the
coast of Labrador.
END_TEXT;

echo "<h2>The text:</h2>";
echo "<div style=\"width: 30em;\">$myText</div>";

$myText = preg_replace( "/[\,\.]/", "", $myText );
$words  = array_unique( preg_split( "/[ \n\n\t]+/", $myText ) );
usort( $words, create_function( '$a, $b', 'return strlen($a) - strlen($b); ' ) );

echo "<h2>The sorted words:</h2>";
echo "<div style=\"width: 30em;\">";

foreach ( $words as $word ) {
  echo "$word ";
}
 
echo "</div>";

echo <<<END_COMMENT
/*
 * Working with References
 */
END_COMMENT;
$myVar = 123;
$myRef =& $myVar;
$myRef++;
echo "<br/>";
echo $myRef . "<br/>";                                                          // Displays "124"
echo $myVar . "<br/>";                                                          // Displays "124"

echo "<hr>";

echo <<<END_COMMENT
/*
 * counter example by using a reference
 */
END_COMMENT;
echo "<br/>";
function resetCounter( &$c )
{
  $c = 0;
}
$counter = 0;
$counter++;
$counter++;
$counter++;
echo "$counter<br/>";                                                           // Displays "3"
resetCounter( $counter );
echo "$counter<br/>";                                                           // Displays "0"

echo "<hr>";

echo <<<END_COMMENT
/*
 * Fazendo referência a uma função
 */
END_COMMENT;
echo "<br/>";
$myNumber = 5;
 
function &getMyNumber()
{
  global $myNumber;
  return $myNumber;
}
 
$numberRef =& getMyNumber();
$numberRef++;
echo "\$myNumber  = $myNumber<br/>";                                            // Displays "6"
echo "\$numberRef = $numberRef<br/>";                                           // Displays "6"

echo <<<END_COMMENT
/*
 * O truque aqui está no perador --, que não será executado;
 */
END_COMMENT;
echo "<br/>";
function fun($num1 , $num2)
     {
         echo '$num1 = '.$num1.'<br />$num2 ='.$num2.' ';
         if ($num1 < $num2)
             return;
         else
             fun ($num1-- , ++$num2);
     }
     fun(3 , 2);

echo <<<END_COMMENT
/*
 * Passa uma função para uma variável
 */
END_COMMENT;
function foo($string)
{
    echo $string;
}

$bar = "foo";
$bar("Hello World!");

?>