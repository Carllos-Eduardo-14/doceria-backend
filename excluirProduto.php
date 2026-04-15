<?php
include 'conexaoBanco.php';

$id = $_GET['id'] ?? null;

// validação
if(!$id){
    echo "ID inválido";
    exit;
}

try {

    $stmt = $conn->prepare("DELETE FROM produto WHERE idProduto = :id");

    $stmt->execute([
        ":id" => $id
    ]);

    header("Location: admin.php");
    exit;

} catch(Exception $e){
    echo "Erro ao excluir produto";
}
?>