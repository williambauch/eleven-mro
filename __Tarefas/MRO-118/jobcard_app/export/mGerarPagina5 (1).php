<?php

function mGerarPagina5XXX($pdf) {
    // =========================================================
    // Pagina 5 — gerada do editor em 22/07/2026, 16:52:19
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
    $pdf->Text(1.32, 25.93, 'Data'); // p029 - Data - Data
    $pdf->MultiCell(34.13, 0, 'Task sub', 0, 'L', false, 1, 20.11, 25.93, true); // p034 - Task sub item - Task sub (multilinha)
    $pdf->MultiCell(49.21, 0, 'Inspector Stamp', 0, 'L', false, 1, 55.83, 25.93, true); // p054 - Inspector Stamp - Inspector Stamp (multilinha)
    $pdf->Text(112.18, 25.93, 'Data (02)'); // new-7 - Data 02 - Data (02)
    $pdf->MultiCell(33.6, 0, 'Task sub item (02)', 0, 'L', false, 1, 130.97, 25.93, true); // new-1 - Task sub item 02 - Task sub item (02) (multilinha)
    $pdf->MultiCell(47.89, 0, 'Inspector Stamp (02)', 0, 'L', false, 1, 166.69, 25.93, true); // new-6 - Inspector Stamp 02 - Inspector Stamp (02) (multilinha)
    $pdf->MultiCell(52.12, 0, 'Measure Taken', 0, 'L', false, 1, 1.32, 40.48, true); // p095 - Measure Taken (Unit) - Measure Taken (multilinha)
    $pdf->MultiCell(49.74, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 55.3, 40.48, true); // p107 - Incerteza-erro-desvio - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(52.65, 0, 'Measure Taken (Unit)  (02)', 0, 'L', false, 1, 111.92, 40.48, true); // new-8 - Measure Taken (Unit) 02 - Measure Taken (Unit)  (02) (multilinha)
    $pdf->MultiCell(47.89, 0, 'Incerteza-erro-desvio (02)', 0, 'L', false, 1, 166.69, 40.48, true); // new-2 - Incerteza-erro-desvio 02 - Incerteza-erro-desvio (02) (multilinha)
    $pdf->MultiCell(52.92, 0, 'Proxima Calibração', 0, 'L', false, 1, 0.53, 54.77, true); // p168 - Proxima Calibração - Proxima Calibração (multilinha)
    $pdf->MultiCell(48.68, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 55.83, 54.77, true); // p192 - Equipment pass - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.12, 0, 'Proxima Calibração (02)', 0, 'L', false, 1, 112.18, 54.77, true); // new-3 - Proxima Calibração 02 - Proxima Calibração (02) (multilinha)
    $pdf->MultiCell(48.15, 0, 'Equipment pass (02)', 0, 'L', false, 1, 166.69, 54.77, true); // new-9 - Equipment pass 02 - Equipment pass (02) (multilinha)
    $pdf->MultiCell(52.65, 0, 'PN equipamento', 0, 'L', false, 1, 1.32, 69.59, true); // p198 - PN equipamento - PN equipamento (multilinha)
    $pdf->MultiCell(48.68, 0, 'SN equipamento', 0, 'L', false, 1, 55.83, 69.59, true); // p244 - SN equipamento - SN equipamento (multilinha)
    $pdf->MultiCell(52.65, 0, 'PN equipamento (02)', 0, 'L', false, 1, 111.92, 69.59, true); // new-4 - PN equipamento 02 - PN equipamento (02) (multilinha)
    $pdf->MultiCell(47.89, 0, 'SN equipamento (02)', 0, 'L', false, 1, 166.95, 69.59, true); // new-5 - SN equipamento 02 - SN equipamento (02) (multilinha)
    $pdf->Text(1.32, 91.02, 'Data'); // new-17 - Data (03) - Data
    $pdf->MultiCell(33.6, 0, 'Task sub', 0, 'L', false, 1, 20.11, 91.02, true); // new-16 - Task sub item (03) - Task sub (multilinha)
    $pdf->MultiCell(48.68, 0, 'Inspector Stamp', 0, 'L', false, 1, 55.83, 91.02, true); // new-18 - Inspector Stamp (03) - Inspector Stamp (multilinha)
    $pdf->Text(112.18, 91.02, 'Data'); // new-26 - Data (04) - Data
    $pdf->MultiCell(33.6, 0, 'Task sub', 0, 'L', false, 1, 131.23, 91.02, true); // new-25 - Task sub item (04) - Task sub (multilinha)
    $pdf->MultiCell(48.42, 0, 'Inspector Stamp', 0, 'L', false, 1, 166.69, 91.02, true); // new-27 - Inspector Stamp (04) - Inspector Stamp (multilinha)
    $pdf->MultiCell(52.65, 0, 'Measure Taken', 0, 'L', false, 1, 1.32, 105.57, true); // new-14 - Measure Taken (Unit) (03) - Measure Taken (multilinha)
    $pdf->MultiCell(48.68, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 55.83, 105.57, true); // new-15 - Incerteza-erro-desvio (03) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(52.12, 0, 'Measure Taken', 0, 'L', false, 1, 112.18, 105.57, true); // new-23 - Measure Taken (Unit) (04) - Measure Taken (multilinha)
    $pdf->MultiCell(48.15, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 166.69, 105.57, true); // new-24 - Incerteza-erro-desvio (04) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(52.92, 0, 'Proxima Calibração', 0, 'L', false, 1, 1.32, 119.86, true); // new-13 - Proxima Calibração (03) - Proxima Calibração (multilinha)
    $pdf->MultiCell(49.48, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 55.83, 119.86, true); // new-10 - Equipment pass (03) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(51.86, 0, 'Proxima Calibração', 0, 'L', false, 1, 112.18, 119.86, true); // new-22 - Proxima Calibração (04) - Proxima Calibração (multilinha)
    $pdf->MultiCell(48.15, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 166.69, 119.86, true); // new-19 - Equipment pass (04) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.65, 0, 'PN equipamento', 0, 'L', false, 1, 1.32, 134.67, true); // new-12 - PN equipamento (03) - PN equipamento (multilinha)
    $pdf->MultiCell(49.21, 0, 'SN equipamento', 0, 'L', false, 1, 55.83, 134.67, true); // new-11 - SN equipamento (03) - SN equipamento (multilinha)
    $pdf->MultiCell(52.39, 0, 'PN equipamento', 0, 'L', false, 1, 112.18, 134.67, true); // new-21 - PN equipamento (04) - PN equipamento (multilinha)
    $pdf->MultiCell(48.15, 0, 'SN equipamento', 0, 'L', false, 1, 166.69, 134.67, true); // new-20 - SN equipamento (04) - SN equipamento (multilinha)
    $pdf->Text(1.32, 156.63, 'Data'); // new-35 - Data (05) - Data
    $pdf->MultiCell(32.28, 0, 'Task sub', 0, 'L', false, 1, 22.23, 156.63, true); // new-34 - Task sub item (05) - Task sub (multilinha)
    $pdf->MultiCell(48.42, 0, 'Inspector Stamp', 0, 'L', false, 1, 55.83, 156.63, true); // new-36 - Inspector Stamp (05) - Inspector Stamp (multilinha)
    $pdf->Text(112.18, 156.63, 'Data'); // new-44 - Data (06) - Data
    $pdf->MultiCell(33.6, 0, 'Task sub', 0, 'L', false, 1, 130.97, 156.63, true); // new-43 - Task sub item (06) - Task sub (multilinha)
    $pdf->MultiCell(47.89, 0, 'Inspector Stamp', 0, 'L', false, 1, 166.69, 156.63, true); // new-45 - Inspector Stamp (06) - Inspector Stamp (multilinha)
    $pdf->MultiCell(52.12, 0, 'Measure Taken', 0, 'L', false, 1, 1.32, 171.19, true); // new-32 - Measure Taken (Unit) (05) - Measure Taken (multilinha)
    $pdf->MultiCell(48.95, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 55.83, 171.19, true); // new-33 - Incerteza-erro-desvio (05) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(52.12, 0, 'Measure Taken', 0, 'L', false, 1, 112.18, 171.19, true); // new-41 - Measure Taken (Unit) (06) - Measure Taken (multilinha)
    $pdf->MultiCell(48.15, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 166.69, 171.19, true); // new-42 - Incerteza-erro-desvio (06) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(52.39, 0, 'Proxima Calibração', 0, 'L', false, 1, 1.32, 185.47, true); // new-31 - Proxima Calibração (05) - Proxima Calibração (multilinha)
    $pdf->MultiCell(48.95, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 55.83, 185.47, true); // new-28 - Equipment pass (05) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.39, 0, 'Proxima Calibração', 0, 'L', false, 1, 112.18, 185.47, true); // new-40 - Proxima Calibração (06) - Proxima Calibração (multilinha)
    $pdf->MultiCell(48.15, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 166.69, 185.47, true); // new-37 - Equipment pass (06) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.65, 0, 'PN equipamento', 0, 'L', false, 1, 1.32, 200.29, true); // new-30 - PN equipamento (05) - PN equipamento (multilinha)
    $pdf->MultiCell(48.95, 0, 'SN equipamento', 0, 'L', false, 1, 55.83, 200.29, true); // new-29 - SN equipamento (05) - SN equipamento (multilinha)
    $pdf->MultiCell(51.86, 0, 'PN equipamento', 0, 'L', false, 1, 112.18, 200.29, true); // new-39 - PN equipamento (06) - PN equipamento (multilinha)
    $pdf->MultiCell(48.15, 0, 'SN equipamento', 0, 'L', false, 1, 166.69, 200.29, true); // new-38 - SN equipamento (06) - SN equipamento (multilinha)
    $pdf->Text(1.32, 222.25, 'Data'); // new-53 - Data (07) - Data
    $pdf->MultiCell(31.75, 0, 'Task sub', 0, 'L', false, 1, 21.7, 222.25, true); // new-52 - Task sub item (07) - Task sub (multilinha)
    $pdf->Text(55.83, 222.25, 'Inspector Stamp'); // new-54 - Inspector Stamp (07) - Inspector Stamp
    $pdf->Text(112.18, 222.25, 'Data'); // new-62 - Data (08) - Data
    $pdf->MultiCell(31.75, 0, 'Task sub', 0, 'L', false, 1, 132.03, 222.25, true); // new-61 - Task sub item (08) - Task sub (multilinha)
    $pdf->MultiCell(47.63, 0, 'Inspector Stamp', 0, 'L', false, 1, 166.69, 222.25, true); // new-63 - Inspector Stamp (08) - Inspector Stamp (multilinha)
    $pdf->MultiCell(52.65, 0, 'Measure Taken', 0, 'L', false, 1, 1.32, 236.8, true); // new-50 - Measure Taken (Unit) (07) - Measure Taken (multilinha)
    $pdf->MultiCell(48.42, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 55.83, 236.8, true); // new-51 - Incerteza-erro-desvio (07) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(51.59, 0, 'Measure Taken', 0, 'L', false, 1, 111.92, 236.8, true); // new-59 - Measure Taken (Unit) (08) - Measure Taken (multilinha)
    $pdf->MultiCell(47.36, 0, 'Incerteza-erro-desv', 0, 'L', false, 1, 166.69, 236.8, true); // new-60 - Incerteza-erro-desvio (08) - Incerteza-erro-desv (multilinha)
    $pdf->MultiCell(53.71, 0, 'Proxima Calibração', 0, 'L', false, 1, 1.32, 251.09, true); // new-49 - Proxima Calibração (07) - Proxima Calibração (multilinha)
    $pdf->MultiCell(47.36, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 57.41, 251.09, true); // new-46 - Equipment pass (07) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.12, 0, 'Proxima Calibração', 0, 'L', false, 1, 111.65, 251.09, true); // new-58 - Proxima Calibração (08) - Proxima Calibração (multilinha)
    $pdf->MultiCell(48.15, 0, 'Equipamento apto para tarefa', 0, 'L', false, 1, 166.69, 251.09, true); // new-55 - Equipment pass (08) - Equipamento apto para tarefa (multilinha)
    $pdf->MultiCell(52.12, 0, 'PN equipamento', 0, 'L', false, 1, 1.32, 265.91, true); // new-48 - PN equipamento (07) - PN equipamento (multilinha)
    $pdf->MultiCell(48.42, 0, 'SN equipamento', 0, 'L', false, 1, 55.83, 265.91, true); // new-47 - SN equipamento (07) - SN equipamento (multilinha)
    $pdf->MultiCell(52.12, 0, 'PN equipamento', 0, 'L', false, 1, 112.18, 265.91, true); // new-57 - PN equipamento (08) - PN equipamento (multilinha)
    $pdf->MultiCell(48.15, 0, 'SN equipamento', 0, 'L', false, 1, 166.69, 265.91, true); // new-56 - SN equipamento (08) - SN equipamento (multilinha)
}
