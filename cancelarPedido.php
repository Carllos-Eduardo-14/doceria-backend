<?php
session_start();
include "conexaoBanco.php";

header("Content-Type: application/json");

$idPedido = $_POST["id"] ?? null;
$idCliente = $_SESSION["usuario_id"] ?? null;

// validação
if(!$idPedido || !$idCliente){
    echo json_encode(["erro" => "Dados inválidos"]);
    exit;
}

try {

    $stmt = $conn->prepare("
        UPDATE pedido
        SET status = 'CANCELADO'
        WHERE idPedido = :id
        AND idCliente = :cliente
        AND status = 'PENDENTE'
    ");

    $stmt->execute([
        ":id" => $idPedido,
        ":cliente" => $idCliente
    ]);

    if($stmt->rowCount() > 0){
        echo json_encode([
            "status" => "ok",
            "mensagem" => "Pedido cancelado com sucesso"
        ]);
    } else {
        echo json_encode([
            "erro" => "Não foi possível cancelar (pedido já processado ou não pertence a você)"
        ]);
    }

} catch(Exception $e){
    echo json_encode([
        "erro" => "Erro ao cancelar pedido"
    ]);
}
?>