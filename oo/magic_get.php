<?php require '../_config.php'; 

class Car
{
    public function __get($name)
    {
        echo "O valor recuperado de \$nome �: ";
        return 'red';
    }
}

$obj = new Car;
$x = $obj->qualquer_coisa;
echo "A cor do carro $x";
?>