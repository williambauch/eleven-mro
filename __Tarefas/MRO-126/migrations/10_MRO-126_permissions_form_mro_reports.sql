-- =============================================
-- MRO-126: Permissoes para form_mro_reports
-- Relatorio de Ocorrencia (Qualidade SGSO)
-- TF-60-013 (DANO) / TF-60-041 (PERDA)
--
-- Grupos:
--   1  (Administrador)  - ja possui acesso total (Y Y Y Y Y Y)
--   12 (FERRAMENTARIA)  - ja possui acesso total (Y Y Y Y Y Y)
--   3  (MECANICO)       - NOVO: acesso + insert (cria o relatorio no ato da devolucao)
--                         update/delete/export/print permanecem vazios (''),
--                         pois edicao de relatorio de QUALIDADE e da Ferramentaria/Supervisao
-- =============================================

INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
VALUES ('form_mro_reports', 3, 'Y', 'Y', '', '', '', '')
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;