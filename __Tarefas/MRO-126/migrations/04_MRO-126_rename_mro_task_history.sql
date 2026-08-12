-- =============================================
-- MRO-126: Renomeia mro_nrc_approval_log -> mro_task_history
-- Descricao: A tabela ja e usada como log de auditoria
-- geral de transicoes de status das tasks (rotinas e NRCs),
-- conforme decisoes registradas no MRO-117. O nome antigo
-- ("nrc_approval") nao reflete mais o proposito real.
--
-- Impacto levantado:
--   - 1 FK (task_id -> mro_tasks.task_id) preservada pelo RENAME
--   - 0 views referenciam a tabela
--   - 0 apps ScriptCase (config.json) usam como nome_tabela
--   - 20 arquivos .scriptcase + mro_engine.php serao ajustados
--     (busca/replace documentado em rename_mro_task_history.md)
--
-- Reversivel: basta RENAME de volta.
-- =============================================

-- 1. Renomeia a tabela (a FK task_id permanece valida)
ALTER TABLE "public".mro_nrc_approval_log RENAME TO mro_task_history;

-- 2. Renomeia a sequence padrao (log_id)
ALTER SEQUENCE "public".mro_nrc_approval_log_log_id_seq RENAME TO mro_task_history_log_id_seq;

-- 3. Atualiza o comentario da tabela
COMMENT ON TABLE "public".mro_task_history IS 'Log de auditoria de transicoes de status das tasks (rotinas e NRCs). Substitui o antigo mro_nrc_approval_log.';

-- =============================================
-- Validacao (rodar apos a migration)
-- =============================================
-- SELECT table_name FROM information_schema.tables
-- WHERE table_name = 'mro_task_history';
-- Deve retornar 1 linha.

-- SELECT sequence_name FROM information_schema.sequences
-- WHERE sequence_name = 'mro_task_history_log_id_seq';
-- Deve retornar 1 linha.
