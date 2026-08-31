# MRO-119 — Mapa de Fases (PDF Refinamento)

> **Aprovado em reuniao com cliente em 23/07/2026.**
> Apenas as 22 fases do documento `Refinamento EAP+Pendencias.pdf` serao mantidas.
> Descricoes no formato `"X.Y - Descricao"`.

---

## Tabela consolidada

| sort_order | phase_code | phase_name | Fase Pai |
|:---:|:---|:---|:---|
| 10 | `INDUCTI` | 0.0 - Aircraft Induction | Phase 0.0 |
| 20 | `OP/FUNC` | 1.0 - Initial Operational & Functional Tests | Phase 1.0 |
| 30 | `ENG RUN` | 1.1 - Engine Run | Phase 1.1 |
| 40 | `APU ON` | 1.2 - APU ON | Phase 1.2 |
| 50 | `PNEU ON` | 1.3 - Pneumatic ON | Phase 1.3 |
| 60 | `HYD ON` | 1.4 - Hydraulic ON | Phase 1.4 |
| 70 | `POW ON` | 1.5 - Power ON | Phase 1.5 |
| 80 | `JACKING` | 1.6 - ON Jacking | Phase 1.6 |
| 90 | `POW OFF` | 1.7 - Power OFF | Phase 1.7 |
| 100 | `A OPEN` | 2.0 - Open up & Washing | Phase 2.0 |
| 110 | `INSPEC` | 3.0 - Inspection Phase | Phase 3.0 |
| 120 | `INSP SA` | 3.1 - Inspection without access panel open | Phase 3.1 |
| 130 | `INSP CA` | 3.2 - Inspection with access panel open | Phase 3.2 |
| 140 | `REMOV` | 3.3 - Removal and sending parts to shop | Phase 3.3 |
| 150 | `DAMAGE` | 3.4 - Damage assessment | Phase 3.4 |
| 160 | `DISCREP` | 4.0 - Discrepancies Correction Phase | Phase 4.0 |
| 170 | `CORRECT` | 4.1 - Correction of discrepancies | Phase 4.1 |
| 180 | `MODIF` | 4.2 - Applying modifications | Phase 4.2 |
| 190 | `LUBRIC` | 4.3 - Lubrication & Servicing Phase | Phase 4.3 |
| 200 | `CLOSING` | 5.0 - Access Closing | Phase 5.0 |
| 210 | `FINTEST` | 6.0 - Final Operational & Functional Tests | Phase 6.0 |
| 220 | `FPREP` | 7.0 - Flight Preparation & Aircraft Delivery | Phase 7.0 |

---

## Hierarquia visual

```
 10  INDUCTI   0.0  Aircraft Induction
 20  OP/FUNC   1.0  Initial Operational & Functional Tests
 30  ENG RUN   1.1  Engine Run
 40  APU ON    1.2  APU ON
 50  PNEU ON   1.3  Pneumatic ON
 60  HYD ON    1.4  Hydraulic ON
 70  POW ON    1.5  Power ON
 80  JACKING   1.6  ON Jacking
 90  POW OFF   1.7  Power OFF
100  A OPEN    2.0  Open up & Washing
110  INSPEC    3.0  Inspection Phase
120  INSP SA   3.1  Inspection without access panel open
130  INSP CA   3.2  Inspection with access panel open
140  REMOV     3.3  Removal and sending parts to shop
150  DAMAGE    3.4  Damage assessment
160  DISCREP   4.0  Discrepancies Correction Phase
170  CORRECT   4.1  Correction of discrepancies
180  MODIF     4.2  Applying modifications
190  LUBRIC    4.3  Lubrication & Servicing Phase
200  CLOSING   5.0  Access Closing
210  FINTEST   6.0  Final Operational & Functional Tests
220  FPREP     7.0  Flight Preparation & Aircraft Delivery
```

---

## Migration

**Arquivo:** `migrations/MRO-119_01_phases_pdf_only.sql`

```sql
DELETE FROM public.mro_project_phases;

INSERT INTO public.mro_project_phases (phase_code, phase_name, sort_order)
VALUES
('INDUCTI', '0.0 - Aircraft Induction', 10),
('OP/FUNC', '1.0 - Initial Operational & Functional Tests', 20),
('ENG RUN', '1.1 - Engine Run', 30),
('APU ON',  '1.2 - APU ON', 40),
('PNEU ON', '1.3 - Pneumatic ON', 50),
('HYD ON',  '1.4 - Hydraulic ON', 60),
('POW ON',  '1.5 - Power ON', 70),
('JACKING', '1.6 - ON Jacking', 80),
('POW OFF', '1.7 - Power OFF', 90),
('A OPEN',  '2.0 - Open up & Washing', 100),
('INSPEC',  '3.0 - Inspection Phase', 110),
('INSP SA', '3.1 - Inspection without access panel open', 120),
('INSP CA', '3.2 - Inspection with access panel open', 130),
('REMOV',   '3.3 - Removal and sending parts to shop', 140),
('DAMAGE',  '3.4 - Damage assessment', 150),
('DISCREP', '4.0 - Discrepancies Correction Phase', 160),
('CORRECT', '4.1 - Correction of discrepancies', 170),
('MODIF',   '4.2 - Applying modifications', 180),
('LUBRIC',  '4.3 - Lubrication & Servicing Phase', 190),
('CLOSING', '5.0 - Access Closing', 200),
('FINTEST', '6.0 - Final Operational & Functional Tests', 210),
('FPREP',   '7.0 - Flight Preparation & Aircraft Delivery', 220);
```
