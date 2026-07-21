<?php
$lines = file('C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php');
// Find Cell function
$start = 0;
foreach ($lines as $i => $line) {
    if (preg_match('/function Cell\(/', $line)) {
        $start = $i;
        break;
    }
}
// Print lines around the function body
for ($i = $start; $i < min($start + 80, count($lines)); $i++) {
    echo ($i+1) . ': ' . $lines[$i];
}
