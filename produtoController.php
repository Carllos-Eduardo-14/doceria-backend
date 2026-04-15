<?php
require_once "../config/Database.php";
require_once "../models/Produto.php";

header("Content-Type: application/json; charset=UTF-8");

// Permitir apenas GET
if($_SERVER["REQUEST_METHOD"] !== "GET"){
    http_response_code(405);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Método não permitido"
    ]);
    exit;
}

try {
    $db = (new Database())->conectar();
    $produto = new Produto($db);

    // filtros
    $termo = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

    // busca
    if(!empty($termo) || !empty($categoria)){
        $resultado = $produto->buscar($termo, $categoria);
    } else {
        $resultado = $produto->listar();
    }

    // verifica erro vindo do model
    if(isset($resultado["erro"])){
        echo json_encode($resultado);
        exit;
    }

    $produtos = $resultado["produtos"];

    echo json_encode([
        "status" => "ok",
        "quantidade" => count($produtos),
        "dados" => $produtos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro interno no servidor"
    ]);
}
?>