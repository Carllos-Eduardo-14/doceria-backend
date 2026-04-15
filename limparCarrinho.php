<?php
session_start();

header("Content-Type: application/json");

if(isset($_SESSION["carrinho"])){

    unset($_SESSION["carrinho"]);

    echo json_encode([
        "status" => "ok",
        "mensagem" => "Carrinho Limpo com Sucesso"
    ]);

} else {
    echo json_encode([
        "erro" => "Carrinho já está vazio"
    ]);
}
?>