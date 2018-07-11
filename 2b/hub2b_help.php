<?php

/**
 * Funcao que gera o envio ah API
 * @param type $url
 * @param type $service
 * @param type $options
 * @return type
 */
function postApi($url = NULL, $service = NULL, $options = NULL, $json){
    
    # Variaveis de configuracao
    $sandbox = TRUE;
    $id      = ($sandbox)         ? "90080"                : "";
    $auth    = ($sandbox)         ? "YXRlbmRlc21hcnQ6cHdk" : "";
    $url     = !is_null($url)     ? $url                   : "http://eb-api-sandbox.plataformahub.com.br/RestServiceImpl.svc";
    $service = !is_null($service) ? $service               : die("Erro: Informe um service!");
    
    # Caso tenha informacao, monta parametros para a url
    # exemplo: ?status=1&offset=0...
    if(is_array($options)){
        if(count($options) > 0){
            $string = "?";
            foreach($options as $key => $value):
                $string .= "$key=$value&";
            endforeach;
            $options = substr($string, 0, -1);
        }else{
            $options = NULL;
        }
    }
    
    # Monta o cabecalho
    $sku_options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/json\r\n"
            . "charset: UTF-8\r\n"
            . "User-Agent: PHP\r\n"
            . "Auth: {$auth}\r\n"
            . "Connection: Connection: Keep-Alive\r\n"
            . "Content-Length: " . strlen($json) . "\r\n",
            'content' => $json
        )
    );
    
    # Executa a chamada e realiza o retorno convertendo para objeto
    return json_decode(file_get_contents("{$url}/{$service}/{$id}{$options}", false, stream_context_create($sku_options)));
 
}

?>