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

// verifica se o campo necessário foi enviado via GET
if (isset($_GET['id'])) {

    $id = trim($_GET['id']);

    $consulta = $db_con->prepare("SELECT * FROM produtos WHERE id = ?");
    $consulta->execute([$id]);
    // verifica se produto foi encontrado
    if ($consulta->rowCount() > 0) {
        $produto = $consulta->fetch(PDO::FETCH_ASSOC);
        $resposta["sucesso"] = 1;
        $resposta["nome"] = $produto['nome'];
        $resposta["preco"] = $produto['preco'];
        $resposta["descricao"] = $produto['descricao'];
        $resposta["criado_por"] = $produto['usuarios_login'];
        $resposta["criado_em"] = $produto['criado_em'];
        $resposta["img"] = $produto['img'];
    } else {
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "produto nao encontrado.";
        $resposta["cod_erro"] = 4;
    }

} else {
    // não foram enviados parâmetros para o servidor
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "faltam parametros";
    $resposta["cod_erro"] = 3;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e imprime na tela.
echo json_encode($resposta);
