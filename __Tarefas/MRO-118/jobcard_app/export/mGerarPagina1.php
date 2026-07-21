<?php

function mGerarPagina1XXX($pdf) {
    // =========================================================
    // Pagina 1 — gerada do editor em 21/07/2026, 17:18:08
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_1_jic_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);

    // Fonte: 10px normal → 7.5pt
    $pdf->SetFont('helvetica', '', 7.5);
    $pdf->MultiCell(142.61, 0, 'LH WING - FLAPTRACK 2 - THE DISCHARGER-STATIC IS DAMAGED.', 0, 'L', false, 1, 1.59, 47.63, true); // p034 - Discrepancy - LH WING - FLAPTRACK 2 - THE DISCHARGER-STATIC IS DAMAGED. (multilinha)
    $pdf->Text(1.59, 263, 'Texto Motivo do Diferimento'); // p125 - Motivo do Diferimento - Texto Motivo do Diferimento
    $pdf->MultiCell(142.08, 0, 'Engineering Instruction:', 0, 'L', false, 1, 1.59, 128.59, true); // p055 - Engineering Instruction: (Instrução de Engenharia) - Engineering Instruction: (multilinha)
    $pdf->MultiCell(142.08, 0, 'REPLACE THE DISCHARGER-STATIC IAW AMM 23-61-41-000-001-A AND 23-61-41-400-001-A.', 0, 'L', false, 1, 1.59, 87.05, true); // p043 - Corrective Action: (Ação corretiva) - REPLACE THE DISCHARGER-STATIC IAW AMM 23-61-41-000-001-A AND 23-61-41-400-001-A. (multilinha)
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(166.69, 154.25, '01 List if applicable'); // new-1 - Indicar se aplicável 01 - 01 List if applicable
    $pdf->Text(166.69, 161.66, '02 List if applicable'); // new-2 - Indicar se aplicável 02 - 02 List if applicable
    $pdf->Text(162.72, 114.04, 'PALOMA GODOY DA SILVA SANT'); // p053 - Originado por - PALOMA GODOY DA SILVA SANT
    $pdf->Text(162.72, 102.39, 'Wing LH'); // p050 - Area/Zona - Wing LH
    $pdf->MultiCell(45.51, 0, '1 A4 -- 2 h', 0, 'L', false, 1, 162.72, 73.82, true); // p042 - Especialidade - 1 A4 -- 2 h (multilinha)
    $pdf->Text(162.72, 61.65, '3601'); // p039 - Serial Number - 3601
    $pdf->Text(162.72, 50.01, 'A320-214'); // p036 - Tipo de Aeronave - A320-214
    $pdf->MultiCell(147.64, 0, 'Texto de Ação Executada', 0, 'L', false, 1, 1.59, 179.39, true); // p080 - Ação Executada - Texto de Ação Executada (multilinha)
    $pdf->Text(18.52, 214.58, 'P/N Instalado'); // p097 - - - P/N Instalado
    $pdf->Text(98.16, 214.58, 'S/N Instalado'); // p098 - - - S/N Instalado
    $pdf->Text(18.52, 206.9, 'P/N Removido'); // p093 - - - P/N Removido
    $pdf->Text(98.16, 206.9, 'S/N Removido'); // p094 - - - S/N Removido
    $pdf->Text(82.02, 248.71, 'Aqui Assinatura do Cliente'); // p121 - Assinatura do Cliente - Aqui Assinatura do Cliente
    $pdf->Text(82.02, 234.95, '(Nome Completo do Cliente)'); // p111 - - - (Nome Completo do Cliente)
    $pdf->Text(162.72, 249.5, 'Assinatura do IIO se necessário:'); // p122 - Assinatura do IIO se necessário - Assinatura do IIO se necessário:
    $pdf->Text(162.72, 204.79, 'Nome do Executor'); // p090 - Executado por - Nome do Executor
    $pdf->write1DBarcode('N000000000000001900360002901000000A4I001', 'C39', 45.77, 15.35, 100, 5, null, array('text' => true), 'N'); // p010 - NON ROUTINE Código de Barras - N000000000000001900360002901000000A4I001 (barcode)
    // Fonte: 11px bold → 8.25pt
    $pdf->SetFont('helvetica', 'B', 8.25);
    $pdf->Text(179.12, 143.14, 'X'); // p086 - Nova NR Aberta (S) - X
    $pdf->Text(203.46, 180.18, 'X'); // p114 - Ferramentas Calibráveis Foram Usadas (N) - X
    $pdf->Text(42.07, 229.13, 'X'); // new-7 - Diferimento - X
    $pdf->Text(6.35, 229.13, 'X'); // new-6 - Aprovação - X
    $pdf->Text(201.35, 231.51, 'X'); // new-5 - Item IIO planejado (N) - X
    $pdf->Text(179.12, 231.51, 'X'); // new-4 - Item IIO planejado (S) - X
    $pdf->Text(201.61, 143.14, 'X'); // p082 - Nova NR Aberta (N) - X
    $pdf->Text(177.54, 180.18, 'X'); // p113 - Ferramentas Calibráveis Foram Usadas (S) - X
    // Fonte: 12px normal → 9pt
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Text(42.33, 35.45, 'A320'); // p025 - Empresa - A320
    // Fonte: 12px bold → 9pt
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Text(3.44, 248.71, 'ID (Identificação)'); // p120 - ID Identificação - ID (Identificação)
    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(138.64, 35.45, '23'); // p026 - ata - 23
    $pdf->Text(162.98, 7.14, '38.25'); // p005 - N° da Ordem de Serviço - 38.25
    // Fonte: 13px bold → 9.75pt
    $pdf->SetFont('helvetica', 'B', 9.75);
    $pdf->Text(79.38, 33.07, '190036'); // p023 - Documento de Origem N° 1 - 190036
    // Fonte: 14px normal → 10.5pt
    $pdf->SetFont('helvetica', '', 10.5);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(183.09, 258.33);
    $pdf->Cell(18, 3.7, '12/11/2026', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p099 - Data Assinatura do IIO - 12/11/2026 (date)
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(132.03, 258.33);
    $pdf->Cell(18, 3.7, '10/11/2026', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // p102 - Data Motivo do Diferimento - 10/11/2026 (date)
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetXY(183.09, 214.4);
    $pdf->Cell(18, 3.7, '10/11/2026', 0, 0, 'L', true, '', 0, false, 'T', 'T'); // new-3 - Data - 10/11/2026 (date)
    // Fonte: 14px bold → 10.5pt
    $pdf->SetFont('helvetica', 'B', 10.5);
    $pdf->Text(79.38, 37.31, '0672100000189'); // p029 - Documento de Origem N° 2 - 0672100000189
    $pdf->Text(1.59, 35.45, 'PR-MLD'); // p024 - Registro da Aeronave - PR-MLD
    $pdf->Text(162.98, 33.6, 'AVO03/25'); // p028 - projeto - AVO03/25
    // Fonte: 15px normal → 11.25pt
    $pdf->SetFont('helvetica', '', 11.25);
    $pdf->Text(167.22, 126.47, '2'); // p060 - Horas Estimadas (Homens/Hora - 2
    // Fonte: 15px bold → 11.25pt
    $pdf->SetFont('helvetica', 'B', 11.25);
    $pdf->Text(162.98, 20.37, 'N190036001'); // p011 - Documento N° - N190036001
    // Fonte: 16px bold → 12pt
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Text(45.77, 4.5, 'NON ROUTINE JOB INSTRUCTION CARD CFEF'); // p004 - - - NON ROUTINE JOB INSTRUCTION CARD CFEF
    // Fonte: 9px normal → 6.75pt
    $pdf->SetFont('helvetica', '', 6.75);
    $pdf->Text(99.48, 166.42, '(Revisão da Referência:)'); // p077 - Revisão da Referência - (Revisão da Referência:)
    $pdf->Text(1.59, 166.42, 'De acordo com: (Referencia)'); // p075 - De acordo com: (Referencia) - De acordo com: (Referencia)
}
