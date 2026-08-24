<?php
function converterHora($hora, $formato = 'decimal') {
    $partes = explode(':', $hora);
    $horas = isset($partes[0]) ? (int)$partes[0] : 0;
    $minutos = isset($partes[1]) ? (int)$partes[1] : 0;
    $segundos = isset($partes[2]) ? (int)$partes[2] : 0;

    switch ($formato) {
        case 'decimal':
            return round($horas + ($minutos / 60) + ($segundos / 3600), 4);
        case 'segundos':
            return ($horas * 3600) + ($minutos * 60) + $segundos;
        default:
            return false; // formato inválido
    }
}
?>