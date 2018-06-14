<?php require '../_config.php'; 

class Car
{
    
  const   HATCHBACK     = 1;
  const   STATION_WAGON = 2;
  const   SUV           = 3;
  public  $model;
  public  $color;
  public  $manufacturer;
  public  $type;
  private $speed        = 0;
  
  public function accelerate()
  {
      if($this->speed >= 100 ) return false;
      $this->speed += 10;
      return true;
  }
  
  public function brake()
  {
      if($this->speed <= 0) return false;
      $this->speed -= 10;
      return true;
  }
  
  public function getSpeed()
  {
      return $this->speed;
  }
  
  public static function calcConsumo($km, $litros)
  {
      return $km / $litros;
  }
  
}
 
$myCar = new Car;
$myCar->model        = "Dodge Caliber";
$myCar->color        = "red";
$myCar->manufacturer = "Chrysler";
$myCar->type         = Car::HATCHBACK;

debug( $myCar );

echo "The model car is: ";
switch ($myCar->type){
  case Car::HATCHBACK:
    echo "hatchback";
    break;
  case Car::STATION_WAGON:
    echo "station wagon";
    break;
  case Car::SUV:
    echo "SUV";
    break;
  default:
    echo "Não definido";
}

echo "<hr>";

echo "<p>Stepping on the gas...<br />";
 
while ( $myCar->accelerate() ) {
  echo "Current speed: " . $myCar->getSpeed() . " mph<br />";
}

echo "</p><p>Top speed! Slowing down...<br />";
 
while ( $myCar->brake() ) {
  echo "Current speed: " . $myCar->getSpeed() . " mph<br />";
}
 
echo "</p><p>Stopped!</p>";

echo "<p>Méida de 1L para cada " . Car::calcConsumo(10, 2) . "KM</p>"
        
?>