<?php require '../_config.php'; 

class Car
{
    
  const  HATCHBACK     = 1;
  const  STATION_WAGON = 2;
  const  SUV           = 3;
  public $model;
  public $color;
  public $manufacturer;
  public $type;
  
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
}

?>