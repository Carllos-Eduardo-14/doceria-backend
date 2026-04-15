<?php
include "conexaoBanco.php";

header("Content-Type: application/json");

$id = $_POST["id"] ?? null;
$status = $_POST["status"] ?? null;

// Validação
if(empty($id) || empty($status)){
    echo json_encode(["Erro" => "Dados inválidos"]);
    exit;
}

// Status Permitidos
$statusPermitidos = ["PENDENTE", "PREPARANDO", "PRONTO", "ENTREGUE", "CANCELADO"];

if(!in_array($status, $statusPermitidos)){
    echo json_encode(["Erro" => "Status Inválido"]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE pedido SET status = :status WHERE idpedido = :id");

    $stmt->execute([
    ":status" => $status,
    ":id" => $id
]);

    echo json_encode([
        "status" => "ok",
        "mensagem" => "Status Atualizado com Sucesso"
    ]);

} catch(Exception $e){
    echo json_encode([
    "status" => "ok",
    "mensagem" => "Erro ao Atualizar",
    "detalhe" => $e->getMessage()

    ]);
}
?>