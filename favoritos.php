<?php
session_start();
include "conexaoBanco.php";

header("Content-Type: application/json");

$conn = (new Database())->conectar();

$idCliente = $_SESSION["usuario_id"] ?? null;
$idProduto = $_POST["idProduto"] ?? null;

// validação
if(!$idCliente){
    echo json_encode(["erro" => "Faça login"]);
    exit;
}

if(!$idProduto){
    echo json_encode(["erro" => "Produto inválido"]);
    exit;
}

try {

    // verifica se já existe
    $check = $conn->prepare("
        SELECT * FROM favoritos 
        WHERE idCliente = :cliente AND idProduto = :produto
    ");
    $check->execute([
        ":cliente" => $idCliente,
        ":produto" => $idProduto
    ]);

    if($check->rowCount() > 0){

        // remove
        $del = $conn->prepare("
            DELETE FROM favoritos 
            WHERE idCliente = :cliente AND idProduto = :produto
        ");

        $del->execute([
            ":cliente" => $idCliente,
            ":produto" => $idProduto
        ]);

        echo json_encode([
            "status" => "removido",
            "mensagem" => "Removido dos favoritos"
        ]);

    } else {

        // adiciona
        $add = $conn->prepare("
            INSERT INTO favoritos (idCliente, idProduto)
            VALUES (:cliente, :produto)
        ");

        $add->execute([
            ":cliente" => $idCliente,
            ":produto" => $idProduto
        ]);

        echo json_encode([
            "status" => "adicionado",
            "mensagem" => "Adicionado aos favoritos"
        ]);
    }

} catch(Exception $e){

    echo json_encode([
        "erro" => "Erro ao processar favorito"
    ]);
}
?>