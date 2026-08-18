<?php

function mCarregarMateriais($var_task_id, $var_task_code, $var_task_name, $var_projeto)
{
    // =========================================================================
    // MRO-126: mCarregarMateriais - Lista os materiais pendentes de separacao
    // de uma JIC (Provedoria - Bip de Saida)
    //
    // Criterio identico ao Gated Process (grid_provedoria_release):
    //   - Material nao aplicado (is_applied IS NOT TRUE)
    //   - Material bloqueante (is_blocking_task)
    //   - Material comprometido por compras (committed_qty >= planned_qty)
    //
    // Retorna array com status/task_id/task_code/header/html/total/separados
    // para o onExecute montar o JSON do terminal.
    // =========================================================================

        $var_task_id = (int)$var_task_id;

        // Materiais pendentes da JIC (mesmo criterio do Gated Process)
        $sql_mats = "SELECT tm.task_material_id, m.part_number, m.product_code, m.description,
                            tm.planned_qty, COALESCE(tm.committed_qty, 0),
                            (tm.separated_at IS NOT NULL) AS separado
                     FROM mro_task_materials tm
                     JOIN mro_materials m ON m.material_id = tm.material_id
                     WHERE tm.task_id = " . $var_task_id . "
                       AND tm.is_applied IS NOT TRUE
                       AND (m.is_blocking_task IS TRUE OR m.is_blocking_task IS NULL)
                       AND COALESCE(tm.committed_qty, 0) >= tm.planned_qty
                     ORDER BY (tm.separated_at IS NOT NULL) ASC, m.part_number ASC";
        sc_lookup(rs_mats, $sql_mats);

        $var_total = 0;
        $var_sep   = 0;
        $var_html  = "";

        if (!empty({rs_mats})) {
            foreach ({rs_mats} as $mat) {
                $var_total++;
                $var_ok = ($mat[6] == 't' || $mat[6] == true || $mat[6] == 1);

                if ($var_ok) { $var_sep++; }

                $var_pn   = $mat[1];
                $var_pc   = $mat[2];
                $var_desc = $mat[3];
                $var_qtd  = $mat[4];

                $var_badge = $var_ok
                    ? "<span style='color:#198754;'><i class='fa-solid fa-circle-check'></i> Separado</span>"
                    : "<span style='color:#6c757d;'><i class='fa-solid fa-hourglass-half'></i> Pendente</span>";

                $var_html .= "<li class='log-item' style='border-left:4px solid " . ($var_ok ? '#198754' : '#dee2e6') . ";'>
                    <div style='flex:1;'>
                        <b>$var_pn</b> <span style='color:#6c757d; font-size:11px;'>($var_pc)</span><br>
                        <span style='font-size:12px;'>" . htmlspecialchars($var_desc) . " - Qtd: $var_qtd</span>
                    </div>
                    $var_badge
                </li>";
            }
        }

        $var_header = "<div style='background:#e9ecef; padding:8px; border-radius:4px; margin-bottom:10px; font-weight:bold; font-size:12px; color:#0d6efd;'>
            JIC: $var_task_code | Projeto: $var_projeto<br>
            <span style='font-weight:normal; color:#495057;'>" . htmlspecialchars($var_task_name) . "</span><br>
            <span style='color:#198754;'>Separados: $var_sep de $var_total</span>
        </div>";

        if ($var_total == 0) {
            $var_html .= "<li class='log-item' style='color:#6c757d;'>Nenhum material pendente de separação para esta JIC.</li>";
        }

        // MRO-126: detecta se todos os materiais ja foram separados
        // (para avisar quando o almoxarife tentar bipar a JIC novamente)
        $var_tudo_separado = ($var_total > 0 && $var_sep == $var_total);

        return array(
            'status'         => 'success',
            'task_id'        => $var_task_id,
            'task_code'      => $var_task_code,
            'header'         => $var_header,
            'html'           => $var_html,
            'total'          => $var_total,
            'separados'      => $var_sep,
            'tudo_separado'  => $var_tudo_separado
        );
}
