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

// array de resposta
$resposta = array();

// verifica se todos os campos necessários foram enviados ao servidor
if (isset($_POST['login']) && (
        isset($_POST['nome']) || isset($_POST['email']))
) {

    $login = trim($_POST['login']);

    $campos_para_atualizar = [];
    $valores = [];

    if (!empty($_POST['nome'])) {
        $campos_para_atualizar[] = "nome = ?";
        $valores[] = trim($_POST['nome']);
    }

    if (!empty($_POST['email'])) {
        $campos_para_atualizar[] = "email = ?";
        $valores[] = trim($_POST['email']);
    }

    if (count($campos_para_atualizar) > 0) {
        
    }

}


// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e 
// imprime na tela.
echo json_encode($resposta);


