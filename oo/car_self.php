<?php require '../_config.php'; 

class Car
{
    public static function calcMpg($miles, $gallons)
    {
        return ($miles/$gallons);
    }
    
    public static function displayMpg($miles, $gallons)
    {
        echo "This car's MPG is: " . self::calcMpg($miles, $gallons);
    }
}

echo Car::displayMpg(150, 5);

?>