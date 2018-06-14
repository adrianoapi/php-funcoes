<?php require '../_config.php'; 

class Sample
{
    public static function have_fun()
    {
        echo "Work!!!";
    }
}

Sample::{'have_fun'}();

?>