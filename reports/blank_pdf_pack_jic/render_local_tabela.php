<?php
// Script de teste LOCAL (PHP puro) da proposta de TABELA.
// Nao faz parte do app ScriptCase; serve para testar o layout fora do SC.

require_once("C:/Program Files/NetMake/v9-php82/wwwroot/scriptcase/prod/third/tcpdf/tcpdf.php");

$pdf = new TCPDF('P', 'mm', array(215.9, 279.4), true, 'UTF-8', false);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0, 0);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// Caminho relativo funciona aqui pois reports/_lib/img/ existe.
$logo = '../_lib/img/grp__NM__img__NM__logo_digex_mro.png';

$html = '
<table border="1" cellpadding="2" cellspacing="0" style="border-collapse:collapse; width:100%; font-family:helvetica; color:#000; font-size:7pt;">
  <tr style="height:22mm;">
    <td style="width:24%; border:1px solid #000; vertical-align:middle;">
      <img src="' . $logo . '" style="width:38mm; height:13mm;" />
    </td>
    <td style="width:46%; border:1px solid #000; text-align:center; vertical-align:middle; font-size:11pt; font-weight:bold;">
      NON ROUTINE JOB INSTRUCTION CARD CFEF
    </td>
    <td style="width:30%; border:1px solid #000; vertical-align:middle; font-size:7pt;">
      A/C Work Order: <br>
      Document N&deg;: <br>
      <b>N190036001</b><br>
      ATA 23
    </td>
  </tr>
  <tr>
    <td style="width:24%; border:1px solid #000;">
      A/C Registration:<br><b>PR-MLD</b><br>
      <span style="font-size:6pt;">(Registro da Aeronave)</span>
    </td>
    <td style="width:30%; border:1px solid #000;">
      Company<br><b>COM 199912-01/ANAC</b><br>
      <span style="font-size:6pt;">(Empresa)</span>
    </td>
    <td style="width:46%; border:1px solid #000;">
      Origin Document N&deg;<br>
      <b>190036</b> / <b>0672100000189</b><br>
      <span style="font-size:6pt;">(Documento de Origem N&deg;)</span>
    </td>
  </tr>
  <tr>
    <td colspan="3" style="border:1px solid #000;">
      <b>Discrepancy</b><br>
      LH WING - FLAPTRACK 2 - THE DISCHARGER-STATIC IS DAMAGED.
    </td>
  </tr>
  <tr>
    <td colspan="3" style="border:1px solid #000;">
      <b>Corrective Action:</b><br>
      REPLACE THE DISCHARGER-STATIC IAW AMM 23-61-41-000-001-A AND 23-61-41-400-001-A.
    </td>
  </tr>
  <tr>
    <td colspan="3" style="border:1px solid #000;">
      <b>Engineering Instruction:</b> <span style="font-size:6pt;">(Instru&ccedil;&atilde;o de Engenharia)</span>
    </td>
  </tr>
  <tr>
    <td colspan="3" style="border:1px solid #000;">
      <b>In Accordance With: (Reference)</b> <span style="font-size:6pt;">De acordo com: (Refer&ecirc;ncia)</span>
    </td>
  </tr>
  <tr>
    <td style="width:34%; border:1px solid #000;">
      Estimated Hours<br><span style="font-size:6pt;">(Horas Estimadas (Homens/Hora))</span>
    </td>
    <td style="width:33%; border:1px solid #000;">
      New NR Open?<br><b>Y (S):</b>
    </td>
    <td style="width:33%; border:1px solid #000;">
      Project:<br><span style="font-size:6pt;">(Projeto)</span> <b>AV003/25</b>
    </td>
  </tr>
  <tr>
    <td style="width:34%; border:1px solid #000;">
      A/C Type:<br><span style="font-size:6pt;">(Tipo de Aeronave)</span> <b>A320-214</b>
    </td>
    <td style="width:33%; border:1px solid #000;">
      A/C SN:<br><span style="font-size:6pt;">(Serial Number)</span> <b>3601</b>
    </td>
    <td style="width:33%; border:1px solid #000;">
      Skill<br><span style="font-size:6pt;">(Habilidade)</span>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="border:1px solid #000;">
      Were Calibrating Tools Used?<br>
      <span style="font-size:6pt;">(Ferramentas Calibr&aacute;veis Foram Usadas?)</span><br>
      <b>Y (S): N (N):</b>
    </td>
    <td style="border:1px solid #000;">
      Reference Revision:<br>
      <span style="font-size:6pt;">(Revis&atilde;o da Refer&ecirc;ncia:)</span>
    </td>
  </tr>
  <tr>
    <td colspan="3" style="border:1px solid #000;">
      Planned RII Item:<br>
      <span style="font-size:6pt;">(Item RII planejado)</span> <b>Y (S):</b>
    </td>
  </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output(__DIR__ . '/pack_jic_pagina1_tabela.pdf', 'F');
echo "PDF gerado: " . __DIR__ . "/pack_jic_pagina1_tabela.pdf\n";
