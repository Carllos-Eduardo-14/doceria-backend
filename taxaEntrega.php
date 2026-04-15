<?php

function calcularFrete($distancia) {

    if ($distancia <= 5) {
        return 5.00;
    } elseif ($distancia <= 10) {
        return 10.00;
    } elseif ($distancia <= 20) {
        return 20.00;
    } else {
        $extra = $distancia - 20;
        return 20 + ($extra * 2);
    }
}

// recebe distância
$distancia = $_GET['distancia'] ?? $_POST['distancia'] ?? null;

// validação
if($distancia === null || !is_numeric($distancia) || $distancia < 0){
    echo json_encode([
        "erro" => "Distância inválida"
    ]);
    exit;
}

$distancia = (float)$distancia;

// calcula frete
$frete = calcularFrete($distancia);

// resposta
header("Content-Type: application/json");

echo json_encode([
    "status" => "ok",
    "distancia" => $distancia,
    "frete" => number_format($frete, 2, '.', '')
]);
?>