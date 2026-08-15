# 🔄 Fluxo de Execução, Aprovação e Duplo Check (RII)

Este documento descreve a lógica de negócio e a máquina de estados para o ciclo de vida das tarefas no **MRO System**, garantindo conformidade regulatória (ANAC) e segregação rígida de funções.

> **Nota do Gerente de Projeto:** e temos que criar um fluxo após o completd....
>
> montei esse esboço.
>
> temos que ter a aprovação no painel do supervisor, na aba Concluídos.
>
> Temos que criar mais uma aba no painel do supervisor com o nome Inspeção. O Inspetor é um supervisor que está marcado como inspetor tbm no cadastro de Colaboradores (Employed)... não sei se consegue criar uma regra para mostrar a aba de Inspeção somente para quem tiver esse campo de inspetor marcado, se conseguir ótimo, senão deixa visível mesmo.
>
> O inspetor segue a mesma regra do supervisor e só visualiza as tarefas do seu skill.

---

## 0. Task em IN_PROGRESS

Task é liberada (`btn_liberar_para_execucao` na grid, `btn_validar_prog_rotina` no form, ou auto-approve do `mro_engine`) → task fica `RELEASED` e são criados "slots" de assignment com `status_code = 'NOT_STARTED'` e `executed_by_employee_id = NULL`. Então, quando o supervisor atribuir o primeiro mecânico (`form_public_mro_task_assignments_planned`), o sistema deve atualizar o status da TASK para `IN_PROGRESS`.

---

## 1. Execução no Chão de Fábrica (Múltiplos Mecânicos)

* **Trabalho Simultâneo:** Uma tarefa pode ter múltiplos mecânicos alocados simultaneamente. Cada mecânico gerencia seu apontamento (*Clock-In* e *Clock-Out*) de forma individual pelo tablet.
* **Status Geral IN_PROGRESS ("Em Execução"):** Enquanto houver pelo menos um mecânico com o cronômetro ativo (*Clock-In* aberto), o status geral da tarefa no Kanban permanece em **"Em Execução"**.
* Nós iremos implementar agora na task o status `SUPSIG → PENDING_INSP1 → PENDING_INSP2 → COMPLETED`.

### ⏱️ Regra do Último Clock-Out

A transição de status da tarefa só é avaliada quando o **último mecânico ativo** realiza o seu *Clock-Out*:

```
[Mecânico realiza Clock-Out]
             │
             ▼
¿Existem outros mecânicos ativos na tarefa?
             ├── SIM ──► Mantém status da task "IN_PROGRESS" (Em Execução) — apenas encerra o relógio do mecânico que saiu (status assignment fica em: PENDING_HANDOVER)
             │
             └── NÃO (Último a sair) ──► Como o serviço foi encerrado?
                                             │
                                             ├── Passagem de Serviço (Incompleto) — Status Assignment: PENDING_HANDOVER ──► Status TASK: PENDING_PROG (Programação)
                                             │
                                             └── Finalizado (100% concluído) — Status Assignment: SUPSIG ──────► Status TASK: SUPSIG (Supervisor)

= Atualmente Motivo 6 coloca o assignment em SUPSIG e o timesheet em COMPLETED.
```

---

## 2. Máquina de Estados e Esteira de Aprovação

Assim que a tarefa é concluída pelo último mecânico e entra no status DA TASK **`SUPSIG`**, ela segue a esteira de validação em camadas abaixo:

```
 Status TASK: [SUPSIG] (Fila do Supervisor)
         │
         ▼
 ┌──────────────┐
 │  SUPERVISOR  │ ──► A tarefa exige inspeção?
 └──────────────┘
         │
         ├── NÃO (Tarefa Comum) ──────────────────────────────────┐
         │                                                        │
         └── SIM (Requires Rii ou Is Rii)                         │
                 │                                                │
                 ▼                                                │
          [PENDING_INSP1] (Fila do Inspetor 1)                    │
                 │                                                │
                 ▼                                                │
          ┌────────────┐                                          │
          │ INSPETOR 1 │ ──► Exige Duplo Check (Is Rii / RII)?   │
          └────────────┘                                          │
                 │                                                │
                 ├── NÃO (Inspeção Simples / Requires Rii) ──┐    │
                 │                                           │    │
                 └── SIM (Duplo Check / Is Rii)              │    │
                         │                                   │    │
                         ▼                                   │    │
                  [PENDING_INSP2] (Fila do Inspetor 2)       │    │
                         │                                   │    │
                         ▼                                   │    │
                  ┌────────────┐                             │    │
                  │ INSPETOR 2 │                             │    │
                  └────────────┘                             │    │
                         │                                   │    │
                         ▼                                   ▼    ▼
                   [COMPLETED] ◄──────────────────────────────────┘
                         │
                         ▼
             (Fila do Time de Registros)
```

---

## 3. Detalhamento das Etapas e Regras de Transição

### Etapa 1: Validação do Supervisor (Aprovação de Escopo)

