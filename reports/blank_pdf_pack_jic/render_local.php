<?php
// Script de teste LOCAL (PHP puro) para renderizar a pagina 1 do Pack JIC.
// Nao faz parte do app ScriptCase; serve apenas para testar o layout fora do SC.

// TCPDF instalado no ScriptCase (substituicao do sc_include_lib("tcpdf"))
require_once("C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php");

// Caminho absoluto da imagem de fundo (no CLI o relativo nao resolve)
// grade/linhas da pagina 1, SEM texto e SEM logo
$imgFundo = __DIR__ . '/modelo_jobcard_1_grid.png';

// 1. Inicializacao (mesma config do onExecute.scriptcase)
// O modelo Pack_JIC.pdf e tamanho LETTER (215,9 x 279,4 mm), nao A4.
// Usamos array de mm para garantir o formato exato neste TCPDF.
$pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// =========================================================
// IMAGEM DE FUNDO (modelo_jobcard_1.png) - pagina 1 do Pack_JIC.pdf
// Pagina LETTER = 215,9 x 279,4 mm. A imagem preenche a pagina
// toda SEM distorcao. Os dados estao estaticos nesta fase.
// =========================================================
$pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);

// LOGO: substitui o image1.jpeg original pelo logo do projeto.
// Caminho absoluto (no CLI o relativo ../_lib/img/ nao resolve direto).
$logo = __DIR__ . '/_lib/img/grp__NM__img__NM__logo_digex_mro.png';
$pdf->Image($logo, 2.73, 6.24, 36.29, 15.10, 'PNG');

