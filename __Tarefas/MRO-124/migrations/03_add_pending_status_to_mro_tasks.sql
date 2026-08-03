-- MRO-124 - Pendências disponíveis para seleção nas tarefas

CREATE TABLE IF NOT EXISTS public.mro_tasks_pending_status (
    pending_id serial4 NOT NULL,
    pending_code varchar(20) NOT NULL,
    pending_description varchar(255) NOT NULL,
    is_active bool DEFAULT true NULL,
    created_at timestamp DEFAULT now() NULL,
    updated_at timestamp DEFAULT now() NULL,
    CONSTRAINT mro_tasks_pending_status_pkey PRIMARY KEY (pending_id),
    CONSTRAINT mro_tasks_pending_status_pending_code_key UNIQUE (pending_code)
);

INSERT INTO public.mro_tasks_pending_status (pending_code, pending_description)
VALUES
    ('PDGX', 'DIGEX PROCEDURES'),
    ('DEFERID', 'CUSTOMER DEFERRED'),
    ('CANC', 'TASK CANCELLED'),
    ('OUTSOURCE', 'PENDING - OUTSOURCE SERVICE'),
    ('TOOLS', 'PENDING - TOOLS EQUIPMENT'),
    ('PARTS', 'PENDING - PARTS / COMPONENTS'),
    ('PENDPART', 'PENDING - PARTS PARTIALLY AVAILABLE'),
    ('PARTSNO', 'PENDING - PARTS/ COMPONENTS (NO GO)'),
    ('PARTSGO', 'PENDING - PARTS/ COMPONENTS (GO)'),
    ('LG', 'PENDING - LANDING GEAR'),
    ('ENGINE', 'PENDING - ENGINE'),
    ('REPORT', 'PENDING - REPORT (LAUDO)'),
    ('FORM', 'PENDING - FORM / NF'),
    ('DUP', 'WAITING - CANCELLATION - DUPLICATE DOCUMENT'),
    ('LIBPROC', 'WAITING - CANCELLATION - NEF/MEL/CDL'),
    ('N/A', 'WAITING - CANCELLATION - NOT APPLICABLE'),
    ('CUSTOMER', 'WAITING - CUSTOMER DEFINITION'),
    ('APROV', 'WAITING - CUSTOMER APPROVAL'),
    ('REAPROV', 'WAITING - CUSTOMER REAPPROVAL'),
    ('QUAL', 'WAITING - QUALITY DEFINITION'),
    ('ENG DEFINI', 'WAITING - ENGINEERING DEFINITION'),
    ('PLANEJ', 'WAITING - PLANNING ANALYSIS'),
    ('PROCED', 'WAITING - PROCEDURE'),
    ('PREDEC', 'WAITING - PREDECESSORS'),
    ('STOR', 'WAITING - STORAGE SCHEDULE DATE'),
    ('ENGINE RUN', 'WAITING - ENGINE RUN'),
    ('APU', 'WAITING - APU ON'),
    ('POWER ON', 'WAITING - ELECTR PWR ON'),
    ('POWER OFF', 'WAITING - ELECTR PWR OFF'),
    ('HYD ON', 'WAITING - HYDRAULICS ON'),
    ('HYD OFF', 'WAITING - HYDRAULICS OFF'),
    ('PNEU ON', 'WAITING - PNEUMATICS ON'),
    ('JACKING', 'WAITING - ACFT ON JACKING'),
    ('ON GROUND', 'WAITING - ACFT ON GROUND'),
    ('RII', 'WAITING - IIO INSPECTION'),
    ('BORESCOPE', 'WAITING - BORESCOPE INSPECTION'),
    ('TESTS', 'WAITING - TESTS'),
    ('FLIGHT', 'WAITING - TEST FLIGHT'),
    ('FMO', 'WAITING - MAN POWER AVAILABLE'),
    ('OPENING', 'WAITING - ACCESS OPENING PHASE'),
    ('CLEANING', 'WAITING - CLEANING / WASHING PHASE'),
    ('INSP', 'WAITING - INSPECTION PHASE'),
    ('FMRO', 'WAITING - CORRECTIVE / MODIFICATIONS PHASE'),
    ('LUB/SER', 'WAITING - LUBRICATION / SERVICING PHASE'),
    ('CLOSING', 'WAITING - ACCESS CLOSING PHASE'),
    ('FTEST', 'WAITING - FINAL TESTS PHASE'),
    ('DELIV', 'WAITING - DELIVERY PHASE'),
    ('PROV', 'AVAILABLE - PARTS PROVIDING'),
    ('PAINT', 'AVAILABLE - PAINTING'),
    ('NDT', 'AVAILABLE - NON DESTRUCTIVE TESTING'),
    ('ANADE', 'AVAILABLE - STRUCTURAL DAMAGE ANALYSIS'),
    ('TROUBLE', 'AVAILABLE - TROUBLESHOOTING'),
    ('AVAIL', 'AVAILABLE - TASK AVAILABLE'),
    ('INPROG', 'AVAILABLE - TASK IN PROGRESS'),
    ('SUPSIG', 'SUPERVISOR SIGNOFF')
ON CONFLICT (pending_code) DO UPDATE
SET pending_description = EXCLUDED.pending_description;

ALTER TABLE public.mro_tasks
    ADD COLUMN IF NOT EXISTS pending_id int4 NULL;

ALTER TABLE public.mro_tasks
    DROP CONSTRAINT IF EXISTS fk_mro_tasks_pending_code,
    DROP CONSTRAINT IF EXISTS fk_mro_tasks_pending_id;

ALTER TABLE public.mro_tasks
    ADD CONSTRAINT fk_mro_tasks_pending_id
    FOREIGN KEY (pending_id)
    REFERENCES public.mro_tasks_pending_status (pending_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

CREATE INDEX IF NOT EXISTS idx_mro_tasks_pending_id
    ON public.mro_tasks (pending_id);
