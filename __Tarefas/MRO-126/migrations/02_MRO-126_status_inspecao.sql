-- =============================================
-- MRO-126: Status de Inspecao (Duplo Check RII)
-- Descricao: Adiciona os status PENDING_INSP1 e
-- PENDING_INSP2 a esteira de aprovacao.
-- CONCLUDED NAO deve existir (o terminal e COMPLETED).
--
-- Fluxo: SUPSIG -> PENDING_INSP1 -> PENDING_INSP2 -> COMPLETED
--
-- Idempotente: usa ON CONFLICT (status_code) DO UPDATE.
-- =============================================

INSERT INTO "public".mro_sys_status (status_code, module, label_ptbr, kanban_color, icon, display_order)
VALUES
('PENDING_INSP1', 'TASKS', 'Fila Inspeção 1', '#17a2b8', 'fas fa-search', 22),
('PENDING_INSP2', 'TASKS', 'Fila Inspeção 2', '#6f42c1', 'fas fa-user-shield', 23)
ON CONFLICT (status_code) DO UPDATE
SET module         = EXCLUDED.module,
    label_ptbr     = EXCLUDED.label_ptbr,
    kanban_color   = EXCLUDED.kanban_color,
    icon           = EXCLUDED.icon,
    display_order  = EXCLUDED.display_order;

-- =============================================
-- Validacao
-- =============================================
-- SELECT status_code, label_ptbr FROM "public".mro_sys_status
-- WHERE status_code IN ('PENDING_INSP1','PENDING_INSP2')
-- ORDER BY display_order;
