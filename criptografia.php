<?php
$password = 'Teste';
   
// Criptografa com MD5
$password = md5($password);
   
// Palavra única no sistema para combinar a senha do usuário
$keySecurity = '4]e`7FIyq>a5w!jx-$M}OXDT4V%^*t';
   
// Criptografando com crypt ( http://php.net/manual/pt_BR/function.crypt.php )
$hash = crypt($password, $keySecurity);
   
// Criptografia de 128 bits
$new = hash('sha512', $hash);
echo $new."<br/>";
echo strlen($new);


?>