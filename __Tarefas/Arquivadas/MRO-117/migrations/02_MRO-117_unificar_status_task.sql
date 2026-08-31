-- =============================================
-- Unificar status em module TASKS
-- Descricao: Adiciona novo module 'TASKS' com todos
-- os status de rotinas e nao-rotinas unificados
-- =============================================

-- Antes existiam modules separados (NRC e TASKS) com status duplicados.
-- Agora unificamos tudo em um unico module 'TASKS'.
--
-- Funcionamento do INSERT ... ON CONFLICT:
-- - Tenta inserir cada linha.
-- - Se o status_code ja existir (ex: 'DRAFT' ja existe em module NRC),
--   o ON CONFLICT faz um UPDATE em vez de INSERT, alterando os valores
--   para os novos (module = 'TASKS', novas cores/icone/label).
-- - Se o status_code NAO existir, insere normalmente.
-- Isso garante que nao quebrara se rodar o script mais de uma vez.

INSERT INTO "public".mro_sys_status (status_code, module, label_ptbr, kanban_color, icon, display_order)
VALUES
('DRAFT',              'TASKS', 'Rascunho',                '#6c757d', 'fas fa-edit',               1),
('PLANNED',            'TASKS', 'Planejado (P6)',          '#adb5bd', 'fas fa-calendar-alt',       2),
('NOT_STARTED',        'TASKS', 'Não Iniciado',            '#6c757d', 'fas fa-clipboard-list',     3),
('PENDING_ENG',        'TASKS', 'Fila Engenharia',         '#ffc107', 'fas fa-hard-hat',           4),
('ENG_REVIEW',         'TASKS', 'Em Análise (Eng)',        '#17a2b8', 'fas fa-search',             5),
('PENDING_COORD',      'TASKS', 'Aguardando Coordenador',  '#ffc107', 'fas fa-user-check',         6),
('PENDING_PLANNING',   'TASKS', 'Aguardando Planejamento', '#ffc107', 'fas fa-tasks',              7),
('PENDING_PROG',       'TASKS', 'Aguardando Programação',  '#ffc107', 'fas fa-hard-hat',           8),
('PROGRAMMING_OK',     'TASKS', 'Programação Validada',    '#17a2b8', 'fas fa-check-circle',       9),
('PENDING_OA',         'TASKS', 'Aguardando O&A',          '#fd7e14', 'fas fa-calculator',         10),
('WAITING_CUSTOMER',   'TASKS', 'Aguardando Aprovação',    '#fd7e14', 'fas fa-user-tie',           11),
('COMMERCIAL_REVIEW',  'TASKS', 'Revisão Comercial',       '#ffc107', 'fas fa-handshake',          12),
('APPROVED',           'TASKS', 'Aprovado (Cliente)',      '#28a745', 'fas fa-thumbs-up',          13),
('RELEASED',           'TASKS', 'Liberado',                '#28a745', 'fas fa-check',              14),
('IN_PROGRESS',        'TASKS', 'Em Execução',             '#28a745', 'fas fa-play',               15),
('PAUSED',             'TASKS', 'Pausado',                 '#ffc107', 'fas fa-pause',              16),
('BLOCKED',            'TASKS', 'Bloqueado',               '#dc3545', 'fas fa-ban',                17),
('PENDING_HANDOVER',   'TASKS', 'Aguardando Repasse',      '#fd7e14', 'fas fa-exchange-alt',       18),
('SUPSIG',             'TASKS', 'Aguardando Assinatura',   '#17a2b8', 'fas fa-file-signature',     19),
('COMPLETED',          'TASKS', 'Concluído',               '#20c997', 'fas fa-check-double',       20),
('CANCELLED',          'TASKS', 'Cancelado',               '#dc3545', 'fas fa-times-circle',       21),
('REJECTED',           'TASKS', 'Reprovado / Diferido',    '#dc3545', 'fas fa-thumbs-down',        22)
ON CONFLICT (status_code) DO UPDATE SET
    module        = 'TASKS',
    label_ptbr    = EXCLUDED.label_ptbr,
    kanban_color  = EXCLUDED.kanban_color,
    icon          = EXCLUDED.icon,
    display_order = EXCLUDED.display_order;
