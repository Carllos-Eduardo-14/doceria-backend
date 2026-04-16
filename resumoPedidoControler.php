<?php
require_once "conexaoBanco.php";
require_once "resumoPedido.php";

header("Content-Type: application/json; charset=UTF-8");

$conn = (new Database())->conectar();
$resumo = new ResumoPedido($conn);

$idPedido = $_GET['idPedido'] ?? 0;

if(!$idPedido){
    echo json_encode([
        "status"=>"erro",
        "msg"=>"ID do pedido não informado"
    ]);
    exit;
}

echo json_encode(
    $resumo->buscar($idPedido)
);
?>