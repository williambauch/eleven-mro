-- =============================================
-- MRO-126: Novo status PENDING_PROVIDER
-- Status intermediario do Gated Process:
-- Planejamento libera JIC COM material bloqueante
-- (is_blocking_task true) -> PENDING_PROVIDER.
-- A provedoria lista, separa os materiais e
-- libera para RELEASED final.
-- =============================================

INSERT INTO mro_sys_status (status_code, label_ptbr, module)
VALUES ('PENDING_PROVIDER', 'Aguardando Provedoria', 'TASKS')
ON CONFLICT (status_code) DO UPDATE SET
    label_ptbr = EXCLUDED.label_ptbr,
    module = EXCLUDED.module;

-- =============================================
-- Validacao (apos executar):
-- SELECT status_code, label_ptbr, module
-- FROM mro_sys_status
-- WHERE status_code = 'PENDING_PROVIDER';
-- =============================================
