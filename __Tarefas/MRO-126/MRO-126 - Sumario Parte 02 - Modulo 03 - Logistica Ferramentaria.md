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

Informação POO: a disponibilidade dos materiais está na tabela mro_task_materials, committed_qty >= planned_qty isso pq pode ter saldo no estoque, mas compras que determina em qual projeto vai aplicar o produto (pode ter mais de um projeto na casa, se não tiver saldo disponível o suficiente para suprir todos os projetos, eles determinam qual a prioridade). esse campo é alimentado na importação da planilha de material pro sistema.

Provedoria — Fluxo de Recolhimento de Material (Bip de Saída):

Regra: No balcão da provedoria, o almoxarife deve realizar a baixa física das peças via leitura do código de barras, atrelando logicamente o lote do componente entregue ao código da JIC correspondente e ao ID do mecânico que realizou a retirada (Rastreabilidade As-Built).

Informação do POO: Fluxo tem que ser assim:
Bipa  a JIC
Bipa todos os materiais
botão para finalizar a separação.
Não precisa bipar o cracha....
para vc entender, o rapaz da provedoria vai olhar no sistema as JICs que estão disponíveis e os materiais que ele tem que separarar, ele pega no estoque e faz um pacote (coloca os materias dentro de um saco e prende a JIC impressa nesse saco).
esse saco fica disponível no angar numa prateleira, o mecanico quando for usar vai lá e pega.... não tem registro dele pegando o material. (é na confiança mesmo).
então tem que gravar só se foi separado os materiais na JIC...talvez só um campo a mais na tabela mro_task_materials com a data e hora da separação e o usuário que fez.


Ferramentaria — Check-in Padrão (Bip de Retorno):

Regra: No ato da devolução da ferramenta no balcão, o atendente bipa o código do ativo e o sistema deve exigir obrigatoriamente a demarcação de seu estado físico de retorno (OK ou Com Avaria). Caso seja marcado como avariado, o sistema altera o status do ativo no banco para indisponível.

No painel do mecânico, criar listagem de ferramentas com avaria pra o ele preencher os formulários de avarias.

---

## Sumario das alteracoes implementadas - 3. Módulo de Logística & Ferramentaria (Almoxarifado)

## `Almoxarifado/grid_provedoria_release` (Grid)

### Monitoramento de Liberacao (Gated Process)

**`sql/schema.sql`**
- Regra de negocio: lista apenas JICs/tarefas `PLANNED`/`NOT_STARTED` com 100% dos materiais bloqueantes disponiveis no estoque fisico (`stock_balance >= planned_qty`), permitindo ao planejador liberar com seguranca para o hangar.
- Filtro via subquery com `HAVING COUNT(*) FILTER (WHERE m.stock_balance < tm.planned_qty) = 0`; alias em todos os campos expostos.
- Abordagem: duplicata da `grid_public_mro_tasks` na IDE (herda campos, botoes, filtros e layout).

**`events/onRecord`**
- Span do `btn_predecessor` inclui `data-status='{status_code}'` (campo da linha) para o JS controlar o checkbox RUN.

**`events/onScriptInit`**
- Funcao `esconderCheckboxRun()` desabilita o checkbox RUN (esmaecido) quando o status nao esta em `PLANNING`/`NOT_STARTED`/`PLANNED`/`APPROVED` ou quando bloqueada por predecessora.

**Menu:** `item_50` em `Security/sec_menu/menu_tree.md` — Logística e Ferramentaria > Provedoria - Liberação de Materiais.

- Observacao de dados: os 509 materiais estao com `stock_balance = 0.00`, portanto a grid nasce vazia — comportamento correto da regra.
- Nota: a grid `grid_mro_material_release` (primeira abordagem) foi removida — substituida pela duplicata.

---

## `Almoxarifado/blank_mro_provedoria_bip` (Blank)

### Fluxo de Recolhimento de Material (Bip de Saida)

**`events/onExecute`**
- Regra de negocio (ajustada pelo POO): sem cracha nem lote individual — o almoxarife bipa a JIC, bipa os materiais e finaliza o pacote; grava apenas `separated_at`/`separated_by` no material.
- Acoes AJAX: `LOAD_JIC`, `LOAD_JIC_ID`, `SEPARAR`, `FINALIZAR`; elegibilidade igual ao Gated Process (`is_applied IS NOT TRUE` + `is_blocking_task` + `committed_qty >= planned_qty`).
- Trava de sessao (`[usr_login]` vazio bloqueia), botao Enter ao lado do campo material, materiais separados vao para o final da lista, aviso de JIC ja separada.
- Regra de negocio: o botao Finalizar Separação (Pacote) nao permite finalizar com pendentes — bloqueia com aviso.
- Diagnostico de JIC nao elegivel aprimorado: percorre TODAS as ocorrencias do mesmo `task_code` (em varios projetos) e consolida os motivos (bloqueio de material / status nao liberado / motivos mistos) em mensagem orientativa — antes usava apenas a primeira linha e podia mostrar motivo enganoso.

**`methods/mCarregarMateriais.php`**
- `mCarregarMateriais()` monta a lista (badges Separado/Pendente) e retorna `total`/`separados`/`tudo_separado`; logica auxiliar fora do corpo do `onExecute`.

**`sql/schema.sql`** (integracao com grid_provedoria_release)
- Colunas calculadas `total_separar`, `separados` e `pct_separado` (1 casa decimal) com `ORDER BY pct_separado DESC, task_code`.

**`events/onRecord`** (integracao com grid_provedoria_release)
- Campo virtual `ver_pct_seprado` exibe barra de progresso (verde 100% / amarelo parcial / cinza 0%), formatada pt-BR.

