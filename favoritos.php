<?php
session_start();
require_once "conexaoBanco.php";

header("Content-Type: application/json; charset=UTF-8");

// verifica login
if(!isset($_SESSION["usuario_id"])){
    echo json_encode(["status"=>"erro","msg"=>"Faça login"]);
    exit;
}

// conexão
$conn = (new Database())->conectar();

$idCliente = $_SESSION["usuario_id"];
$idProduto = $_POST["idProduto"] ?? 0;

if(!$idProduto){
    echo json_encode(["status"=>"erro","msg"=>"Produto inválido"]);
    exit;
}

// verifica se já existe
$stmt = $conn->prepare("
    SELECT * FROM favoritos 
    WHERE idCliente = :cliente AND idProduto = :produto
");

$stmt->execute([
    ":cliente" => $idCliente,
    ":produto" => $idProduto
]);

// TOGGLE
if($stmt->rowCount() > 0){

    // remover
    $conn->prepare("
        DELETE FROM favoritos 
        WHERE idCliente = :cliente AND idProduto = :produto
    ")->execute([
        ":cliente" => $idCliente,
        ":produto" => $idProduto
    ]);

    echo json_encode([
        "status" => "removido",
        "msg" => "Removido dos favoritos"
    ]);

} else {

    // adicionar
    $conn->prepare("
        INSERT INTO favoritos (idCliente, idProduto) 
        VALUES (:cliente, :produto)
    ")->execute([
        ":cliente" => $idCliente,
        ":produto" => $idProduto
    ]);

    echo json_encode([
        "status" => "adicionado",
        "msg" => "Adicionado aos favoritos ❤️"
    ]);
}
?>