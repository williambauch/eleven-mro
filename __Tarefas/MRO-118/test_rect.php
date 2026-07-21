<?php
require_once 'C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php';

$pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Retangulo preto
$pdf->SetFillColor(0, 0, 0);
$pdf->Rect(10, 10, 50, 20, 'F');

// Retangulo branco em cima
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(20, 12, 15, 10, 'F');

// Texto
$pdf->SetFont('helvetica', '', 10);
$pdf->Text(22, 18, 'TESTE');

// MultiCell com fill
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(18, 4, '10/11/2026', 0, 'L', true, 1, 100, 100, true);

$pdf->Output('C:/xampp/htdocs/MRO_System/__Tarefas/MRO-118/test_rect.pdf', 'F');
echo "PDF gerado\n";
