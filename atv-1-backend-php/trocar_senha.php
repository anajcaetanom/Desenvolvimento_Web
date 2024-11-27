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

// verifica se o usuário está logado
if (autenticar($db_con)) {

    // pega o login autenticado
    $login = $GLOBALS['login'];

    // verifica se o parâmetro necessário foi enviado ao servidor
    if (isset($_POST['nova_senha']) && !empty(trim($_POST['nova_senha']))) {
        $nova_senha = trim($_POST['nova_senha']);

        $token = password_hash($nova_senha, PASSWORD_DEFAULT);

        $consulta = $db_con->prepare("UPDATE usuarios SET token = ? WHERE login = ?");

        if ($consulta->execute([$token, $login])) {
            $resposta["sucesso"] = 1;
            $resposta["cod_sucesso"] = "senha atualizada.";
        } else {
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "falha ao atualizar senha.";
            $resposta["cod_erro"] = 2;
        }


    } else {
        // não foi enviado o parâmetro para o servidor
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "faltam parametros.";
        $resposta["cod_erro"] = 3;
    }

} else {
    // senha ou usuário nao confere
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "login ou senha não conferem.";
    $resposta["cod_erro"] = 0;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e
// imprime na tela.
echo json_encode($resposta);


