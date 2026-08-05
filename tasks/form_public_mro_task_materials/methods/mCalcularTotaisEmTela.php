<?php

function mCalcularTotaisEmTela()
{
    // mCalcularTotaisEmTela
    // Recalcula total_cost e committed_total_cost usando os valores
    // atuais em tela. Mesma regra de calculo do import control_import_empenhos:
    //   - material do CLIENTE tem custo zerado
    //   - total_cost           = planned_qty * unit_cost
    //   - committed_total_cost = committed_qty * committed_unit_cost
    //     (se committed_unit_cost vazio, usa unit_cost)
    // Chamado pelos eventos Ajax onChange dos campos de qty e valor.

        $var_qty           = (float){planned_qty};
        $var_custo         = (float){unit_cost};
        $var_committed_qty = (float){committed_qty};
        $var_committed_custo = (float){committed_unit_cost};

    	// Fallback: empenho sem custo proprio usa o custo normal
        if ($var_committed_custo <= 0) {
            $var_committed_custo = $var_custo;
            {committed_unit_cost} = number_format($var_committed_custo, 2, ',', '.');
        }

        // Regra do import: material do CLIENTE nao tem custo
        if (strtoupper(trim({material_source})) == 'CLIENTE') {
            // Zera tambem os campos em tela (mesmo padrao do import)
            {unit_cost}          = 0.00;
            {committed_unit_cost} = 0.00;
            $var_custo           = 0.00;
            $var_committed_custo  = 0.00;
        }

        {total_cost}          = number_format($var_qty * $var_custo, 2, ',', '.');
        {committed_total_cost} = number_format($var_committed_qty * $var_committed_custo, 2, ',', '.');
}
