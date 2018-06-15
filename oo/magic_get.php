<?php require '../_config.php'; 

class Car
{
    public function __get($name)
    {
        echo "O valor recuperado de \$nome é: ";
        return 'red';
    }
}

$obj = new Car;
$x = $obj->qualquer_coisa;
echo "A cor do carro $x";
?>