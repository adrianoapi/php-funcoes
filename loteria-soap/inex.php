<?php

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: security=true"));
curl_setopt($curl, CURLOPT_URL, 'http://www1.caixa.gov.br/loterias/loterias/megasena/megasena_pesquisa_new.asp');
curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
$return = curl_exec($curl);
curl_close($curl);
echo $return;
echo "<hr>";
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: security=true"));
curl_setopt($curl, CURLOPT_URL, 'http://www1.caixa.gov.br/loterias/_arquivos/loterias/D_megase.zip');
curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
$return = curl_exec($curl);
curl_close($curl);
file_put_contents('file.zip', $return);

$opts = array(
    'http' => array(
        'method'=>"GET",
        'header' => array(
            'Cookie: security=true'."\r\n",
            'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1",
        )
    ),
);
$context = stream_context_create($opts);
echo file_get_contents('http://www1.caixa.gov.br/loterias/loterias/megasena/megasena_pesquisa_new.asp', false, $context);

?>