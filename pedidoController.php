<?php
session_start();
header('Content-Type: application/json');

include "conexaoBanco.php";
include "pedido.php";
include "taxaEntrega.php";

$conn = (new Database())->conectar();

// VERIFICA LOGIN
if(!isset($_SESSION["usuario_id"])){
    echo json_encode(["erro" => "Faça login"]);
    exit;
}

// VERIFICA CARRINHO
if(empty($_SESSION["carrinho"])){
    echo json_encode(["erro" => "Carrinho vazio"]);
    exit;
}

// DADOS
$dataEntrega = $_POST["dataEntrega"] ?? null;
$horaEntrega = $_POST["horaEntrega"] ?? null;
$obs = $_POST["obs"] ?? "";
$tipoEntrega = $_POST["tipoEntrega"] ?? null;
$endereco = $_POST["endereco"] ?? "";
$metodoPagamento = $_POST["metodoPagamento"] ?? null;
$distancia = $_POST["distancia"] ?? 0;

// VALIDAÇÃO DATA/HORA
if(!$dataEntrega || !$horaEntrega){
    echo json_encode(["erro" => "Preencha data e hora"]);
    exit;
}

// 💳 VALIDAÇÃO PAGAMENTO
if(empty($metodoPagamento)){
    echo json_encode(["erro" => "Escolha forma de pagamento"]);
    exit;
}

// VALIDAÇÃO ENTREGA (BACKEND FORTE)
if($tipoEntrega == "RETIRADA"){

    $endereco = null;
    $distancia = 0;
    $taxaEntrega = 0;

} elseif($tipoEntrega == "DELIVERY"){

    if(empty($endereco)){
        echo json_encode(["erro" => "Informe o endereço"]);
        exit;
    }

    if(!is_numeric($distancia) || $distancia <= 0){
        echo json_encode(["erro" => "Distância inválida"]);
        exit;
    }

    $taxaEntrega = calcularFrete((float)$distancia);

} else {
    echo json_encode(["erro" => "Tipo de entrega inválido"]);
    exit;
}

// CRIA PEDIDO
$pedido = new Pedido($conn);

$resultado = $pedido->criar(
    $_SESSION["usuario_id"],
    $_SESSION["carrinho"],
    $dataEntrega,
    $horaEntrega,
    $obs,
    $tipoEntrega,
    $endereco,
    $taxaEntrega,
    $metodoPagamento
);

// WHATSAPP AUTOMÁTICO
if(isset($resultado["status"])){

    $numero = "5581991480182";

    $mensagem = "Novo Pedido\n";
    $mensagem .= "Pedido Nº: ".$resultado["pedido"]."\n";
    $mensagem .= "Cliente: ".$_SESSION["usuario_nome"]."\n";
    $mensagem .= "Data: $dataEntrega às $horaEntrega\n";
    $mensagem .= "Tipo: $tipoEntrega\n";
    $mensagem .= "Pagamento: $metodoPagamento\n";

    if($tipoEntrega == "DELIVERY"){
        $mensagem .= "Distância: {$distancia} km\n";
        $mensagem .= "Endereço: $endereco\n";
        $mensagem .= "Frete: R$ $taxaEntrega\n";
    }

    $mensagem .= "Total: R$ ".$resultado["total"];

    $link = "https://wa.me/$numero?text=" . urlencode($mensagem);

    echo json_encode([
        "status" => "ok",
        "pedido" => $resultado["pedido"],
        "whatsapp" => $link
    ]);

}else{
    echo json_encode($resultado);
}
?>