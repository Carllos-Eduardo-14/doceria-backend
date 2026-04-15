<?php
include "conexaoBanco.php";

header("Content-Type: application/json");

$busca = $_GET["busca"] ?? "";

// validação
if(empty($busca)){
    echo json_encode(["erro" => "Digite algo para buscar"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT * FROM produto 
    WHERE nome LIKE :busca
");

$like = "%$busca%";

$stmt->bindParam(":busca", $like);
$stmt->execute();

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// retorno
echo json_encode([
    "total" => count($resultados),
    "produtos" => $resultados
]);
?>