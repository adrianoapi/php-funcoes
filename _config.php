<?php

function debug($data)
{
    echo "<pre>";
    if(is_array($data)){
        print_r($data);
    }else{
        echo $data;
    }
    echo "<pre>";
}

?>