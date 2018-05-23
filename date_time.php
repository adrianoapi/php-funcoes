<?php

# http://br.phptherightway.com/

function debug($date)
{
    echo "<pre>";
         print_r($date);
    echo "</pre>";
}

$date = new DateTime();

debug($date);

echo "<hr>";

$raw   = '20. 11. 1987';
$start = DateTime::createFromFormat('d. m. Y', $raw);

echo "Start: " . $start->format('Y-d-m');

# cria uma cópia de $start e adiciona um mês e 6 dias
$end = clone $start;
$end->add(new DateInterval('P1M6D'));

echo "<hr>";

$diff = $end->diff($start);
echo "Diference " . $diff->format('%m month, %d days (total: %a days)');

if($start < $end){
    echo "<hr>";
    echo "Start is before end!\n";
}

?>