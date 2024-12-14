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
    if (isset($_POST['id']) && (
        isset($_POST['novo_nome']) || isset($_POST['novo_preco']) ||
        isset($_POST['nova_descricao']) || isset($_POST['nova_img']))) {

        $id = trim($_POST['id']);

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

                // atualiza o nome se foi enviado
                if (isset($_POST['novo_nome']) && !empty(trim($_POST['novo_nome']))) {

                    $novo_nome = trim($_POST['novo_nome']);

                    $consulta = $db_con->prepare(
                        "UPDATE produtos 
                         SET nome = ? 
                         WHERE id = ?");

                    if ($consulta->execute([$novo_nome, $id])) {
                        $resposta["sucesso"] = 1;
                        $resposta["cod_sucesso"] = "Nome atualizado.";
                    } else {
                        $resposta["sucesso"] = 0;
                        $resposta["erro"] = "Falha ao atualizar o nome.";
                        $resposta["cod_erro"] = 2;
                    }
                }

                // atualiza o preço se foi enviado
                if (isset($_POST['novo_preco']) && !empty(trim($_POST['novo_preco']))) {
                    
                    $novo_preco = trim($_POST['novo_preco']);

                    $consulta = $db_con->prepare(
                        "UPDATE produtos 
                        SET preco = ? 
                        WHERE id = ?");

                    if ($consulta->execute([$novo_preco, $id])) {
                        $resposta["sucesso"] = 1;
                        $resposta["mensagem_email"] = "Preco atualizado.";
                    } else {
                        $resposta["sucesso"] = 0;
                        $resposta["erro"] = "Falha ao atualizar o preco.";
                        $resposta["cod_erro"] = 2;
                    }
                }

                // atualiza descrição se foi enviada
                if (isset($_POST['nova_descricao']) && !empty(trim($_POST['nova_descricao']))) {
                    
                    $nova_descricao = trim($_POST['nova_descricao']);

                    $consulta = $db_con->prepare(
                        "UPDATE produtos 
                         SET descricao = ? 
                         WHERE id = ?");

                    if ($consulta->execute([$nova_descricao, $id])) {
                        $resposta["sucesso"] = 1;
                        $resposta["mensagem_email"] = "Descricao atualizada.";
                    } else {
                        $resposta["sucesso"] = 0;
                        $resposta["erro"] = "Falha ao atualizar descricao.";
                        $resposta["cod_erro"] = 2;
                    }
                }

                // atualiza imagem se foi enviada
                if (isset($_POST['nova_img']) && !empty(trim($_POST['nova_img']))) {
                    
                    $nova_img = trim($_POST['nova_img']);

                    $consulta = $db_con->prepare(
                        "UPDATE produtos 
                        SET img = ? 
                        WHERE id = ?");

                    if ($consulta->execute([$nova_img, $id])) {
                        $resposta["sucesso"] = 1;
                        $resposta["mensagem_email"] = "Imagem atualizada.";
                    } else {
                        $resposta["sucesso"] = 0;
                        $resposta["erro"] = "Falha ao atualizar imagem.";
                        $resposta["cod_erro"] = 2;
                    }
                }

            } else {
                $resposta["sucesso"] = 0;
                $resposta["erro"] = "Usuario nao é o criador do produto.";
                $resposta["cod_erro"] = 0;
            }

        } else {
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "Produto nao encontrado.";
            $resposta["cod_erro"] = 4;
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


