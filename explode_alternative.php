<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Emulando Funcoes nativas do PHP - explode
------------------------------------------------------------------------------*/

class Explode{
    
    private $delimiter;
    private $string;
    private $array;
    
    public function __construct() {}
    
    public function setDelimiter($char)
    {
        $this->delimiter = $char;
    }
    
    public function setString($string)
    {
        $this->string = $string;
    }
    
    /**
     * Aplica a logica explode
     * @return type
     */
    public function getResult()
    {
        $_str = NULL;
        $_arr = NULL;
        for( $i = 0; $i < $this->_strlen(); $i++)
        {
            if($this->string[$i] == $this->delimiter)
            {
                $_arr[] = $_str;
                $_str   = NULL;
            }else{
                $_str .= $this->string[$i];
            }
        }
        
        # Checa se sobrou algo na string
        if(isset($_str)){
            $_arr[] = $_str;
        }
        
        return $_arr;
    }
    
    /**
     * Emula a funcao strlen
     * @return int
     */
    private function _strlen()
    {
        $j = 0;
        while($this->_isset(@$this->string[$j])){
            $j++;
        }
        return $j;
    }
    
    /**
     * Emuala a funcao isset
     * @param type $char
     * @return type
     */
    private function _isset($char)
    {
        return $char != NULL ? true : '';
    }
    
}

/*-----------------------------------------------------------------------------
# Instanciando a classe
------------------------------------------------------------------------------*/

$myString = "Hello, world! The beautiful world.";

$obj = new Explode();
$obj->setDelimiter(" ");
$obj->setString($myString);
print_r($obj->getResult());
 
