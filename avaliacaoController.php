<?php
session_start();
require_once "conexaoBanco.php";
require_once "avaliacao.php";

header("Content-Type: application/json; charset=UTF-8");

$conn = (new Database())->conectar();
$avaliacao = new Avaliacao($conn);

// POST → enviar avaliação
if($_SERVER["REQUEST_METHOD"] === "POST"){

    if(!isset($_SESSION["usuario_id"])){
        echo json_encode(["status"=>"erro","msg"=>"Faça login"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $avaliacao->criar(
        $data['idProduto'],
        $_SESSION["usuario_id"],
        $data['nota'],
        $data['comentario']
    );

    echo json_encode([
        "status" => "sucesso",
        "msg" => "Agradecemos sua Avaliação ⭐"
    ]);
}

// GET → listar avaliações
if($_SERVER["REQUEST_METHOD"] === "GET"){

    $idProduto = $_GET['idProduto'] ?? 0;

    $lista = $avaliacao->listarPorProduto($idProduto);
    $media = $avaliacao->media($idProduto);

    echo json_encode([
        "media" => $media,
        "avaliacoes" => $lista
    ]);
}
?>