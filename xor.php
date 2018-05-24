<?php

/*-----------------------------------------------------------------------------
# PHP FUNCTIONS
# -----------------------------------------------------------------------------
# XOR
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
//   00000111 => 5 DECIMAL