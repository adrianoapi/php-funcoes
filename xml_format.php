<?php

$data='<?xml version="1.0" encoding="utf-8"?> 
<books> 
<book id="1">PHP 5.5 in 42 Hours</book> 
<book id="2">Learning PHP 5.5 The Hard Way</book> 
</books>';
$xml = simplexml_load_string($data);
echo $xml->book[1];
$c = $xml->children(); echo $c[1];