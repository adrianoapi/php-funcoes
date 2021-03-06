<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Bitwise
# Reference: http://www.diogomatheus.com.br/blog/php/operadores-bitwise-bit-a-bit/
------------------------------------------------------------------------------*/

/*
 * Operador & ( Bitwise AND )
 * O operador & ( Bitwise AND ) compara dois valores utilizando suas representa��es
 * bin�rias, retornando um novo valor, para formar esse valor de retorno cada bit � comparado,
 * retornando 1( true ) [quando ambos os bits forem iguais] a 1( true ),
 * caso contr�rio retorna 0( false ).
 */

$a = 5;
$b = 1;
echo $a & $b;
//   00000101
// & 00000001
//-----------
//   00000001 => 1 DECIMAL

echo "<hr>";

/*
 * Operador | ( Bitwise OR )
 * O operador | ( Bitwise OR ) compara dois valores utilizando suas representa��es bin�rias, retornando 
 * um novo valor, para formar esse valor de retorno cada bit � comparado, [retornando 1( true ) se um dos 
 * bits comparados forem iguais a 1( true )], caso contr�rio retorna 0 ( false ).
 */

echo $a | $b;
//   00000101
// & 00000001
//-----------
//   00000101 => 5 DECIMAL

/*
 * Operador ^ ( Bitwise XOR )
 * O operador ^ ( Bitwise XOR ) compara dois valores utilizando suas representa��es bin�rias, 
 * retornando um novo valor, para formar esse valor de retorno cada bit � comparado, retornando 1( true ) 
 * quando os bits comparados forem diferentes, caso contr�rio retorna 0( false ).
 */

echo "<hr>";

echo $a ^ $b;
//   00000101
// & 00000001
//-----------
//   00000100 => 4 DECIMAL

echo "<hr>";

/*
 * Operador ~ ( Bitwise NOT )
 * O operador ~ ( Bitwise NOT ) diferente dos operadores anteriores, � um operador que afeta apenas um 
 * operando, incrementando(++) e invertendo seu sinal, de positivo para negativo e vice versa.
 */

$a = -2;
// 11111111111111111111111111111110
$a = ~$a;
// 00000000000000000000000000000001 => 1 DECIMAL
echo $a . "<br/>";
$b = 2;
// 00000000000000000000000000000010
$b = ~$b;
// 11111111111111111111111111111101 => -3 DECIMAL
echo $b;

echo "<hr>";

/*
 * Operador >> ( Bitwise right shift )
 * O operador >> ( deslocamento de bits para a direita ) olhando pela base decimal parece estranho, mas 
 * se olharmos pela representa��o bin�ria do valor iremos identificar facilmente que os bits deslizam para 
 * direita, sendo o operando da direita respons�vel pelo n�mero de vezes que os bits ser�o deslizados, 
 * cada passo equivale a dividir por 2, ou seja, $a >> 3, � o mesmo que dividir $a por 2 tr�s vezes.
 */

$a = 49;
// 00110001
echo $a >> 3;
// 00000110 => 6 DECIMAL

echo "<hr>";

/*
 * Operador << ( Bitwise left shift )
 * O operador << ( deslocamento de bits para a esquerda ) segue a mesma linha de racioc�nio do 
 * operador >>, mas ao inv�s de deslizar os bits para a direita, desliza os bits para a esquerda, cada 
 * passo equivale a multiplicar por 2, ou seja, $a << 3, � o mesmo que multiplicar $a por 2 tr�s vezes.
 */

$a = 49;
// 00110001
echo $a << 3;
// 110001000 => 392 DECIMAL

echo "<hr>";

// Vai retornar o qu�?
//echo chr((ord('a') + ord('A'))/2);

class Magic { 
    protected $v = array( 
        "a" => 1, 
        "b" => 2, 
        "c" => 3 
    ); 
    public 

    function __get($v) 
    { 
        return $this->v[$v]; 
    } 
} 

$m = new Magic(); 
$m->d[] = 4; 
echo $m->d[0];