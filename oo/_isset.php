<?php require '../_config.php'; 

class Car
{
    public function __isset($string)
    {
        return substr($string, 0, 4) == "test" ? true : false;
    }
}

$obj = new Car;
debug(isset($obj->banana    ));
debug(isset($obj->testbanana));


?>