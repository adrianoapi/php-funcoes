
<?php
 
require '../_config.php'; 

class Cliente
{
    private $nome;
    
    /**
     * Método construtor
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
     * Método desconstrutor
     */
//    public function __destruct()
//    {
//        echo "Variável \$cliente sendo destruída.";
//    }
}

$obj = new Cliente('Adriano');
echo $obj;
?>