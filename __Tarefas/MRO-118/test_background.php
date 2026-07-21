<?php
require_once 'C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php';

$pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Imagem de fundo (igual ao ScriptCase)
$img = 'C:/xampp/htdocs/MRO_System/reports/blank_pdf_pack_jic/_lib/img/grp__NM__bg__NM__pack_page_1_jic_trim.png';
$pdf->Image($img, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);

// TESTE 1: Rect branco + Text
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(140, 100, 30, 10, 'F');
$pdf->Text(142, 106, 'TESTE RECT + TEXT');

// TESTE 2: MultiCell com fill
$pdf->SetFont('helvetica', '', 10.5);
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(25, 5, '10/11/2026', 0, 'L', true, 1, 140, 120, true);

// TESTE 3: MultiCell com borda e fill
$pdf->SetFillColor(255, 0, 0);
$pdf->MultiCell(25, 5, '10/11/2026', 1, 'L', true, 1, 140, 130, true);

// TESTE 4: Rect vermelho
$pdf->SetFillColor(255, 0, 0);
$pdf->Rect(140, 140, 30, 5, 'F');

// TESTE 5: Cell com fill
$pdf->SetFillColor(255, 255, 255);
$pdf->SetXY(140, 150);
$pdf->Cell(25, 5, '10/11/2026', 0, 1, 'L', true);

$pdf->Output('C:/xampp/htdocs/MRO_System/__Tarefas/MRO-118/test_background.pdf', 'F');
echo "OK\n";