* **Ação:** O Supervisor de Hangar revisa as horas apontadas e o escopo do trabalho entregue.
* **Lógica de Roteamento:**

  * Se a tarefa **não exigir** nenhum tipo de inspeção: O status é alterado diretamente para **`COMPLETED`** (Tarefa encerrada e liberada para o Time de Registros).
  * Se a tarefa **exigir** inspeção simples ou dupla (`Requires Rii = Y` ou `Is Rii = Y`): O status avança para **`PENDING_INSP1`** (Fila de Inspeção Técnica).

### Etapa 2: Primeira Inspeção Técnica (Inspetor 1)

* **Ação:** Um inspetor qualificado revisa fisicamente o trabalho, valida a conformidade e assina eletronicamente.
* **Lógica de Roteamento:**

  * Se a tarefa exigir apenas **Inspeção Simples (`Requires Rii = Y`)**: O status muda para **`COMPLETED`** (Tarefa encerrada e liberada para o Time de Registros).
  * Se a tarefa exigir **Duplo Check (`Is Rii = Y`)**: O status avança para **`PENDING_INSP2`** (Aguardando o segundo inspetor). O sistema grava o ID do Inspetor 1 para auditoria de segregação de funções.

### Etapa 3: Segunda Inspeção Técnica / Duplo Check (Inspetor 2)

* **Ação:** Um segundo inspetor habilitado realiza a verificação final independente e assina digitalmente.
* **Regra de Ouro (Segregação de Funções):**

  * **O Inspetor 2 não pode ser a mesma pessoa que assinou como Inspetor 1.** O sistema valida os tokens de assinatura e bloqueia o processo caso o mesmo usuário tente assinar ambas as etapas.
* **Lógica de Roteamento:**

  * Após a assinatura eletrônica do Inspetor 2, o status da tarefa é atualizado para **`COMPLETED`** (Tarefa encerrada e liberada para o Time de Registros).

> 📌 **Decisão do Gerente de Projeto (13/08/2026):** a **assinatura digital** (tokens/hash criptográfico) fica **para o futuro**. Hoje o sistema deve apenas **registrar quem aprovou** (Inspetor 1/2) — isso já é feito gravando `INSPECTOR_1`/`INSPECTOR_2` no `mro_task_history` com `user_login` + `action_date`. Quando a assinatura digital for implementada, ela complementará esse registro (sem alterar a estrutura de log atual).

### Etapa 4: Auditoria e Encerramento (Time de Registros)

* **Acesso Restrito:** O painel do Time de Registros exibe **exclusivamente** tarefas que atingiram o status **`COMPLETED`**.
* **Ação:** O time realiza a conferência final dos metadados das assinaturas digitais e gera o Job Card eletrônico ("Zero Papel"), arquivando o pacote de forma segura e auditável para a ANAC.

> 📌 **Decisão do Gerente de Projeto (13/08/2026):** o painel do Time de Registros será **separado do menu do supervisor** (não fica nas abas do `menu_supervisor`). Será criado um **novo perfil (grupo) chamado "Registro"** com acesso exclusivo a essa tela. A implementação fica **para o futuro** — hoje o foco é o fluxo de aprovação/inspeção (SUPSIG → PENDING_INSP1 → PENDING_INSP2 → COMPLETED).

> ✅ **IMPLANTADO (13/08/2026):** perfil **REGISTRO** criado (grupo 13, usuário `registro`/`Registro@321`) com o painel `grid_public_mro_task_registro`. O painel exibe **TODAS as tasks `COMPLETED`** (auditoria geral do encerramento — o filtro por histórico de inspeção RII foi descartado após revisão). Item de menu **Auditoria > Painel de Registros** no `sec_menu`. Detalhes na migration `06_MRO-126_perfil_registro.sql`.

---

### 📋 Tabela Resumida de Transição de Status

|Status Origem|Ação Gatilho|Responsável|Condição de Negócio|Status Destino|
|-|-|-|-|-|
|**Em Execução**|Último Clock-Out|Último Mecânico|Marcou como "Incompleto"|**`PENDING_PROG`**|
|**Em Execução**|Último Clock-Out|Último Mecânico|Marcou como "Finalizado"|**`SUPSIG`**|
|**`SUPSIG`**|Aprovar Tarefa|Supervisor|Sem inspeções requeridas|**`COMPLETED`**|
|**`SUPSIG`**|Aprovar Tarefa|Supervisor|Exige Inspeção (`Requires Rii` ou `Is Rii`)|**`PENDING_INSP1`**|
|**`PENDING_INSP1`**|Assinatura Técnica|Inspetor 1|Inspeção Simples (`Requires Rii` apenas)|**`COMPLETED`**|
|**`PENDING_INSP1`**|Assinatura Técnica|Inspetor 1|Duplo Check Requerido (`Is Rii`)|**`PENDING_INSP2`**|
|**`PENDING_INSP2`**|Assinatura Técnica|Inspetor 2|Usuário diferente do Inspetor 1|**`COMPLETED`**|
