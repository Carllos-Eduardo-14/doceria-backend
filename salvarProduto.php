<?php
include "conexaoBanco.php";

$conn = (new Database())->conectar();

$nome = $_POST['nome'] ?? null;
$descricao = $_POST['descricao'] ?? null;
$preco = $_POST['preco'] ?? null;

// validação
if(!$nome || !$descricao || !$preco){
    echo "Preencha todos os campos";
    exit;
}

try {

    $stmt = $conn->prepare("
        INSERT INTO produto (nome, descricao, preco)
        VALUES (:nome, :descricao, :preco)
    ");

    $stmt->execute([
        ":nome" => $nome,
        ":descricao" => $descricao,
        ":preco" => $preco
    ]);

    header("Location: administrador.php");
    exit;

} catch(Exception $e){
    echo "Erro ao salvar produto";
}
?>