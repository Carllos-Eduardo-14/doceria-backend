<?php
header("Content-Type: application/json");

require_once "../config/Database.php";
require_once "../models/ResumoPedido.php";

$db = (new Database())->conectar();
$resumo = new ResumoPedido($db);

// validação
$idPedido = $_GET['idPedido'] ?? null;

if(!$idPedido || !is_numeric($idPedido)){
    echo json_encode([
        "erro" => "ID do pedido inválido"
    ]);
    exit;
}

try {

    $resultado = $resumo->buscar($idPedido);

    echo json_encode($resultado);

} catch(Exception $e){

    echo json_encode([
        "erro" => "Erro ao buscar resumo do pedido"
    ]);
}
?>