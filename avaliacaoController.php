<?php
require_once "../config/Database.php";
require_once "../models/Avaliacao.php";

header("Content-Type: application/json");

$db = (new Database())->conectar();
$avaliacao = new Avaliacao($db);

$metodo = $_SERVER['REQUEST_METHOD'];

if($metodo === "POST"){

    $data = json_decode(file_get_contents("php://input"), true);

    // VALIDAÇÃO
    if(
        empty($data['idProduto']) ||
        empty($data['idCliente']) ||
        empty($data['nota'])
    ){
        echo json_encode(["erro" => "Dados incompletos"]);
        exit;
    }

    // EXECUTA
    $resultado = $avaliacao->criar(
        $data['idProduto'],
        $data['idCliente'],
        $data['nota'],
        $data['comentario'] ?? ""
    );

    // RETORNA RESULTADO REAL
    echo json_encode($resultado);
    exit;
}

if($metodo === "GET"){

    if(!isset($_GET['idProduto'])){
        echo json_encode(["erro" => "Produto não informado"]);
        exit;
    }

    $idProduto = $_GET['idProduto'];

    $lista = $avaliacao->listarPorProduto($idProduto);
    $media = $avaliacao->media($idProduto);

    echo json_encode([
        "media" => $media,
        "avaliacoes" => $lista
    ]);
    exit;
}
?>