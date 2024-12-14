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
    $login_aut = $GLOBALS['login'];

    $consulta = $db_con->prepare(
        "SELECT *
         FROM usuarios
         WHERE login = ?");
    
    $consulta->execute([$login_aut]);

    // pega os dados do usuario
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // verifica se um dos campos necessários foram enviados ao servidor
    if (isset($_POST['novo_nome']) || isset($_POST['novo_email'])) {

        // verifica se o user autenticado é o mesmo a ser atualizado
        if ($usuario['login'] === $login_aut) {

            // atualiza o nome se foi enviado
            if (isset($_POST['novo_nome']) && !empty(trim($_POST['novo_nome']))) {
                
                $novo_nome = trim($_POST['novo_nome']);

                $consulta = $db_con->prepare(
                    "UPDATE usuarios 
                    SET nome = ? 
                    WHERE login = ?");

                if ($consulta->execute([$novo_nome, $login_aut])) {
                    $resposta["sucesso"] = 1;
                    $resposta["cod_sucesso"] = "nome atualizado.";
                } else {
                    $resposta["sucesso"] = 0;
                    $resposta["erro"] = "falha ao atualizar o nome.";
                    $resposta["cod_erro"] = 2;
                }
            }

            // atualiza o email se foi enviado
            if (isset($_POST['novo_email']) && !empty(trim($_POST['novo_email']))) {
                
                $novo_email = trim($_POST['novo_email']);

                $consulta = $db_con->prepare(
                    "UPDATE usuarios 
                    SET email = ? 
                    WHERE login = ?");

                if ($consulta->execute([$novo_email, $login_aut])) {
                    $resposta["sucesso"] = 1;
                    $resposta["mensagem_email"] = "email atualizado.";
                } else {
                    $resposta["sucesso"] = 0;
                    $resposta["erro"] = "falha ao atualizar o email.";
                    $resposta["cod_erro"] = 2;
                }
            }

        } else {
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "falha de autenticação.";
            $resposta["cod_erro"] = 0;
        }

    } else {
        // não foram enviados nenhum parâmetro válido para o servidor
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "faltam parâmetros.";
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