**Menu:** `item_51` em `Security/sec_menu/menu_tree.md` — Logística e Ferramentaria > Provedoria - Bip de Saída.

**Migration:** `__Tarefas/MRO-126/migrations/08_MRO-126_separacao_materiais.sql` — colunas `separated_at` (timestamp) e `separated_by` (varchar 50) + indice parcial.

---

## `ferramentaria/blank_mro_ferramentaria` (Blank)

### Check-in Padrao (Bip de Retorno)

**`events/onExecute`**
- Regra de negocio: no ato da devolucao, exige demarcacao obrigatoria do estado fisico (`OK`, `DANO` ou `PERDA`); em `DANO`/`PERDA` altera o status do ativo no banco para indisponivel.
- Check-in valida transacao ativa e bloqueia devolucao sem apontamento; `OK` volta para `DISPONIVEL`; `DANO`/`PERDA` => `FERRAMENTA DANIFICADA`/`FERRAMENTA EXTRAVIADA`.
- Bloqueio por calibracao vencida antes do checkout (seguranca ANAC).
- Resposta AJAX do CHECKIN DANO/PERDA devolve `trans_id`; URL do modal do relatorio com `v_trans` e `glo_report_id=0` (forca insert).

---

## `ferramentaria/form_mro_reports` (Form)

### Relatorio de Ocorrencia (Qualidade SGSO) — vinculo com transacao

**`events/onScriptInit`**
- Se `[glo_report_id]` vazio, vale `0` (abre o form em modo insert).

**`events/onLoad`**
- Regra de negocio: preenche automaticamente tipo (`TF-60-013` DANO / `TF-60-041` PERDA), ferramenta, mecanico, tarefa, projeto e `transaction_id` via `v_*`; oculta campos gerenciais.

**`events/onAfterInsert`**
- Seta `[glo_report_id]` com o `report_id` gerado e redireciona para `blank_mro_reports_resume`.

**`sql/schema.sql`**
- `WHERE report_id = [glo_report_id]` (0 = insert, valor = edicao).

---

## `ferramentaria/blank_mro_reports_resume` (Blank)

### Resumo da Ocorrencia

**`events/onExecute`**
- Regra de negocio: exibe o resumo do relatorio (ferramenta, profissional, JIC, data, status, descricao e `transaction_id`) a partir de `[glo_report_id]`.

---

## `ferramentaria/grid_mro_reports_mechanic` (Grid)

### Painel do Mecanico — Avarias (com ou sem relatorio)

**`sql/schema.sql`**
- Regra de negocio: lista todas as devolucoes `DANO`/`PERDA` sob responsabilidade do mecanico logado (`[usr_employee_id]`), tenham ou nao relatorio em `mro_tool_reports`.
- JOIN `mro_tool_transactions` + `mro_tools` + `mro_tasks` + `mro_projects` + `mro_tool_reports r ON r.transaction_id = tt.transaction_id`.
- Colunas calculadas com `concat()`: `tool_desc`, `task_desc`, `project_desc` (ignoram nulos).

**`events/onApplicationInit`**
- Insert/update/delete desabilitados no grid (consulta + acao via link).

**`events/onRecord`**
- Badge de status da devolucao (`DANO`/`PERDA`).

- Ligacao nativa do ScriptCase (Grid -> `form_mro_reports`): relatorio existente abre em edicao (update); inexistente abre em insercao (insert); nunca permite deletar.
- Vinculo transacao x relatorio (melhoria de modelagem): coluna `transaction_id` em `mro_tool_reports` 

**Menu:** `item_52` em `Security/sec_menu/menu_tree.md` — Producao e Manutencao > Avarias do Mecanico.

---

## `_Bibliotecas_Internas/mro_engine.php`

### Refatoracao — liberacao unica (Gated Process)

**`_Bibliotecas_Internas/mro_engine.php`**
- Regra de negocio: nova funcao `fn_liberar_task_para_execucao()` centraliza validacao de predecessora, critica de status, validacao de skill/labor, `UPDATE status_code='RELEASED'`, audit log e criacao de assignments por skill.
- `btn_liberar_para_execucao` da `grid_public_mro_tasks` virou thin wrapper chamando a funcao com origem `'PLANEJADOR'`; a duplicata da provedoria reutiliza a mesma funcao.

---

## Testes de validacao (MRO-126, Modulo 03)

### Teste A - Check-in DANO salvando transaction_id
- **Ferramenta:** F72923-1 (tool_id 8)
- **transaction_id gerado:** 47
- **report_id:** 10
- **Resultado:** aprovado — relatorio gravado com vinculo exato a transacao (via browser)

### Validacao no banco - backfill dos relatorios existentes
| Metrica | Valor |
|---------|-------|
| Relatorios vinculados a transacao | 6 de 6 |

### Teste B - Grid do mecanico (JOIN por transaction_id)
- **Transacoes com relatorio:** report_id preenchido (3, 4, 5, 7, 8, 9)
- **Transacoes sem relatorio:** report_id null (39, 43) — abrem em insert
- **Resultado:** aprovado

---

## Pendências do Modulo 03
- [x] Provedoria: monitoramento de liberação (Gated Process)
- [x] Provedoria: bip de saída com lote (Rastreabilidade As-Built) — ajustado pelo POO para separação de materiais (sem crachá/lote individual)
- [x] Ferramentaria: check-in com condição de retorno (OK/Avaria) + bloqueio por calibração vencida
- [x] Painel do mecânico: listagem de ferramentas com avaria

---

## Critérios de Aceite (UAT) do Modulo 03
[ ] A ferramenta de ferramentaria exige a condição de retorno no check-in e bloqueia empréstimos se estiver com calibração vencida
[ ] A grid da provedoria lista apenas JICs com 100% dos materiais bloqueantes disponíveis no estoque físico
