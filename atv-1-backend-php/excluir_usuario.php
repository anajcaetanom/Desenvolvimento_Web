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
    $login = $GLOBALS['id'];

    // prepara a consulta
    $consulta = $db_con->prepare("DELETE FROM usuarios WHERE login = ?");

    // exclui
    if ($consulta->execute([$login])) {
        $resposta["sucesso"] = 1;
        $resposta["mensagem"] = "Usuario excluido.";
    } else {
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Erro ao excluir usuario.";
        $resposta["cod_erro"] = 2;
    }

} else {
    // senha ou usuário nao confere
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Login ou senha nao conferem.";
    $resposta["cod_erro"] = 0;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e
// imprime na tela.
echo json_encode($resposta);
