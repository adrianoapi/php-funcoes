<?php

function debug($data)
{
    echo "<pre>";
    if(is_array($data))
        print_r($data);
    echo $data;
    echo "<pre>";
}

?>