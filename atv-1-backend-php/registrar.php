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
if (isset($_POST['novo_login']) && isset($_POST['nova_senha']) && 
    isset($_POST['nome']) && isset($_POST['email'])) {
 
    // o metodo trim elimina caracteres especiais/ocultos da string
	$novo_login = trim($_POST['novo_login']);
	$nova_senha = trim($_POST['nova_senha']);

    // variáveis novas requisitadas no exercício
    $nome = $_POST['nome'];
    $email = $_POST['email'];

	// código hash que é gerado a partir da senha
	$token = password_hash($nova_senha, PASSWORD_DEFAULT);

	// verifica se o usuário já existe.
	$consulta_usuario_existe = $db_con->prepare("SELECT login FROM usuarios WHERE login='$novo_login'");
	$consulta_usuario_existe->execute();
	if ($consulta_usuario_existe->rowCount() > 0) {
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "usuario ja cadastrado";
		$resposta["cod_erro"] = 1;
	}
	else {
		// se o usuário ainda não existe, inserimos ele no bd.
		$consulta = $db_con->prepare("INSERT INTO usuarios(login, token, nome, email) VALUES('$novo_login', '$token', '$nome', '$email')");
	 
		if ($consulta->execute()) {
			// se a consulta deu certo, indicamos sucesso na operação.
			$resposta["sucesso"] = 1;
		}
		else {
			// erro na consulta
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "erro BD: " . $consulta->error;
			$resposta["cod_erro"] = 2;
		}
	}
}
else {
	// não foram enviados todos os parâmetros para o servidor
    $resposta["sucesso"] = 0;
	$resposta["erro"] = "faltam parametros";
	$resposta["cod_erro"] = 3;
}

// A conexão com o bd sempre tem que ser fechada
$db_con = null;

// converte o array de resposta em uma string no formato JSON e imprime na tela.
echo json_encode($resposta);