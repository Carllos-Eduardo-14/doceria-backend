<?php
include 'conexaoBanco.php';

$id = $_POST["id"] ?? null;
$nome = $_POST["nome"] ?? null;
$senha = !empty($_POST["senha"]) ? password_hash($_POST["senha"], PASSWORD_DEFAULT) : null;

if(!$id || !$nome){
    echo "Dados inválidos";
    exit;
}

if($senha){
    $sql = "UPDATE Cliente SET nome = :nome, senha = :senha WHERE idCliente = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ":nome" => $nome,
    ":senha" => $senha,
    ":id" => $id
]);
} else {
    $sql = "UPDATE Cliente SET nome = :nome WHERE idCliente = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":nome" => $nome,
        ":id" => $id
    ]);
}

echo "Atualizado com sucesso!"
?>