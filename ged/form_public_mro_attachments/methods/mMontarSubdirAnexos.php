<?php

function mMontarSubdirAnexos()
{
    // Monta o subdiretorio a partir dos campos (funciona em inclusao E edicao)
        if (!empty({task_id})) {
            // Resolve o projeto da tarefa para montar /anexos_mro/p{project}/t{task}
            $var_sql = "SELECT project_id FROM public.mro_tasks WHERE task_id = " . (int){task_id};
            sc_lookup(rs_proj, $var_sql);

            if ({rs_proj} === false) {
                sc_error_message("Erro ao consultar o projeto da tarefa.");
                sc_error_exit();
            }

            $var_pid = (!empty({rs_proj}) && !empty({rs_proj[0][0]})) ? (int){rs_proj[0][0]} : 0;
            [glo_att_subdir] = "/anexos_mro/p" . $var_pid . "/t" . (int){task_id};
        } elseif (!empty({project_id})) {
            [glo_att_subdir] = "/anexos_mro/p" . (int){project_id};
        } elseif (!empty({aircraft_id})) {
            [glo_att_subdir] = "/anexos_mro/a" . (int){aircraft_id};
        }

        // DEBUG: exibe o contexto e o subdiretorio montado
        //echo "DEBUG task_id=" . (int){task_id} . " project_id=" . (int){project_id} . " aircraft_id=" . (int){aircraft_id} . " subdir=" . [glo_att_subdir] . "<BR>";
}
