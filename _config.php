<?php

function debug($data, $stop = false)
{
    echo "<pre>";
    if(is_array($data)){
        print_r($data);
    }else{
        echo $data;
    }
    echo "<pre>";
    if($stop)
        die();
}

?>