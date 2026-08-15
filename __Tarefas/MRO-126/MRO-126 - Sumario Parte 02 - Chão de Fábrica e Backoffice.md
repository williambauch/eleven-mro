# Revisão Chão de Fábrica & Backoffice — Parte 02 (Índice)

> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice, separado por módulos de trabalho.
>
> **Parte 01** — Fluxo de Execução, Aprovação e Duplo Check (RII): [`MRO-126 - Sumario Parte 01 - Fluxo RII.md`](MRO-126%20-%20Sumario%20Parte%2001%20-%20Fluxo%20RII.md)

🎯 Objetivo 
Implementar e ajustar os motores de dados, regras de validação no tablet e interfaces do backoffice para garantir que a operação de manutenção no hangar rode em total conformidade com o escopo contratado, sem gerar desvios ou custos extras não faturados 

---

## Módulos (tarefa + o que foi feito em cada um)

| Módulo | Tarefa | Status | Arquivo |
|--------|--------|--------|---------|
| **01 — Backoffice** (Core PM & Engenharia de Dados) | Revisão do Gantt e Kanban (Syncfusion, edição direta, 1+ projetos) | ✅ Concluído | [`Modulo 01 - Backoffice`](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2001%20-%20Backoffice.md) |
| **02 — Operações e Chão de Fábrica** (Mobile / Tablet) | Auto-pausa de NRC, concorrência de timesheet, duplicidade, rename clockout, DRAFT amplo | ✅ 5/6 itens concluídos (RLS pendente) | [`Modulo 02 - Operacoes`](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2002%20-%20Operacoes.md) |
| **03 — Logística & Ferramentaria** (Almoxarifado) | Provedoria (Gated Process, bip de saída), Ferramentaria (check-in de avaria) | ⏳ Pendente | [`Modulo 03 - Logistica Ferramentaria`](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2003%20-%20Logistica%20Ferramentaria.md) |

---

## Pendências gerais (todas as pendências dos Módulos 02/03)
- [ ] RLS do Supervisor por Skill + múltiplas skills no colaborador (Módulo 02)
- [ ] Provedoria: monitoramento de liberação (Gated Process) (Módulo 03)
- [ ] Provedoria: bip de saída com lote (Módulo 03)
- [ ] Ferramentaria: check-in com condição de retorno + bloqueio por calibração (Módulo 03)
- [ ] Painel do mecânico: listagem de ferramentas com avaria (Módulo 03)

---

> O fluxo de **Execução, Aprovação e Duplo Check (RII)** — incluindo o log de implementação, o perfil REGISTRO, a tabela de apps e as regras de negócio por aplicação — está documentado na **Parte 01**: [`MRO-126 - Sumario Parte 01 - Fluxo RII.md`](MRO-126%20-%20Sumario%20Parte%2001%20-%20Fluxo%20RII.md)
