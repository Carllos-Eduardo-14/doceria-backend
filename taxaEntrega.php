<?php
function calcularFrete($distancia) {
    if ($distancia <= 5) return 5;
    if ($distancia <= 10) return 10;
    if ($distancia <= 20) return 20;

    return 20 + (($distancia - 20) * 2);
}