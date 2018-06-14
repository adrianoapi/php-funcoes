<?php require '../_config.php'; 

class MyClass
{
 
  const         MYCONST    = 123;
  public static $staticVar = 456;
 
  public function myMethod()
  {
    echo "MYCONST = " . MyClass::MYCONST . ", ";
    echo "\$staticVar = " . MyClass::$staticVar . "<br />";
  }
}
 
$obj = new MyClass;
$obj->myMethod();

?>