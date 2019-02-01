<style type="text/css">body{background-color: #000; color: #ccc;}</style>
<?php

require_once 'Crawler.php';


#$startURL = 'https://olhardigital.com.br/';
$startURL = 'https://super.abril.com.br/';
$depth = 6;
$username = 'YOURUSER';
$password = 'YOURPASS';
$crawler = new crawler($startURL, $depth);
$crawler->setHttpAuth($username, $password);
// Exclude path with the following structure to be processed 
$crawler->addFilterPath('customer/account/login/referer');
$crawler->run();