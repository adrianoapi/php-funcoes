<?php
$text = <<<EOT
The big bang bonged under the bung. 
EOT;
preg_match_all('@b.n?g@', $text, $matches); 

echo $text;


?>