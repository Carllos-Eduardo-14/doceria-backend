<?php
include "conexaoBanco.php";

$idPedido = $_GET["id"] ?? null;

if(!$idPedido){
    echo "Pedido inválido";
    exit;
}

// 🔹 Buscar dados do pedido
$stmtPedido = $conn->prepare("
    SELECT * FROM pedido WHERE idPedido = :id
");
$stmtPedido->execute([":id" => $idPedido]);

$p = $stmtPedido->fetch(PDO::FETCH_ASSOC);

if(!$p){
    echo "Pedido não encontrado";
    exit;
}

// 🔹 Buscar itens do pedido
$stmtItens = $conn->prepare("
    SELECT i.*, pr.nome 
    FROM itemPedido i
    JOIN produto pr ON i.idProduto = pr.idProduto
    WHERE i.idPedido = :id
");

$stmtItens->execute([":id" => $idPedido]);
$itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Exibir pedido
echo "<h3>Pedido #" . $p["idPedido"] . "</h3>";
echo "Status: " . $p["status"] . "<br>";
echo "Retirada: " . $p["dataEntrega"] . " às " . $p["horaEntrega"] . "<br>";
echo "Total: R$ " . number_format($p["valorTotal"], 2, ',', '.') . "<br>";
echo "Obs: " . $p["observacao"] . "<br><hr>";

// 🔹 Exibir itens
echo "<h4>Itens:</h4>";

foreach($itens as $item){
    echo $item["nome"] . " - ";
    echo "Qtd: " . $item["quantidade"] . " - ";
    echo "Subtotal: R$ " . number_format($item["subtotal"], 2, ',', '.') . "<br>";
}
?>