<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# Bitwise
------------------------------------------------------------------------------*/

/*
 * Operador & ( Bitwise AND )
 * O operador & ( Bitwise AND ) compara dois valores utilizando suas representações
 * binárias, retornando um novo valor, para formar esse valor de retorno cada bit é comparado,
 * retornando 1( true ) [quando ambos os bits forem iguais] a 1( true ),
 * caso contrário retorna 0( false ).
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
 * O operador | ( Bitwise OR ) compara dois valores utilizando suas representações binárias, retornando 
 * um novo valor, para formar esse valor de retorno cada bit é comparado, [retornando 1( true ) se um dos 
 * bits comparados forem iguais a 1( true )], caso contrário retorna 0 ( false ).
 */

echo $a | $b;
//   00000101
// & 00000001
//-----------
//   00000111 => 5 DECIMAL

/*
 * Operador ^ ( Bitwise XOR )
 * O operador ^ ( Bitwise XOR ) compara dois valores utilizando suas representações binárias, 
 * retornando um novo valor, para formar esse valor de retorno cada bit é comparado, retornando 1( true ) 
 * quando os bits comparados forem diferentes, caso contrário retorna 0( false ).
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
 * O operador ~ ( Bitwise NOT ) diferente dos operadores anteriores, é um operador que afeta apenas um 
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

