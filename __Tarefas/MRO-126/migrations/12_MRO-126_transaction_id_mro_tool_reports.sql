-- =============================================
-- MRO-126: transaction_id em mro_tool_reports
-- Vincula cada relatorio de ocorrencia (DANO/PERDA)
-- a transacao (devolucao) que o originou.
--
-- Contexto: antes o relatorio nao guardava a transacao,
-- e o vinculo era feito por heuristica (tool+employee+task+data).
-- Com transaction_id, o painel do mecanico lista avarias com
-- ou sem relatorio e o form preenche/cria com vinculo exato.
-- =============================================

-- 1. Adiciona a coluna (nullable para relatorios historicos)
ALTER TABLE public.mro_tool_reports
    ADD COLUMN transaction_id integer REFERENCES public.mro_tool_transactions (transaction_id);

-- 2. Backfill dos relatorios existentes (janela de 5 min apos o checkin)
UPDATE public.mro_tool_reports r
SET transaction_id = tt.transaction_id
FROM public.mro_tool_transactions tt
WHERE tt.tool_id = r.tool_id
  AND tt.employee_id = r.employee_id
  AND tt.condition_on_return IN ('DANO', 'PERDA')
  AND ABS(EXTRACT(EPOCH FROM (r.report_date - tt.checkin_time))) < 300;

-- 3. Garante 1 relatorio por transacao (evita duplicidade)
ALTER TABLE public.mro_tool_reports
    ADD CONSTRAINT mro_tool_reports_transaction_id_key UNIQUE (transaction_id);

-- 4. Index para consulta por transacao (painel do mecanico)
CREATE INDEX IF NOT EXISTS idx_mro_tool_reports_transaction_id
    ON public.mro_tool_reports (transaction_id);

-- =============================================
-- Validacao (apos executar): deve retornar 6 linhas (todos os relatorios vinculados)
-- =============================================
-- SELECT r.report_id, r.transaction_id, tt.checkin_time, tt.condition_on_return
-- FROM public.mro_tool_reports r
-- LEFT JOIN public.mro_tool_transactions tt ON tt.transaction_id = r.transaction_id
-- ORDER BY r.report_id;
