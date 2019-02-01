<style type="text/css">body{background-color: #000; color: #ccc;}</style>
<?php

require_once 'Crawler.php';

$links = [
    [ 
        "site" => "olharDigital", 
        "link" => "https://olhardigital.com.br/"
    ],
    [ 
        "site" => "sinteressante", 
        "link" => "https://super.abril.com.br/"
    ],
    [ 
        "site" => "canaltech", 
        "link" => "https://canaltech.com.br"
    ],
];

$depth = 6;
$username = 'YOURUSER';
$password = 'YOURPASS';
$crawler = new crawler($links[0]['link'], $depth);
$crawler->setHttpAuth($username, $password);
// Exclude path with the following structure to be processed 
$crawler->addFilterPath('customer/account/login/referer');
$crawler->run();
