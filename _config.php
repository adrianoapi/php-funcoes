<?php

header("Content-type: text/html; charset=iso-8859-1"); 

function debug($data, $stop = false)
{
    echo "<pre>";
    if(is_array($data) || is_object($data)){
        print_r($data);
    }else{
        echo $data;
    }
    echo "</pre>";
    if($stop)
        die();
}

?>