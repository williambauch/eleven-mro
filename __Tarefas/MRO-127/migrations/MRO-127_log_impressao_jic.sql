-- ============================================================
-- MRO-127: Tabela de log de impressao do Pack JIC (lote e individual)
-- Banco: conn_mro (PostgreSQL)
-- ============================================================

CREATE TABLE IF NOT EXISTS mro_task_print_log (
    print_log_id     SERIAL PRIMARY KEY,
    batch_id         VARCHAR(40)  NOT NULL,
    task_id          INTEGER      NOT NULL DEFAULT 0,
    task_code        VARCHAR(50)  NOT NULL DEFAULT '',
    usuario          VARCHAR(100) NOT NULL DEFAULT '',
    data_solicitacao TIMESTAMP    NOT NULL DEFAULT NOW(),
    data_fim         TIMESTAMP,
    status           VARCHAR(20)  NOT NULL DEFAULT 'em_andamento',
    mensagem_erro    TEXT,
    arquivo_gerado   VARCHAR(500)
);

-- Indices para consulta rapida por lote e por status
CREATE INDEX IF NOT EXISTS idx_jic_print_log_batch  ON mro_task_print_log (batch_id);
CREATE INDEX IF NOT EXISTS idx_jic_print_log_status ON mro_task_print_log (status);
CREATE INDEX IF NOT EXISTS idx_jic_print_log_dt     ON mro_task_print_log (data_solicitacao DESC);

COMMENT ON TABLE  mro_task_print_log IS 'Log de impressao do Pack JIC (MRO-127)';
COMMENT ON COLUMN mro_task_print_log.batch_id IS 'Identificador do lote de impressao (agrupa logs de uma mesma solicitacao)';
COMMENT ON COLUMN mro_task_print_log.task_id IS 'Task processada (0 quando o erro e geral do lote)';
COMMENT ON COLUMN mro_task_print_log.status IS 'em_andamento | concluida | erro';
COMMENT ON COLUMN mro_task_print_log.arquivo_gerado IS 'Caminho do PDF/ZIP gerado (usar file_exists para saber se ainda existe)';