// =========================================================
// CAMADA DE TEXTO SELECIONAVEL - posicoes exatas extraidas
// do Pack_JIC.pdf (pagina 1). helvetica = fonte do original.
// =========================================================
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(10.46, 20.95, utf8_decode('COM 199912-01/ANAC'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.48, 5.84, utf8_decode('A/C Work Order: '));
    $pdf->SetFont('helvetica', '', 5.63);
    $pdf->Text(45.13, 9.06, utf8_decode('NON ROUTINE JOB INSTRUCTION CARD CFEF'));
    $pdf->SetFont('helvetica', '', 5.63);
    $pdf->Text(152.99, 24.81, utf8_decode('N190036001'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(48.85, 23.92, utf8_decode('N000000000000001900360002901000000A4I001'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(130.26, 30.10, utf8_decode('ATA'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(129.75, 37.76, utf8_decode('23'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 30.10, utf8_decode('A/C Registration:'));
    $pdf->SetFont('helvetica', '', 5.63);
    $pdf->Text(3.39, 38.78, utf8_decode('PR-MLD'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(152.99, 11.85, utf8_decode('38.25'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.99, 19.69, utf8_decode('Document N°: '));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.39, 33.02, utf8_decode('(Registro da Aeronave)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(42.25, 30.10, utf8_decode('Company'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(42.25, 33.02, utf8_decode('(Empresa)'));
    $pdf->SetFont('helvetica', '', 4.68);
    $pdf->Text(3.39, 265.90, utf8_decode('TF-60-004'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.99, 213.61, utf8_decode('Date /'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(161.12, 213.99, utf8_decode('Data'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(177.63, 213.66, utf8_decode('/'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.74, 120.14, utf8_decode('Estimated Hours'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.74, 123.06, utf8_decode('(Horas Estimadas (Homens/Hora))'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.32, 134.24, utf8_decode('New NR Open?'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(153.08, 137.63, utf8_decode('(Nova NR Aberta? Sim/Não)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(157.86, 142.66, utf8_decode('Y (S):'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.82, 29.46, utf8_decode('Project:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.91, 31.71, utf8_decode('(Projeto)'));
    $pdf->SetFont('helvetica', '', 5.63);
    $pdf->Text(153.80, 38.48, utf8_decode('AVO03/25'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.65, 45.72, utf8_decode('A/C Type:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.27, 48.47, utf8_decode('(Tipo de Aeronave)'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(152.53, 52.20, utf8_decode('A320-214'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.16, 57.66, utf8_decode('A/C SN:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.78, 60.16, utf8_decode('(Serial Number)'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(153.04, 63.88, utf8_decode('3601'));
    $pdf->SetFont('helvetica', '', 4.68);
    $pdf->Text(152.99, 69.68, utf8_decode('Skill'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.99, 71.88, utf8_decode('(Especialidade)'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(152.99, 75.61, utf8_decode('1 A4 -- 2 h '));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.99, 96.14, utf8_decode('Area/Zone'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.99, 99.06, utf8_decode('(Area/Zona)'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(152.87, 102.79, utf8_decode('Wing LH'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.74, 108.58, utf8_decode('Originated By:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.74, 111.38, utf8_decode('(Originado por)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.61, 115.10, utf8_decode('PALOMA GODOY DA SILVA SANT'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(176.11, 5.84, utf8_decode('(N° da Ordem de Serviço)'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(172.80, 19.30, utf8_decode('(Documento N°)'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(129.88, 33.02, utf8_decode('(Ata)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(174.16, 134.28, utf8_decode('/ Mark as Applicable:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(178.48, 147.83, utf8_decode('(Indicar se aplicável):'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.59, 148.21, utf8_decode('List if applicable'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.08, 153.84, utf8_decode('N°___________________________'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.08, 160.95, utf8_decode('N°___________________________'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.82, 195.45, utf8_decode('Performed By:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(153.59, 198.08, utf8_decode('Executado por:'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.32, 218.31, utf8_decode('Planned RII Item:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.57, 220.94, utf8_decode('(Item IIO planejado)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(157.86, 228.26, utf8_decode('Y (S):'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(76.54, 30.10, utf8_decode('Origin Document N°'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(76.54, 32.51, utf8_decode('(Documento de Origem N°)'));
    $pdf->SetFont('helvetica', '', 5.63);
    $pdf->Text(75.90, 37.00, utf8_decode('190036'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(75.90, 40.98, utf8_decode('0672100000189'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 45.97, utf8_decode('Discrepancy'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 49.83, utf8_decode('LH WING - FLAPTRACK 2 - THE DISCHARGER-STATIC IS DAMAGED.'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 84.58, utf8_decode('Corrective Action:'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.89, 88.69, utf8_decode('REPLACE THE DISCHARGER-STATIC IAW AMM 23-61-41-000-001-A AND 23-61-41-400-001-A.'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 124.21, utf8_decode('Engineering Instruction:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(45.04, 123.82, utf8_decode('(Instrução de Engenharia)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 157.48, utf8_decode('In Accordance With: (Reference)'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.39, 160.15, utf8_decode('De acordo com: (Referencia)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.39, 170.18, utf8_decode('Action Taken:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.39, 172.85, utf8_decode('(Ação Executada)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.13, 202.78, utf8_decode('P/N Out'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.13, 205.61, utf8_decode('P/N Removido'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(78.19, 202.78, utf8_decode('S/N Out'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(78.19, 205.61, utf8_decode('S/N Removido'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.13, 210.14, utf8_decode('P/N In'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.13, 212.60, utf8_decode('P/N Instalado'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(78.44, 213.11, utf8_decode('S/N Instalado'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(25.99, 217.17, utf8_decode('APPROVAL / DEFERMENT Record ( Registro de Aprovação / Diferimento)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(15.16, 225.93, utf8_decode('APPROVAL '));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(15.16, 230.55, utf8_decode('(Aprovação)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(48.18, 225.93, utf8_decode('DEFERMENT'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(48.18, 230.55, utf8_decode('(Diferimento)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(78.40, 222.38, utf8_decode('Customer Full Name :'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(78.66, 225.26, utf8_decode('(Nome Completo do Cliente)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(78.40, 237.62, utf8_decode('Customer Signoff :'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(78.40, 240.24, utf8_decode('(Assinatura do Cliente)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(3.22, 237.36, utf8_decode('ID :'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(3.22, 239.99, utf8_decode('(Identificação)'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.32, 237.11, utf8_decode('RII Signoff if required:'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(152.32, 239.73, utf8_decode('Assinatura do IIO se necessário:'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(2.71, 252.60, utf8_decode('Reason for DEFERMENT'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(2.71, 255.23, utf8_decode('Motivo do Diferimento:'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(116.42, 252.73, utf8_decode('Date'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(116.16, 255.65, utf8_decode('Data'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(167.17, 266.23, utf8_decode('Rev. 07  25/OCT/2024'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(152.99, 255.78, utf8_decode('Date /'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(161.12, 256.16, utf8_decode('Data'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(130.13, 255.82, utf8_decode('/ /'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(168.74, 129.07, utf8_decode('Hh'));
    $pdf->SetFont('helvetica', '', 4.68);
    $pdf->Text(157.56, 127.76, utf8_decode('2'));
    $pdf->SetFont('helvetica', '', 4.68);
    $pdf->Text(188.04, 228.26, utf8_decode('X'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.59, 169.54, utf8_decode('Were Calibrating Tools Used?'));
    $pdf->SetFont('helvetica', '', 2.82);
    $pdf->Text(153.59, 172.93, utf8_decode('Ferramentas Calibráveis Foram Usadas?'));
    $pdf->SetFont('helvetica', '', 4.23);
    $pdf->Text(156.38, 178.56, utf8_decode('Y (S): N (N):'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.59, 185.59, utf8_decode('Note: If YES Record on the back (Stamp).'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(153.59, 189.65, utf8_decode('Nota: Se SIM Registre no verso (Carimbo).'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(94.06, 156.80, utf8_decode('Reference Revision:'));
    $pdf->SetFont('helvetica', '', 3.77);
    $pdf->Text(94.32, 159.81, utf8_decode('(Revisão da Referência:)'));
    $pdf->SetFont('helvetica', '', 4.68);
    $pdf->Text(164.68, 178.48, utf8_decode('X'));

// No CLI nao ha saida HTTP; salva o arquivo no disco.
$arquivoSaida = __DIR__ . '/pack_jic_pagina1_local.pdf';
$pdf->Output($arquivoSaida, 'F');

echo "PDF gerado com sucesso: " . $arquivoSaida . "\n";
