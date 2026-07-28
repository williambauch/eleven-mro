<?php
function mMontarBarcode($data) {
    /**
     * mMontarBarcode — Monta o codigo de barras do Pack JIC.
     *
     * Regras do cliente (MRO-125), validadas com exemplos reais:
     *
     * ROTINA (R) — 41 caracteres:
     *   Pos 01     = "R"
     *   Pos 02-16  = Zeros (15 posicoes)
     *   Pos 17-22  = JIC (6 digitos, LPAD com zeros)
     *   Pos 23-25  = "000" (sequencial fixo para rotina)
     *   Pos 26-29  = project_id (4 digitos, LPAD com zeros)
     *   Pos 30-35  = Zeros (6 posicoes)
     *   Pos 36-37  = skill_code (2 digitos, fallback "XX")
     *   Pos 38     = "I"
     *   Pos 39-41  = "000"
     *   Exemplo: R0000000000000003700210002945000000A4I000
     *
     * N- (NRC nivel 1) — 41 caracteres:
     *   Pos 01     = "N"
     *   Pos 02-16  = Zeros (15 posicoes)
     *   Pos 17-22  = JIC (6 digitos, LPAD com zeros)
     *   Pos 23-25  = "000" (fixo, igual rotina)
     *   Pos 26-29  = project_id (4 digitos, LPAD com zeros)
     *   Pos 30-35  = Zeros (6 posicoes)
     *   Pos 36-37  = skill_code (2 digitos, fallback "XX")
     *   Pos 38     = "I"
     *   Pos 39-41  = Sequencial NRC (3 digitos)
     *   Exemplo: N0000000000000003700210002945000000A4I001
     *
     * NN- (NRC nivel 2) — 40 caracteres:
     *   Pos 01     = "N"
     *   Pos 02-11  = Zeros (10 posicoes)
     *   Pos 12     = "N"
     *   Pos 13-18  = JIC (6 digitos, LPAD com zeros)
     *   Pos 19-21  = Sequencial NN (3 digitos)
     *   Pos 22-24  = "000" (fixo)
     *   Pos 25-28  = project_id (4 digitos, LPAD com zeros)
     *   Pos 29-34  = Zeros (6 posicoes)
     *   Pos 35-36  = skill_code (2 digitos, fallback "XX")
     *   Pos 37     = "I"
     *   Pos 38-40  = Sequencial NN (3 digitos)
     *   Exemplo: N0000000000N14A0810010002885000000S4I001
     *
     * NNN (NRC nivel 3) — 40 caracteres:
     *   Pos 01     = "N"
     *   Pos 02-07  = Zeros (6 posicoes)
     *   Pos 08-09  = "NN"
     *   Pos 10-15  = JIC (6 digitos, LPAD com zeros)
     *   Pos 16-18  = Seq N1 (3 digitos)
     *   Pos 19-21  = Seq N2 (3 digitos)
     *   Pos 22-24  = "000" (fixo)
     *   Pos 25-28  = project_id (4 digitos, LPAD com zeros)
     *   Pos 29-34  = Zeros (6 posicoes)
     *   Pos 35-36  = skill_code (2 digitos, fallback "XX")
     *   Pos 37     = "I"
     *   Pos 38-40  = Seq N3 (3 digitos)
     *   Exemplo: N000000NN1314140080010002885000000P4I001
     *
     * @param array $data  Array associativo com os campos da tarefa
     *                     (task_code, skill_code, project_id, origin_document, is_nrc)
     * @return string      Barcode montado conforme regras do cliente
     */


    
    // =========================================================
    // PASSO 1: Detectar o tipo da tarefa pelo task_code
    // =========================================================
    // O task_code e a fonte mais confiavel para detectar o tipo,
    // pois o flag is_nrc nem sempre esta consistente no banco.
    //
    // Formatos conhecidos de task_code:
    //   Rotina:  "370021"              (apenas o numero JIC)
    //   N-:      "N370021001"          (N + JIC + sequencial 3 digitos)
    //            "NR370021-01"         (NR + JIC + hifen + sequencial 2 digitos)
    //   NN:      "NN14A081001001"      (NN + JIC + seq NN + seq N)
    //   NNN:     "NNN131414008001001"  (NNN + JIC + seq N1 + seq N2 + seq N3)

    $var_task_code = trim($data['task_code']);
    $var_tipo = 'R'; // padrao: rotina

    if (substr($var_task_code, 0, 3) === 'NNN') {
        $var_tipo = 'NNN';
    } elseif (substr($var_task_code, 0, 2) === 'NN') {
        $var_tipo = 'NN';
    } elseif (substr($var_task_code, 0, 1) === 'N' && strlen($var_task_code) > 1) {
        $var_tipo = 'N';
    }

    // =========================================================
    // PASSO 2: Extrair JIC e sequenciais do task_code
    // =========================================================
    // O JIC e o numero da rotina mae (6 digitos).
    // Os sequenciais identificam cada nivel de NRC.

    $var_jic = '';
    $var_seq1 = '000'; // sequencial principal (N-, NN ou NNN conforme o tipo)
    $var_seq2 = '000'; // sequencial secundario (usado no NNN)
    $var_seq3 = '000'; // sequencial terciario (usado no NNN)

    switch ($var_tipo) {
        case 'R':
            // Rotina: task_code e o proprio JIC
            // Ex: "370021" ou "14A081"
            $var_jic = $var_task_code;
            break;

        case 'N':
            // N-: dois formatos possiveis
            if (substr($var_task_code, 0, 2) === 'NR') {
                // Formato "NR370021-01": remove "NR", separa pelo hifen
                $var_sem_prefixo = substr($var_task_code, 2);
                $var_partes = explode('-', $var_sem_prefixo);
                $var_jic = $var_partes[0];
                $var_seq1 = isset($var_partes[1]) ? str_pad($var_partes[1], 3, '0', STR_PAD_LEFT) : '001';
            } else {
                // Formato "N370021001": N + JIC(6) + seq(3)
                $var_sem_prefixo = substr($var_task_code, 1);
                $var_jic = substr($var_sem_prefixo, 0, -3);
                $var_seq1 = substr($var_sem_prefixo, -3);
            }
            break;

        case 'NN':
            // NN: "NN14A081001001" → NN + JIC(6) + seq_NN(3) + seq_N(3)
            // No barcode, apenas o seq_NN e usado; o seq_N vai como "000" fixo
            $var_sem_prefixo = substr($var_task_code, 2);
            $var_jic  = substr($var_sem_prefixo, 0, -6);
            $var_seq1 = substr($var_sem_prefixo, -6, 3); // seq NN
            // seq2 (seq N) nao aparece no barcode NN, fica "000" fixo
            break;

        case 'NNN':
            // NNN: "NNN131414008001001" → NNN + JIC(6) + seq1(3) + seq2(3) + seq3(3)
            $var_sem_prefixo = substr($var_task_code, 3);
            $var_jic  = substr($var_sem_prefixo, 0, -9);
            $var_seq1 = substr($var_sem_prefixo, -9, 3);
            $var_seq2 = substr($var_sem_prefixo, -6, 3);
            $var_seq3 = substr($var_sem_prefixo, -3);
            break;
    }

    // Garante que o JIC tenha 6 posicoes (LPAD com zeros)
    // JICs alfanumericos como "14A081" sao preservados, apenas completados
    $var_jic = str_pad($var_jic, 6, '0', STR_PAD_LEFT);

    // =========================================================
    // PASSO 3: Obter project_id (4 digitos) e skill_code (2 digitos)
    // =========================================================
    // project_id: LPAD com zeros para 4 posicoes
    $var_project_id = isset($data['project_id']) ? (int)$data['project_id'] : 0;
    $var_projeto = str_pad($var_project_id, 4, '0', STR_PAD_LEFT);

    // skill_code: usa o valor do banco, fallback "XX" quando NULL/vazio
    $var_skill = trim($data['skill_code']);
    if (empty($var_skill)) {
        $var_skill = 'XX';
    }
    // Garante 2 posicoes
    $var_skill = str_pad(substr($var_skill, 0, 2), 2, '0', STR_PAD_LEFT);

    // =========================================================
    // PASSO 4: Montar o barcode conforme o tipo
    // =========================================================
    // Layouts validados com os exemplos reais do cliente.
    // Rotina e N- tem 41 caracteres; NN e NNN tem 40 caracteres.

    $var_barcode = '';

    switch ($var_tipo) {
        case 'R':
            // R + 15 zeros + JIC(6) + "000" + projeto(4) + 6 zeros + skill(2) + "I" + "000"
            // Total: 1+15+6+3+4+6+2+1+3 = 41 caracteres
            $var_barcode = 'R'
                . str_repeat('0', 15)       // pos 02-16
                . $var_jic                   // pos 17-22
                . '000'                      // pos 23-25 (sequencial fixo rotina)
                . $var_projeto               // pos 26-29
                . str_repeat('0', 6)         // pos 30-35
                . $var_skill                 // pos 36-37
                . 'I'                        // pos 38
                . '000';                     // pos 39-41
            break;

        case 'N':
            // N + 15 zeros + JIC(6) + "000" + projeto(4) + 6 zeros + skill(2) + "I" + seq1(3)
            // Total: 1+15+6+3+4+6+2+1+3 = 41 caracteres
            // O sequencial NRC aparece apenas no final (pos 39-41), pos 23-25 e "000" fixo
            $var_barcode = 'N'
                . str_repeat('0', 15)       // pos 02-16
                . $var_jic                   // pos 17-22
                . '000'                      // pos 23-25 (fixo, igual rotina)
                . $var_projeto               // pos 26-29
                . str_repeat('0', 6)         // pos 30-35
                . $var_skill                 // pos 36-37
                . 'I'                        // pos 38
                . $var_seq1;                 // pos 39-41
            break;

        case 'NN':
            // N + 10 zeros + "N" + JIC(6) + seq_NN(3) + "000" + projeto(4) + 6 zeros + skill(2) + "I" + seq_NN(3)
            // Total: 1+10+1+6+3+3+4+6+2+1+3 = 40 caracteres
            $var_barcode = 'N'
                . str_repeat('0', 10)       // pos 02-11
                . 'N'                        // pos 12
                . $var_jic                   // pos 13-18
                . $var_seq1                  // pos 19-21 (seq NN)
                . '000'                      // pos 22-24 (fixo)
                . $var_projeto               // pos 25-28
                . str_repeat('0', 6)         // pos 29-34
                . $var_skill                 // pos 35-36
                . 'I'                        // pos 37
                . $var_seq1;                 // pos 38-40 (seq NN)
            break;

        case 'NNN':
            // N + 6 zeros + "NN" + JIC(6) + seq1(3) + seq2(3) + "000" + projeto(4) + 6 zeros + skill(2) + "I" + seq3(3)
            // Total: 1+6+2+6+3+3+3+4+6+2+1+3 = 40 caracteres
            $var_barcode = 'N'
                . str_repeat('0', 6)        // pos 02-07
                . 'NN'                       // pos 08-09
                . $var_jic                   // pos 10-15
                . $var_seq1                  // pos 16-18
                . $var_seq2                  // pos 19-21
                . '000'                      // pos 22-24 (fixo)
                . $var_projeto               // pos 25-28
                . str_repeat('0', 6)         // pos 29-34
                . $var_skill                 // pos 35-36
                . 'I'                        // pos 37
                . $var_seq3;                 // pos 38-40
            break;
    }

    // Log de auditoria para validacao
    sc_log_add("barcode_debug", "task_code=" . $var_task_code . " tipo=" . $var_tipo . " barcode=" . $var_barcode . " len=" . strlen($var_barcode));

    return $var_barcode;
}
