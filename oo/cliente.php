
<?php
 
require '../_config.php'; 

class Cliente
{
    private $nome;
    
    /**
     * M�todo construtor
     * @param type $nome
     */
    public function __construct($nome)
    {
        $this->nome = $nome;
    }
    
    public function __toString()
    {
        return $this->nome;
    }
    
    /**
     * M�todo desconstrutor
     */
//    public function __destruct()
//    {
//        echo "Vari�vel \$cliente sendo destru�da.";
//    }
}

$obj = new Cliente('Adriano');
echo $obj;
?>