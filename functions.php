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
?>