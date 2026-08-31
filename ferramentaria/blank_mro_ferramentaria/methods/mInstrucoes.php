<?php

function mInstrucoes()
{
    // ============================================================
    // MÉTODO: mInstrucoes
    // Conteúdo do modal de Instruções e Regras de Negócio do
    // Terminal de Ferramentaria (blank_mro_ferramentaria).
    // Chamado pelo botão "Instruções" no header da tela.
    // ============================================================

    echo "
    <div id='modal_instrucoes' style='display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99998; font-family:Segoe UI, sans-serif;'>
        <div style='background:#fff; width:92%; max-width:760px; max-height:90vh; margin:4vh auto; border-radius:10px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column;'>
            <div style='background:#0d6efd; color:#fff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;'>
                <h3 style='margin:0; font-size:17px;'><i class='fa-solid fa-circle-info'></i> Instruções do Terminal de Ferramentaria</h3>
                <button type='button' onclick='fecharInstrucoes()' style='background:none; border:none; color:#fff; font-size:26px; cursor:pointer; line-height:1;' title='Fechar'>&times;</button>
            </div>
            <div style='padding:20px; overflow-y:auto; font-size:13px; color:#212529; line-height:1.5;'>

                <h4 style='color:#0d6efd; margin:0 0 10px;'><i class='fa-solid fa-barcode'></i> Como usar o Terminal</h4>
                <ol style='margin:0 0 16px; padding-left:20px;'>
                    <li><strong>Apontamento (SAÍDA):</strong> bipe o <strong>crachá do mecânico</strong> e depois a <strong>etiqueta da ferramenta</strong>. O sistema vincula a custódia à tarefa ativa do mecânico.</li>
                    <li><strong>Desapontamento (RETORNO):</strong> bipe a <strong>etiqueta da ferramenta</strong> e confirme a condição de retorno (OK, Avariada ou Extraviada).</li>
                    <li>Ao bipar o crachá, ele fica <strong>fixo automaticamente</strong> para bipar várias ferramentas em sequência.</li>
                    <li>Ao finalizar, use o botão <strong>Limpar Sessão</strong> para liberar a estação para o próximo mecânico.</li>
                </ol>

                <h4 style='color:#198754; margin:0 0 10px;'><i class='fa-solid fa-shield-halved'></i> Regras de Negócio</h4>
                <ul style='margin:0 0 16px; padding-left:20px;'>
                    <li><strong>Trava de Clock-In:</strong> o mecânico só pode retirar ferramenta se tiver um apontamento ativo (timesheet aberto) na tarefa.</li>
                    <li><strong>Calibração:</strong> ferramenta com calibração vencida <strong>não pode ser retirada</strong> — é enviada para \"FERRAMENTA EM CALIBRAÇÃO\" e <strong>não poderá ser emprestada</strong> até passar por recalibração.</li>
                    <li><strong>Ferramenta emprestada:</strong> não é possível retirar uma ferramenta que já está em uso por outro mecânico.</li>
                    <li><strong>Ferramenta danificada/extraviada:</strong> o check-in nessas condições retém a ferramenta e abre um <strong>relatório SGSO</strong> (Qualidade).</li>
                    <li><strong>Ferramenta fora do planejado:</strong> se a ferramenta não está prevista na JIC, o sistema permite com aviso de <strong>Extra As-Built</strong>.</li>
                    <li><strong>Vínculo com a JIC:</strong> toda retirada fica vinculada à tarefa ativa do mecânico, garantindo a rastreabilidade da custódia.</li>
                </ul>

                <h4 style='color:#dc3545; margin:0 0 10px;'><i class='fa-solid fa-triangle-exclamation'></i> Importante</h4>
                <ul style='margin:0; padding-left:20px;'>
                    <li>Em caso de <strong>avaria ou perda</strong>, preencha o relatório de ocorrência que abre automaticamente.</li>
                    <li>Não devolva ferramenta sem antes selecionar a <strong>condição de retorno</strong> correta.</li>
                </ul>

            </div>
            <div style='padding:14px 20px; border-top:1px solid #e9ecef; text-align:right;'>
                <button type='button' onclick='fecharInstrucoes()' style='background:#0d6efd; color:#fff; border:none; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:bold; cursor:pointer;'>Entendi, fechar</button>
            </div>
        </div>
    </div>
    ";
}
