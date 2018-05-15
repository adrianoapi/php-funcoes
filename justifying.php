<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
      <title>Justifying Lines of Text</title>
      <link rel="stylesheet" type="text/css" href="common.css" />
    </head>
    <body>
      <h1>Justifying Lines of Text</h1>
   
        <?php
        $myText = <<<END_TEXT
Depois de afirmar que o acordo era "o acordo de n�o prolifera��o mais forte j� 
negociado" num discurso de 5 de agosto de 2015, Obama afirmou: "A proibi��o do 
Ir� ter uma arma nuclear � permanente." Mas em 2025, o Ir� j� poderia usar
centr�fugas avan�adas para enriquecer ur�nio, o que provocou
cr�ticas at� mesmo de colegas democratas. O l�der da minoria do
Senado, Chuck Schumer (D-N.Y.), declarou em 6 de agosto de 2015 
que "o acordo permitiria que o Ir�, ap�s 10 a 15 anos, 
estivesse �s v�speras de ser um Estado nuclear e com 
a b�n��o da comunidade mundial. O Ir� teria a luz 
verde para estar t�o perto, ou ainda 
mais perto, de possuir uma 
arma nuclear do 
que est� hoje.
END_TEXT;
        
  #echo "<pre>".utf8_encode($myText)."</pre>";

  $myText = str_replace( "\n", " ", $myText );
  $myText = str_replace( "  ", " ", $myText );
  $point  = 0;
  $_text  = NULL;
  $cols   = 40;
  $j      = 0;
  
  for($i = 0; $i < strlen($myText); $i++){
      
      if($j >= $cols && $myText[$i] == " "){
          $_text .= $myText[$i]."\n";
          $j = 0;
      }else{
          $_text .= $myText[$i];
          $j++;
      }
  }
  
/*-----------------------------------------------------------------------------  
# Depois de afirmar que o acordo era "o acordo 
# de n�o prolifera��o mais forte j� negociado" 
# num discurso de 5 de agosto de 2015, Obama 
# afirmou: "A proibi��o do Ir� ter uma arma 
# nuclear � permanente." Mas em 2025, o Ir� 
# j� poderia usar centr�fugas avan�adas para 
# enriquecer ur�nio, o que provocou cr�ticas 
# at� mesmo de colegas democratas. O l�der 
# da minoria do Senado, Chuck Schumer (D-N.Y.), 
# declarou em 6 de agosto de 2015 que "o acordo 
# permitiria que o Ir�, ap�s 10 a 15 anos, 
# estivesse �s v�speras de ser um Estado nuclear 
# e com a b�n��o da comunidade mundial. O Ir� 
# teria a luz verde para estar t�o perto, ou 
# ainda mais perto, de possuir uma arma nuclear 
# do que est� hoje.
------------------------------------------------------------------------------*/  
  echo "<pre>".utf8_encode($_text)."</pre>";
  
  ?>
  
    </body>
  </html>