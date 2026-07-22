<?php

function mGerarPagina5Calibrated($pdf) {
    // =========================================================
    // Pagina 5 — gerada do editor em 22/07/2026, 11:37:41
    // Dimensoes LETTER: 215,9 x 279,4 mm
    // =========================================================

    $pdf->AddPage();

    // --- Imagem de fundo ---
    $imgFundo = '../_lib/img/grp__NM__bg__NM__pack_page_5_calibrated_trim.png';
    $pdf->Image($imgFundo, 0, 0, 215.9, 279.4, 'PNG', '', '', false, 300, '', false, false, 0);
    $pdf->setPageMark();

    // Fonte: 13px normal → 9.75pt
    $pdf->SetFont('helvetica', '', 9.75);
    $pdf->Text(66.41, 7.14, 'A320-214'); // p019 - - - A320-214
    $pdf->Text(123.56, 7.14, 'PR-MLD'); // p021 - - - PR-MLD
    $pdf->Text(173.83, 7.14, 'N190036001'); // p023 - - - N190036001
    // Fonte: 11px normal → 8.25pt
    $pdf->SetFont('helvetica', '', 8.25);
    $pdf->Text(166.69, 54.77, 'Equipment pass (02)'); // new-9 - Equipment pass 02 - Equipment pass (02)
    $pdf->Text(166.69, 69.59, 'SN equipamento (02)'); // new-5 - SN equipamento 02 - SN equipamento (02)
    $pdf->Text(55.83, 54.77, 'Equipamento apto para tarefa'); // p192 - Equipment pass - Equipamento apto para tarefa
    $pdf->Text(55.83, 69.59, 'SN equipamento'); // p244 - SN equipamento - SN equipamento
    $pdf->Text(1.32, 69.59, 'PN equipamento'); // p198 - PN equipamento - PN equipamento
    $pdf->Text(1.32, 54.77, 'Proxima Calibração'); // p168 - Proxima Calibração - Proxima Calibração
    $pdf->Text(1.32, 40.48, 'Measure Taken'); // p095 - Measure Taken (Unit) - Measure Taken
    $pdf->Text(55.83, 40.48, 'Incerteza-erro-desv'); // p107 - Incerteza-erro-desvio - Incerteza-erro-desv
    $pdf->Text(20.11, 25.93, 'Task sub'); // p034 - Task sub item - Task sub
    $pdf->Text(166.69, 25.93, 'Inspector Stamp (02)'); // new-6 - Inspector Stamp 02 - Inspector Stamp (02)
    $pdf->Text(130.97, 25.93, 'Task sub item (02)'); // new-1 - Task sub item 02 - Task sub item (02)
    $pdf->Text(112.18, 40.48, 'Measure Taken (Unit)  (02)'); // new-8 - Measure Taken (Unit) 02 - Measure Taken (Unit)  (02)
    $pdf->Text(166.69, 40.48, 'Incerteza-erro-desvio (02)'); // new-2 - Incerteza-erro-desvio 02 - Incerteza-erro-desvio (02)
    $pdf->Text(112.18, 54.77, 'Proxima Calibração (02)'); // new-3 - Proxima Calibração 02 - Proxima Calibração (02)
    $pdf->Text(55.83, 25.93, 'Inspector Stamp'); // p054 - Inspector Stamp - Inspector Stamp
    $pdf->Text(1.32, 134.67, 'PN equipamento'); // new-12 - PN equipamento (03) - PN equipamento
    $pdf->Text(1.32, 119.86, 'Proxima Calibração'); // new-13 - Proxima Calibração (03) - Proxima Calibração
    $pdf->Text(1.32, 105.57, 'Measure Taken'); // new-14 - Measure Taken (Unit) (03) - Measure Taken
    $pdf->Text(55.83, 105.57, 'Incerteza-erro-desv'); // new-15 - Incerteza-erro-desvio (03) - Incerteza-erro-desv
    $pdf->Text(20.11, 91.02, 'Task sub'); // new-16 - Task sub item (03) - Task sub
    $pdf->Text(55.83, 91.02, 'Inspector Stamp'); // new-18 - Inspector Stamp (03) - Inspector Stamp
    $pdf->Text(55.83, 134.67, 'SN equipamento'); // new-11 - SN equipamento (03) - SN equipamento
    $pdf->Text(55.83, 119.86, 'Equipamento apto para tarefa'); // new-10 - Equipment pass (03) - Equipamento apto para tarefa
    $pdf->Text(166.69, 134.67, 'SN equipamento'); // new-20 - SN equipamento (04) - SN equipamento
    $pdf->Text(112.18, 119.86, 'Proxima Calibração'); // new-22 - Proxima Calibração (04) - Proxima Calibração
    $pdf->Text(112.18, 105.57, 'Measure Taken'); // new-23 - Measure Taken (Unit) (04) - Measure Taken
    $pdf->Text(166.69, 105.57, 'Incerteza-erro-desv'); // new-24 - Incerteza-erro-desvio (04) - Incerteza-erro-desv
    $pdf->Text(130.97, 91.02, 'Task sub'); // new-25 - Task sub item (04) - Task sub
    $pdf->Text(166.69, 91.02, 'Inspector Stamp'); // new-27 - Inspector Stamp (04) - Inspector Stamp
    $pdf->Text(166.69, 119.86, 'Equipamento apto para tarefa'); // new-19 - Equipment pass (04) - Equipamento apto para tarefa
    $pdf->Text(112.18, 69.59, 'PN equipamento (02)'); // new-4 - PN equipamento 02 - PN equipamento (02)
    $pdf->Text(112.18, 134.67, 'PN equipamento'); // new-21 - PN equipamento (04) - PN equipamento
    $pdf->Text(55.83, 200.29, 'SN equipamento'); // new-29 - SN equipamento (05) - SN equipamento
    $pdf->Text(1.32, 200.29, 'PN equipamento'); // new-30 - PN equipamento (05) - PN equipamento
    $pdf->Text(1.32, 185.47, 'Proxima Calibração'); // new-31 - Proxima Calibração (05) - Proxima Calibração
    $pdf->Text(55.83, 171.19, 'Incerteza-erro-desv'); // new-33 - Incerteza-erro-desvio (05) - Incerteza-erro-desv
    $pdf->Text(55.83, 156.63, 'Inspector Stamp'); // new-36 - Inspector Stamp (05) - Inspector Stamp
    $pdf->Text(55.83, 185.47, 'Equipamento apto para tarefa'); // new-28 - Equipment pass (05) - Equipamento apto para tarefa
    $pdf->Text(22.23, 156.63, 'Task sub'); // new-34 - Task sub item (05) - Task sub
    $pdf->Text(1.32, 171.19, 'Measure Taken'); // new-32 - Measure Taken (Unit) (05) - Measure Taken
    $pdf->Text(166.69, 200.29, 'SN equipamento'); // new-38 - SN equipamento (06) - SN equipamento
    $pdf->Text(112.18, 185.47, 'Proxima Calibração'); // new-40 - Proxima Calibração (06) - Proxima Calibração
    $pdf->Text(112.18, 171.19, 'Measure Taken'); // new-41 - Measure Taken (Unit) (06) - Measure Taken
    $pdf->Text(166.69, 171.19, 'Incerteza-erro-desv'); // new-42 - Incerteza-erro-desvio (06) - Incerteza-erro-desv
    $pdf->Text(130.97, 156.63, 'Task sub'); // new-43 - Task sub item (06) - Task sub
    $pdf->Text(166.69, 156.63, 'Inspector Stamp'); // new-45 - Inspector Stamp (06) - Inspector Stamp
    $pdf->Text(166.69, 185.47, 'Equipamento apto para tarefa'); // new-37 - Equipment pass (06) - Equipamento apto para tarefa
    $pdf->Text(55.83, 265.91, 'SN equipamento'); // new-47 - SN equipamento (07) - SN equipamento
    $pdf->Text(1.32, 265.91, 'PN equipamento'); // new-48 - PN equipamento (07) - PN equipamento
    $pdf->Text(1.32, 251.09, 'Proxima Calibração'); // new-49 - Proxima Calibração (07) - Proxima Calibração
    $pdf->Text(1.32, 236.8, 'Measure Taken'); // new-50 - Measure Taken (Unit) (07) - Measure Taken
    $pdf->Text(55.83, 236.8, 'Incerteza-erro-desv'); // new-51 - Incerteza-erro-desvio (07) - Incerteza-erro-desv
    $pdf->Text(20.11, 222.25, 'Task sub'); // new-52 - Task sub item (07) - Task sub
    $pdf->Text(55.83, 251.09, 'Equipamento apto para tarefa'); // new-46 - Equipment pass (07) - Equipamento apto para tarefa
    $pdf->Text(112.18, 200.29, 'PN equipamento'); // new-39 - PN equipamento (06) - PN equipamento
    $pdf->Text(166.69, 265.91, 'SN equipamento'); // new-56 - SN equipamento (08) - SN equipamento
    $pdf->Text(112.18, 265.91, 'PN equipamento'); // new-57 - PN equipamento (08) - PN equipamento
    $pdf->Text(112.18, 251.09, 'Proxima Calibração'); // new-58 - Proxima Calibração (08) - Proxima Calibração
    $pdf->Text(112.18, 236.8, 'Measure Taken'); // new-59 - Measure Taken (Unit) (08) - Measure Taken
    $pdf->Text(166.69, 236.8, 'Incerteza-erro-desv'); // new-60 - Incerteza-erro-desvio (08) - Incerteza-erro-desv
    $pdf->Text(130.97, 222.25, 'Task sub'); // new-61 - Task sub item (08) - Task sub
    $pdf->Text(166.69, 222.25, 'Inspector Stamp'); // new-63 - Inspector Stamp (08) - Inspector Stamp
    $pdf->Text(166.69, 251.09, 'Equipamento apto para tarefa'); // new-55 - Equipment pass (08) - Equipamento apto para tarefa
    $pdf->Text(55.83, 222.25, 'Inspector Stamp'); // new-54 - Inspector Stamp (07) - Inspector Stamp
    $pdf->Text(1.32, 25.93, 'Data'); // p029 - Data - Data
    $pdf->Text(112.18, 25.93, 'Data (02)'); // new-7 - Data 02 - Data (02)
    $pdf->Text(1.32, 91.02, 'Data'); // new-17 - Data (03) - Data
    $pdf->Text(112.18, 91.02, 'Data'); // new-26 - Data (04) - Data
    $pdf->Text(1.32, 156.63, 'Data'); // new-35 - Data (05) - Data
    $pdf->Text(112.18, 156.63, 'Data'); // new-44 - Data (06) - Data
    $pdf->Text(1.32, 222.25, 'Data'); // new-53 - Data (07) - Data
    $pdf->Text(112.18, 222.25, 'Data'); // new-62 - Data (08) - Data
}
