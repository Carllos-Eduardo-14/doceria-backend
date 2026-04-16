<?php
session_start();
require_once "conexaoBanco.php";
require_once "pedido.php";

header("Content-Type: application/json; charset=UTF-8");

// Verifica login
if(!isset($_SESSION["usuario_id"])){
    echo json_encode(["status"=>"erro","msg"=>"Faça login"]);
    exit;
}

// conexão
$conn = (new Database())->conectar();

// dados recebidos
$dataEntrega = $_POST["dataEntrega"] ?? "";
$horaEntrega = $_POST["horaEntrega"] ?? "";
$obs = $_POST["observacao"] ?? "";
$distancia = $_POST["distancia"] ?? 0;
$pagamento = $_POST["pagamento"] ?? "dinheiro";

// carrinho (simulado via sessão)
$carrinho = $_SESSION["carrinho"] ?? [];

if(empty($carrinho)){
    echo json_encode(["status"=>"erro","msg"=>"Carrinho vazio"]);
    exit;
}

// cálculo de frete
function calcularFrete($d){
    if ($d <= 5) return 5;
    if ($d <= 10) return 10;
    if ($d <= 20) return 20;
    return 20 + (($d - 20)*2);
}

$frete = calcularFrete($distancia);

// cria pedido
$pedido = new Pedido($conn);

$resultado = $pedido->criar(
    $_SESSION["usuario_id"],
    $carrinho,
    $dataEntrega,
    $horaEntrega,
    $obs,
    "DELIVERY",
    "Endereço do cliente",
    $frete
);

// se deu certo → gerar WhatsApp
if($resultado["status"] === "sucesso"){

    $mensagem = "🧾 *Novo Pedido*%0A";
    $mensagem .= "Pedido Nº: " . $resultado["idPedido"] . "%0A";
    $mensagem .= "Total: R$" . $resultado["total"] . "%0A";
    $mensagem .= "Pagamento: " . $pagamento . "%0A";
    $mensagem .= "Entrega: " . $dataEntrega . " às " . $horaEntrega . "%0A";
    $mensagem .= "Obs: " . $obs;

    // link WhatsApp 
    $numero = "5581991480182"; // 
    $linkWhats = "https://wa.me/$numero?text=$mensagem";

    // limpar carrinho
    unset($_SESSION["carrinho"]);

    echo json_encode([
        "status" => "sucesso",
        "pedido" => $resultado,
        "whatsapp" => $linkWhats
    ]);

} else {
    echo json_encode($resultado);
}
?>