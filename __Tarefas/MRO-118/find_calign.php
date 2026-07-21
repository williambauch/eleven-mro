<?php
$lines = file('C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php');
// Find where $calign is used in getCellCode
$searchFrom = 0;
foreach ($lines as $i => $line) {
    if (preg_match('/function getCellCode\(/', $line)) {
        $searchFrom = $i;
        break;
    }
}
// Search for calign handling
for ($i = $searchFrom; $i < min($searchFrom + 150, count($lines)); $i++) {
    if (strpos($lines[$i], '$calign') !== false || strpos($lines[$i], '$valign') !== false) {
        echo ($i+1) . ': ' . $lines[$i];
    }
}
