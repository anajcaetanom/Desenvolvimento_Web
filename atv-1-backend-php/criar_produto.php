<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */
 
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

// conexão com bd
require_once('conexao_db.php');

// autenticação
require_once('autenticacao.php');

// array de resposta
$resposta = array();

// verifica se o usuário conseguiu autenticar
if(autenticar($db_con)) {
	
	// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
	// A criacao de um produto precisa dos seguintes parametros:
	// nome - nome do produto
	// preco - preco do produto
	// descricao - descricao do produto
	// img - imagem do produto
	if (isset($_POST['nome']) && isset($_POST['preco']) && isset($_POST['descricao']) && isset($_FILES['img'])) {
		
		// Aqui sao obtidos os parametros
		$nome = $_POST['nome'];
		$preco = $_POST['preco'];
		$descricao = $_POST['descricao'];
		
		$filename = $_FILES['img']['tmp_name'];
		$client_id="ce5d3a656e2aa51";
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api.imgur.com/3/image',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('image'=> new CURLFILE($filename),'type' => 'file','title' => 'Simple upload','description' => 'This is a simple image upload in Imgur'),
		  CURLOPT_HTTPHEADER => array(
			'Authorization: Client-ID ' . $client_id
		  ),
		));

		$imgur_response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if($http_code == 200) {
			
			$imgur_response_json = json_decode($imgur_response, true);
			$img_url=$imgur_response_json['data']['link'];
			
			// A proxima linha insere um novo produto no BD.
			// A variavel consulta indica se a insercao foi feita corretamente ou nao.
			$consulta = $db_con->prepare("INSERT INTO produtos(nome, preco, descricao, img, usuarios_login) VALUES('$nome', '$preco', '$descricao', '$img_url', '$login')");
			if ($consulta->execute()) {
				// Se o produto foi inserido corretamente no servidor, o cliente 
				// recebe a chave "sucesso" com valor 1
				$resposta["sucesso"] = 1;
			} else {
				// Se o produto nao foi inserido corretamente no servidor, o cliente 
				// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
				// motivo da falha.
				$resposta["sucesso"] = 0;
				$resposta["erro"] = "Erro ao criar produto no BD: " . $consulta->error;
				$resposta["cod_erro"] = 2;
			}
		}
		else {
			// Se o envio da imagem para o IMGUR não funcionou, o cliente 
			// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
			// motivo da falha.
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "Erro ao enviar a imagem para o IMGUR. HTTP CODE: " . $http_code;
			$resposta["cod_erro"] = 2;
		}	
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
		// motivo da falha.
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "Campo requerido nao preenchido";
		$resposta["cod_erro"] = 3;
	}
}
else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "usuario ou senha não confere";
	$resposta["cod_erro"] = 0;
}

// Fecha a conexao com o BD
$db_con = null;

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>