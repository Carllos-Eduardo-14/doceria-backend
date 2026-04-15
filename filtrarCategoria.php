<?php
include "conexaoBanco.php";

if(isset($_GET["categoria"])){

    $categoria = $_GET["categoria"];

    // Preparar a query
    $stmt = $conn->prepare9("SELECT * FROM produto WHERE categoria = :cat");

    // Vincular parâmetro
    $stmt->bindParam(":cat", $categoria);

    // Executar
    $stmt->execute();

    // Verificar se encontrou resultado
    if($stmt->rowCount() > 0){
        while($p = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo $p["nome"] . "<br>";
        }
    } else {
        echo "Nenhum produto encontrado." ;
    }
} else {
    echo "Categoria não encontrada.";
}
?> 