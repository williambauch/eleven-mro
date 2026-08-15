# Revisão Chão de Fábrica & Backoffice — Parte 02 — Módulo 03 (Logística & Ferramentaria)

> **Parte 02** — Escopo geral do Chão de Fábrica & Backoffice, separado por módulos de trabalho.
>
> - [Módulo 01 — Backoffice (Gantt e Kanban)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2001%20-%20Backoffice.md)
> - [Módulo 02 — Operações e Chão de Fábrica (Mobile / Tablet)](MRO-126%20-%20Sumario%20Parte%2002%20-%20Modulo%2002%20-%20Operacoes.md)
> - **Módulo 03 — Logística & Ferramentaria (Almoxarifado)** (este arquivo)

---

## Tarefa do Modulo 03 — Logística & Ferramentaria (Almoxarifado)

Provedoria — Tela de Monitoramento de Liberação (Gated Process):

Regra: Grid dedicada que lista apenas as JICs/tarefas que estejam com 100% dos materiais necessários liberados no estoque físico, permitindo ao planejador liberar as tarefas de forma segura para o hangar.

Provedoria — Fluxo de Recolhimento de Material (Bip de Saída):

Regra: No balcão da provedoria, o almoxarife deve realizar a baixa física das peças via leitura do código de barras, atrelando logicamente o lote do componente entregue ao código da JIC correspondente e ao ID do mecânico que realizou a retirada (Rastreabilidade As-Built).

Ferramentaria — Check-in Padrão (Bip de Retorno):

Regra: No ato da devolução da ferramenta no balcão, o atendente bipa o código do ativo e o sistema deve exigir obrigatoriamente a demarcação de seu estado físico de retorno (OK ou Com Avaria). Caso seja marcado como avariado, o sistema altera o status do ativo no banco para indisponível.

No painel do mecânico, criar listagem de ferramentas com avaria pra o ele preencher os formulários de avarias.

---

## Sumario das alteracoes implementadas

> Sem alteracoes implementadas neste modulo (pendente: provedoria e ferramentaria).

---

## Pendências do Modulo 03
- [ ] Provedoria: monitoramento de liberação (Gated Process)
- [ ] Provedoria: bip de saída com lote (Rastreabilidade As-Built)
- [ ] Ferramentaria: check-in com condição de retorno (OK/Avaria) + bloqueio por calibração vencida
- [ ] Painel do mecânico: listagem de ferramentas com avaria

---

## Critérios de Aceite (UAT) do Modulo 03
[ ] A ferramenta de ferramentaria exige a condição de retorno no check-in e bloqueia empréstimos se estiver com calibração vencida
