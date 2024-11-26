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

    // verifica se um dos campos necessários foram enviados ao servidor
    if (isset($_POST['novo_nome']) || isset($_POST['novo_email'])) {

        // atualiza o nome se foi enviado
        if (isset($_POST['novo_nome']) && !empty(trim($_POST['novo_nome']))) {
            $novo_nome = trim($_POST['novo_nome']);

            $consulta = $db_con->prepare("UPDATE usuarios SET nome = ? WHERE login = ?");

            if ($consulta->execute([$novo_nome, $login])) {
                $resposta["sucesso"] = 1;
                $resposta["cod_sucesso"] = "Nome atualizado.";
            } else {
                $resposta["sucesso"] = 0;
                $resposta["erro"] = "Falha ao atualizar o nome.";
                $resposta["cod_erro"] = 2;
            }
        }

        // atualiza o email se foi enviado
        if (isset($_POST['novo_email']) && !empty(trim($_POST['novo_email']))) {
            $novo_email = trim($_POST['novo_email']);

            $consulta = $db_con->prepare("UPDATE usuarios SET email = ? WHERE login = ?");

            if ($consulta->execute([$novo_email, $login])) {
                $resposta["sucesso"] = 1;
                $resposta["mensagem_email"] = "Email atualizado.";
            } else {
                $resposta["sucesso"] = 0;
                $resposta["erro"] = "Falha ao atualizar o email.";
                $resposta["cod_erro"] = 2;
            }
        }

    } else {
        // não foram enviados nenhum parâmetro válido para o servidor
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Faltam parâmetros.";
        $resposta["cod_erro"] = 3;
    }
    
} else {
    // senha ou usuário nao confere
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Login ou senha não conferem.";
    $resposta["cod_erro"] = 0;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e 
// imprime na tela.
echo json_encode($resposta);


