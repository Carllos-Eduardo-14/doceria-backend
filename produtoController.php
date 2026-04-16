<?php
require_once "conexaoBanco.php";
require_once "produto.php";

header("Content-Type: application/json; charset=UTF-8");

// Permitir apenas GET
if($_SERVER["REQUEST_METHOD"] !== "GET"){
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido"]);
    exit;
}

// Conexão
$db = (new Database())->conectar();
$produto = new Produto($db);

// Parâmetros
$termo = $_GET['busca'] ?? "";

// Busca ou lista
if(!empty($termo)){
    $resultado = $produto->buscar($termo);
} else {
    $resultado = $produto->listar();
}

// Retorno
echo json_encode([
    "status" => "sucesso",
    "quantidade" => count($resultado),
    "dados" => $resultado
]);
?>