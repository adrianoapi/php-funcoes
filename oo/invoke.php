<?php require '../_config.php'; 

function fun1(callable $func)
{
    $func();
    return " have fun";
}

class Sample
{
    public function __invoke()
    {
        echo "Enjoy";
    }
}

$obj = new Sample();
echo fun1($obj);
debug( $obj );
?>