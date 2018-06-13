<?php require '../_config.php'; 

class Car
{
  public $color;
  public $manufacturer;
  static public $numberSold = 123;
}
 
Car::$numberSold++;
debug( Car::$numberSold );                                                      // Displays "124"

echo "<hr>";

class Teste
{
    static public $today = "2018-06-13";
}
debug( Teste::$today );
?>