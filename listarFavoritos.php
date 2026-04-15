<?php
session_start();
include "conexaoBanco.php";

$idCliente = $_SESSION["usuario_id"];

$sql = "SELECT produto.* FROM favoritos
        JOIN produto ON produto.idProduto = favoritos.idProduto
        WHERE favoritos.idCliente = :cliente";

$stmt = $conn->prepare($sql);
$stmt->execute([":cliente" => $idCliente]);

while($p = $stmt->fetch()){
    echo $p["nome"] . " - R$" . $p["preco"] . "<br>";
}
?>