<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Creating a Wrapper Class with __call()</title>
  </head>
  <body>
 
<?php
 
require '../_config.php'; 

class CleverString {

    private        $_theString        = "";
    private static $_allowedFunctions = array( "strlen", "strtoupper", "strpos", "var_dump" );

    public function setString( $stringVal ) {
        $this->_theString = $stringVal;
    }

    public function getString() {
        return $this->_theString;
    }

    public function __call( $methodName, $arguments ) {
        if ( in_array( $methodName, self::$_allowedFunctions ) ) {
            array_unshift( $arguments, $this->_theString );
            return call_user_func_array( $methodName, $arguments );
        } else {
            die ( "<p>Method 'CleverString::$methodName' doesn't exist</p>" );
        }
    }
}

$obj = new CleverString;
$obj->setString( "adriano" );
echo "<p>The string is: " . $obj->getString() . "</p>";
echo "<p>The length of the string is: " . $obj->strlen() . "</p>";
echo "<p>The string in uppercase letters is: " . $obj->strtoupper() ."</p>";
echo "<p>The letter 'o' occurs at position: " . $obj->strpos( "o" ) ."</p>";
$obj->var_dump();
debug($obj);
$obj->TESTE();
?>
 
  </body>
</html>