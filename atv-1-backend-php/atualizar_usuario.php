<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

/*
 * 
 * Códigos de erro:
 * 0 : falha de autenticação
 * 1 : usuário já existe
 * 2 : falha banco de dados
 * 3 : faltam parametros
 * 4 : entrada não encontrada no BD
 * 
 */

require_once('conexao_db.php');
require_once('autenticacao.php');

// array de resposta
$resposta = array();

// verifica se todos os campos necessários foram enviados ao servidor
if (autenticar($db_con)) {
    
} else {
    // senha ou usuario nao confere
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "usuario ou senha não confere";
    $resposta["cod_erro"] = 0;
}



// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e 
// imprime na tela.
echo json_encode($resposta);


