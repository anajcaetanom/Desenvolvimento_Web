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

    // verifica se o id do produto foi enviado como parâmetro
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // pega o produto
        $consulta = $db_con->prepare(
            "SELECT * 
             FROM produtos 
             WHERE id = ?");

        $consulta->execute([$id]);

        // verifica se produto existe
        if ($consulta->rowCount() > 0) {
            // pega os dados do produto
            $produto = $consulta->fetch(PDO::FETCH_ASSOC);

            // verifica se o usuario autenticado é o criador do produto
            if ($produto['usuarios_login'] === $login) {
                // prepara a consulta
                $consulta = $db_con->prepare(
                    "DELETE 
                     FROM produtos 
                     WHERE id = ?");

                $resposta["sucesso"] = 1;   

                if ($consulta->execute([$id])) {
                    $resposta["sucesso"] = 1;
                    $resposta["mensagem"] = "Produto excluido.";
                } else {
                    $resposta["sucesso"] = 0;
                    $resposta["erro"] = "Erro ao excluir produto.";
                    $resposta["cod_erro"] = 2;
                }

            } else {
                $resposta["sucesso"] = 0;
                $resposta["erro"] = "falha de autenticação.";
                $resposta["cod_erro"] = 0;
            }
            
        } else {
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "Produto nao encontrado.";
            $resposta["cod_erro"] = 4;
        }

    } else {
        // não foi enviado nenhum parâmetro válido para o servidor
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Faltam parâmetros.";
        $resposta["cod_erro"] = 3;
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