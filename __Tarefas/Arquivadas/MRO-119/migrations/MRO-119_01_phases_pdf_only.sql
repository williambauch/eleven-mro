-- ===================================================================
-- MRO-119: Limpar e recadastrar fases conforme PDF Refinamento
--
-- Decisao do cliente (reuniao 23/07): manter APENAS as fases que
-- constam no documento "Refinamento EAP+Pendencias.pdf".
-- Descricoes no formato: "X.Y - Descricao"
--
-- ===================================================================

BEGIN;

-- 1. Limpar todos os registros existentes
DELETE FROM public.mro_project_phases;

-- 2. Inserir apenas as 22 fases do PDF Refinamento
--    (ordenado por sort_order)

INSERT INTO public.mro_project_phases (phase_code, phase_name, sort_order)
VALUES
-- Phase 0.0
('INDUCTI', '0.0 - Aircraft Induction', 10),

-- Phase 1.x — Initial Operational & Functional Tests
('OP/FUNC', '1.0 - Initial Operational & Functional Tests', 20),
('ENG RUN', '1.1 - Engine Run', 30),
('APU ON',  '1.2 - APU ON', 40),
('PNEU ON', '1.3 - Pneumatic ON', 50),
('HYD ON',  '1.4 - Hydraulic ON', 60),
('POW ON',  '1.5 - Power ON', 70),
('JACKING', '1.6 - ON Jacking', 80),
('POW OFF', '1.7 - Power OFF', 90),

-- Phase 2.0
('A OPEN',  '2.0 - Open up & Washing', 100),

-- Phase 3.x — Inspection Phase
('INSPEC',  '3.0 - Inspection Phase', 110),
('INSP SA', '3.1 - Inspection without access panel open', 120),
('INSP CA', '3.2 - Inspection with access panel open', 130),
('REMOV',   '3.3 - Removal and sending parts to shop', 140),
('DAMAGE',  '3.4 - Damage assessment', 150),

-- Phase 4.x — Discrepancies Correction Phase
('DISCREP', '4.0 - Discrepancies Correction Phase', 160),
('CORRECT', '4.1 - Correction of discrepancies', 170),
('MODIF',   '4.2 - Applying modifications', 180),
('LUBRIC',  '4.3 - Lubrication & Servicing Phase', 190),

-- Phase 5.0
('CLOSING', '5.0 - Access Closing', 200),

-- Phase 6.0
('FINTEST', '6.0 - Final Operational & Functional Tests', 210),

-- Phase 7.0
('FPREP',   '7.0 - Flight Preparation & Aircraft Delivery', 220);

COMMIT;
