<?php require '../_config.php'; 

class Car
{
    public $color;
}

class Garage
{
    public function paint($car, $color)
    {
        $car->color = $color;
    }
}

$car = new Car;
$gar = new Garage;
$car->color = "red";
$gar->paint($car, "purple");

debug ( $car->color );

?>