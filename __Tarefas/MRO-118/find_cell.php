<?php
$lines = file('C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php');
foreach ($lines as $i => $line) {
    if (strpos($line, 'function Cell(') !== false) {
        echo "Line " . ($i+1) . ": " . $line;
        // Also show param comments
        for ($j = $i+1; $j < min($i+15, count($lines)); $j++) {
            $trimmed = trim($lines[$j]);
            if (strpos($trimmed, '*') === 0 || strpos($trimmed, '//') === 0 || empty($trimmed)) {
                echo "Line " . ($j+1) . ": " . $lines[$j];
            } else {
                break;
            }
        }
        break;
    }
}
