
<?php
 
require '../_config.php'; 

class MyString
{
        
    public function __call($name, $arguments)
    {
        if(strpos($name, 'upper') !== false){
            return strtoupper($arguments[0]);
        }
    }
    
    public static function __callStatic($name, $arguments)
    {
        if(strpos($name, 'upper') !== false){
            return strtoupper($arguments[0]);
        }
    }
}

$string = new MyString();

echo $string->upper("minha string");
echo MyString::upper("minha string");
?>