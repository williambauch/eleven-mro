# Consolidar as interfaces de Rotina (Task) e Não-Rotina (NRC) em uma aplicação única, adotando o layout padrão da Task e implementando renderização condicional para os botões e grids exclusivos de NRC.

Resumo:

Consolidar as interfaces de Rotina (Task) e Não-Rotina (NRC) em uma aplicação única, adotando o layout padrão da Task e implementando renderização condicional para os botões e grids exclusivos de NRC.

Descrição:

Para reduzir a duplicidade de código e melhorar a usabilidade do sistema para os mecânicos e inspetores, precisamos unificar as aplicações public_mro_tasks e public_mro_nrc. A interface resultante deve usar o layout atual da Task como base. A visualização das ações deve ser inteligente: os botões e recursos que pertencem estritamente às Não-Rotinas (NRC) devem ser ocultados em tarefas normais e exibidos apenas quando o registro carregado for uma NRC. Os grids de ambas as telas também precisam ser mesclados em um único componente dinâmico.

**Sobre a criação de Não-Rotinas (NRC):** Atualmente NRCs só são criadas através da aplicação `ctrl_abertura_nrc`, que pode ser chamada a partir de:
- `form_public_mro_task_assignments` (Form) — rota do mecânico via assignment ativa
- `grid_public_mro_tasks` (Grid) — rota de backoffice via task_id_origem

> ⚠️ Por enquanto **não vamos alterar** `ctrl_abertura_nrc`. Ela permanece como está.

**Regra de Negócio — Criação de NRCs:**

1. **Fluxo principal (Mecânico em campo):** O mecânico abre NRCs exclusivamente pelo **clock-in/clock-out** — ou seja, enquanto está executando uma tarefa (assignment ativa) e encontra alguma discrepância/disconformidade. Ele usa o botão no `form_public_mro_task_assignments` para disparar o `ctrl_abertura_nrc`, que detecta automaticamente a assignment em andamento.

2. **Fluxo secundário (Qualquer perfil):** Qualquer usuário (incluindo o próprio mecânico) também pode abrir uma **NRC em cima de outra NRC** — ou seja, o `parent_task_id` pode apontar tanto para uma Rotina Padrão quanto para uma Não-Rotina existente. Isso é feito pela rota de backoffice na `grid_public_mro_tasks`, selecionando a task de origem manualmente.

**Resumo:** Mecânico cria NRC via assignment ativa (campo). Qualquer perfil pode criar NRC a partir de outra NRC (backoffice).

Critérios de Aceite (Definition of Done):

Layout Base Consolidado: A interface unificada deve preservar a identidade visual e estrutural da atual aplicação form_public_mro_tasks.

Renderização Condicional (Botões): A lógica de frontend/backend deve validar o tipo de registro aberto. Se for uma NRC, os botões específicos de ações da NRC devem ficar visíveis e funcionais. Se for uma Task comum, estes botões devem permanecer ocultos.

Unificação dos Grids: Mesclar os data grids de listagem. O novo grid deve ser capaz de exibir tanto Tasks quanto NRCs, com os devidos filtros para que o usuário consiga distinguir facilmente os registros na tela.

Refatoração de Links e Rotas: Atualizar todos os atalhos, menus e redirecionamentos do sistema que antes apontavam para form_public_mro_nrc para que passem a abrir a nova aplicação unificada (passando o ID/Tipo por parâmetro, se necessário).

Desativação da App Legada: Arquivar/desativar a aplicação form_public_mro_nrc antiga para evitar uso indevido após a subida desta versão.

Informações Técnicas (Para o Desenvolvedor):

Aplicações Impactadas: form_public_mro_tasks e grid_public_mro_tasks (será mantida e expandida) e form_public_mro_nrc e grid_public_mro_nrc (será descontinuada).

Lógica de View: Utilizar o campo `is_nrc` da tabela `mro_tasks` para controlar a visibilidade dos botões do header e ações do grid/form. Se `is_nrc = true`, o registro é uma Não-Rotina (NRC). Se `is_nrc = false` (ou NULL), é uma Task/Rotina comum. Este é o campo definidor do tipo.

**Campo `task_type` (tabela `mro_task_types`) — DESCONTINUADO:** O campo `task_type` vindo da tabela `mro_task_types` em `form_public_mro_tasks` será descontinuado. **Não usar** esse campo para nenhuma lógica nova.

Atenção aos Privilégios: Garantir que a unificação não quebre as permissões de acesso (ACL). Quem tem permissão apenas para ver Task não deve conseguir aprovar NRC indevidamente pela nova tela mista.

**Forms** — Grupos de acesso:
- `form_public_mro_tasks`: Administrador, COMERCIAL, COORDENADOR, ENGENHARIA, Group Default, MECANICO, PLANEJAMENTO, PROGRAMACAO
- `form_public_mro_nrc`: Administrador, COMERCIAL, COORDENADOR, ENGENHARIA, Group Default, MECANICO, PLANEJAMENTO, PROGRAMACAO

**Grids** — Grupos de acesso:
- `grid_public_mro_nrc`: Administrador, COMERCIAL, COORDENADOR, ENGENHARIA, Group Default, MECANICO, PLANEJAMENTO, PROGRAMACAO, SUPERVISOR
- `grid_public_mro_tasks`: Administrador, COMERCIAL, COORDENADOR, ENGENHARIA, Group Default, **ADD MECANICO** ✅, PLANEJAMENTO, PROGRAMACAO, SUPERVISOR


## Sumário das alterações implementadas - WILLIAM BAUCH

### `Security/sec_form_sec_apps` — Campo `grupos_acesso` (label)

**Motivo:** Facilitar a consulta de quais grupos de acesso uma aplicação possui diretamente no formulário de edição de aplicações, sem precisar abrir o banco.

**O que foi feito:**
- Adicionado campo virtual label `grupos_acesso` (varchar) no formulário
- Criado evento `05_onLoad/onLoad.scriptcase` com consulta SQL que busca os grupos vinculados via `sec_groups_apps` × `sec_groups`
- O campo é **read-only** e exibe os grupos separados por vírgula
- Em caso de erro ou nenhum grupo vinculado, exibe mensagem descritiva

**Arquivo:** `events/onLoad`

---

## `form_public_mro_tasks`

### Campos de deferment para NRC

**Motivo:** Unificar o comportamento com `form_public_mro_nrc`: os campos de adiamento (`deferment_status`, `deferment_reason`) devem existir no form, mas só aparecer quando a tarefa for uma Não-Rotina.

**`events/onLoad`**
- Adicionada regra: os campos `deferment_status` e `deferment_reason` só são exibidos se a tarefa for NRC (`is_nrc = true`)
- Se a tarefa for NRC e `deferment_status` estiver ativo, `deferment_reason` também é exibido

**`events_ajax/deferment_status_onClick`**
- Copiado do `form_public_mro_nrc`: ao clicar em `deferment_status`, mostra ou esconde o campo `deferment_reason`

### Conversão de skill_code e root_task_id vazios para null

**Motivo:** Os campos `skill_code` (FK `fk_tasks_skill`) e `root_task_id` (FK `mro_tasks_root_task_id_fkey`) possuem chaves estrangeiras. Quando ficam vazios no form, o ScriptCase envia `''` ou `0` no SQL, violando as FKs. A correção converte esses valores para `NULL`.

**Arquivos criados:**
- `events/onBeforeInsert` — converte `{skill_code}` vazio e `{root_task_id}` = 0 para `null` antes de inserir
- `events/onBeforeUpdate` — converte `{skill_code}` vazio e `{root_task_id}` = 0 para `null` antes de atualizar

```php
if (empty({skill_code})) {
    {skill_code} = 'null';
}
if (empty({root_task_id}) || {root_task_id} == 0) {
    {root_task_id} = 'null';
}
```

### Skill padrão no mestre-detalhe de recursos

**Motivo:** Quando uma tarefa é criada com `skill_code` definido, o sistema deve incluir automaticamente essa skill como um recurso no mestre-detalhe (`mro_task_resources`). O select de `skill_code` foi alterado para simples (não múltiplo), e skills adicionais podem ser cadastradas manualmente no relacionamento de recursos.

**`events/onAfterInsert`**
- Insere o `skill_code` da tarefa como `resource_code` em `mro_task_resources`
- Verifica duplicata antes de inserir (mesma task + mesmo resource_code)
- Após inserir, executa `sc_commit_trans()` e `sc_redir` para recarregar o mestre-detalhe

**`events/onAfterUpdate`**
- Mesma lógica do onAfterInsert: insere o `skill_code` em `mro_task_resources` se ainda não existir
- Após inserir, também executa `sc_commit_trans()` e `sc_redir` — garante que o detail atualize mesmo quando a skill é alterada via edição

### Campo `created_by` com valor padrão `[usr_login]`

**Motivo:** O campo `created_by` não era preenchido ao criar uma nova Rotina no form, ficando vazio no banco. Isso impedia o filtro por usuário (que usa `created_by = [usr_login]`) de funcionar corretamente.

**O que foi feito:**
- Configurado na interface do ScriptCase o valor inicial do campo `{created_by}` como `[usr_login]` no insert
- Campo marcado como **escondido** (hidden) no formulário — o usuário não vê nem edita, mas o valor é gravado automaticamente

### Bloco financeiro (Over & Above)

**Motivo:** Exibir campos financeiros de O&A para perfis não-mecânicos quando a NRC atinge os status `PENDING_OA`, `COMMERCIAL_REVIEW` ou `RELEASED`.

**`events/onLoad`**
- Adicionado `sc_block_display('bloco_financeiro', 'off')` no início — bloco oculto para tasks comuns
- Ativado (`'on'`) dentro das fases 5, 6 e 7 do workflow NRC para perfis que não sejam MECANICO
- Campos do bloco: `{is_oa}`, `{ao_hours}`, `{oa_material_cost}`

### Remoção do campo Task_Type

**Motivo:** Campo `{Task_Type}` (tabela `mro_task_types`) está em desuso no projeto. O campo definidor agora é `is_nrc`.

- Campo `{Task_Type}` removido do `form_public_mro_tasks` — não aparece mais no formulário
- A coluna e a tabela `mro_task_types` permanecem no banco para compatibilidade com dados existentes

### Regras de readonly e obrigatoriedade para NRC

**Motivo:** Garantir a integridade dos dados de Não-Rotinas: campos de identificação da tarefa (`project_id`, `task_code`) não podem ser alterados após criar uma NRC, e campos essenciais (`instruction_text`, `task_name`, `parent_task_id`) devem ser preenchidos obrigatoriamente.

**`events/onLoad`**:
- `parent_task_id` e `origin_document` → ocultos para rotinas comuns, exibidos apenas quando `is_nrc = true`
- `project_id` → read-only quando `is_nrc = true`
- `task_code` → read-only quando `is_nrc = true`
- `parent_task_id` → read-only quando `is_nrc = true`
- **FASE 1 (DRAFT):** Antes liberava os botões se o usuário fosse do grupo MECANICO. Alterado para liberar **somente se o usuário logado for o criador** (`[usr_login] == {created_by}`), independentemente do grupo — "DRAFT somente quem criou pode alterar"
- **FASES 2 e 4 (PENDING_ENG / PENDING_PROG):** Corrigido `sc_field_readonly({description}, 'on')` → `sc_field_readonly({instruction_text}, 'on')`. O campo `description` não existe em `mro_tasks`; o nome correto da coluna de relato é `instruction_text`

**`events/onValidate`** — obrigatórios quando `is_nrc = true` (novo evento):
- `parent_task_id` → obrigatório (também é read-only, pois é definido na criação)
- `instruction_text` → obrigatório
- `task_name` → obrigatório

### Card informativo de status (`mExibirCardStatus`)

**Motivo:** Fornecer ao usuário um feedback visual claro sobre o estágio atual da NRC e qual perfil/grupo é responsável pela próxima ação, sem precisar abrir outras telas ou consultar documentação.

**Arquivo criado:** `methods/mExibirCardStatus.scriptcase`

**Lógica:**
- Se a tarefa não for NRC (`is_nrc` vazio), o método retorna sem exibir nada
- Busca `label_ptbr` e `kanban_color` da tabela `mro_sys_status WHERE module = 'TASKS'` pelo `status_code` atual
- Se o status não for encontrado no banco, usa fallback: label = próprio `status_code`, cor = cinza (`#6c757d`)
- Mapeia cada status para o grupo responsável via `switch` (ex: `PENDING_PROG` → "Programação", `PENDING_OA` → "Comercial / Cliente", etc.)
- Renderiza um card centralizado (largura 60%) com borda esquerda colorida, exibindo:
  - Label do status em PT-BR num badge arredondado com a cor do kanban
  - Texto "Próxima ação depende de:" + nome do grupo responsável

**`events/onLoad`**
- Adicionada chamada `mExibirCardStatus();` logo após a tradução dos grupos de acesso
- O card só aparece para NRC (validação interna no método)

### Indicador visual de tipo (`{routine_type}`)

**`events/onLoad`**
- Campo `{routine_type}` preenchido dinamicamente com `"Rotina Padrão"` ou `"Não Rotina (RN)"` conforme o valor de `{is_nrc}`
- Exibe visualmente no topo do formulário qual o tipo do registro atual, auxiliando na identificação rápida

---

## `ctrl_abertura_nrc`

### Título, campo Descrição Detalhada e redirect

- Título da aplicação alterado para **"Abertura de NR"**
- Removida a limitação de 20 caracteres no campo `{descricao_detalhada}`, alterado para **2000 caracteres**
- Redirect após criar NRC alterado de `form_public_mro_nrc` para `form_public_mro_tasks`

---

## `grid_public_mro_tasks`

### Badge de status dinâmico via `mro_sys_status`

**`events/onRecord`**
- O badge de status (`{status_badge}`) agora é **dinâmico**: busca `label_ptbr`, `kanban_color` e `icon` da tabela `mro_sys_status WHERE module = 'TASKS'` para o `status_code` atual
- Define cor do texto automaticamente: preto se fundo claro, branco se fundo escuro
- **Fallback:** se o status não for encontrado na tabela, exibe o próprio `status_code` com estilo neutro (cinza)


### Filtro MECANICO incorporado ao ScriptInit

**Motivo:** Unificar o comportamento da `grid_public_mro_tasks` com o que já existia na `grid_public_mro_nrc`: usuários com perfil exclusivo MECANICO devem ver apenas as tarefas que criaram.

**`events/onScriptInit`**
- Incorporada a mesma lógica de tradução de grupos (group_id → nomes) que já existia na `grid_public_mro_nrc`
- Adicionado filtro automático: se o usuário logado tiver **apenas** o perfil MECANICO, a grid filtra por `created_by = [usr_login]` — ele vê só as próprias tarefas
- Filtro de `project_id` (existente) mantido e combinado num único `sc_select_where` com o filtro MECANICO, respeitando `sc_where_atual`
- Apenas 1 chamada de `sc_select_where(add)` é executada por init, com ambas as condições concatenadas

---

## Migrations

### Acesso MECANICO a grid_public_mro_tasks

**Motivo:** O grupo MECANICO tem acesso a `grid_public_mro_nrc` mas não a `grid_public_mro_tasks`. Antes de desativar a grid legada, é necessário conceder essa permissão.

**Arquivo:** `migrations/01_MRO-117_grant_mecanico_acesso_grid_public_mro_tasks.sql`

```sql
UPDATE sec_groups_apps
SET priv_access = 'Y',
    priv_insert = '',
    priv_update = '',
    priv_delete = '',
    priv_export = 'Y',
    priv_print = 'Y'
WHERE app_name = 'grid_public_mro_tasks'
  AND group_id = 3;
```

### Unificação dos status em `mro_sys_status`

**Motivo:** Antes existiam modules separados (NRC e TASKS) com status duplicados. Com a unificação das interfaces, agora tudo fica sob `module = 'TASKS'`, incluindo os status do fluxo NRC que faltavam.

**Arquivo:** `migrations/02_MRO-117_unificar_status_task.sql`

- Unificados 22 status no module `TASKS` com label, cor e ícone
- Usa `INSERT ... ON CONFLICT DO UPDATE` — se o `status_code` já existia em outro module, atualiza para `TASKS`; se não, insere
- Foram ignorados da lista os status que aparecem em SELECT distinct status_code FROM mro_tasks order by status_code ; mas não tem rotina que crie: `PENDING`, `PENDING_ENGINEERING`, `PENDING_OA_APPROVAL`

---

**Arquivo:** `migrations/03_MRO-117_grant_acesso_form_public_mro_task_resources.sql`

### 03. Garantir permissões do form_public_mro_task_resources

**Motivo:** O `form_public_mro_task_resources` (usado para alocar recursos às tarefas) estava sem nenhum grupo vinculado no `sec_groups_apps`. Com a consolidação Task/NRC, esse form precisa ter as mesmas permissões do `form_public_mro_tasks` para que os perfis corretos consigam acessá-lo.

```sql
INSERT INTO sec_groups_apps (app_name, group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print)
SELECT 'form_public_mro_task_resources', group_id, priv_access, priv_insert, priv_update, priv_delete, priv_export, priv_print
FROM sec_groups_apps
WHERE app_name = 'form_public_mro_tasks'
ON CONFLICT (app_name, group_id) DO UPDATE SET
    priv_access = EXCLUDED.priv_access,
    priv_insert = EXCLUDED.priv_insert,
    priv_update = EXCLUDED.priv_update,
    priv_delete = EXCLUDED.priv_delete,
    priv_export = EXCLUDED.priv_export,
    priv_print = EXCLUDED.priv_print;
```

---

## Desativação da app legada `form_public_mro_nrc` / `grid_public_mro_nrc`

### Ações realizadas

- Removido item do menu `Produção e Manutenção > Gestão de Não Rotinas (NRC) (grid_public_mro_nrc)` do `Security/sec_menu/menu_tree.md`

---

## `_Bibliotecas_Internas/mro_engine.php`

### Aprovação automática quando NRC não estoura o CAP

**Motivo:** Quando a Programação clica em "Validar" (`btn_validar_prog`), o motor O&A roda. Se a NRC estourou o CAP do contrato, ela avança pra `PENDING_OA` — funcionando corretamente. O problema era quando **não estourava o CAP**: o motor rodava mas não mudava o status, deixando a NRC travada em `PENDING_PROG` sem nenhum feedback pro usuário (identificado durante teste com a task 26336, que só avançou após aumentar `estimated_hours` de 10h para 55h para forçar o estouro).

**Solução implementada:** Se o motor O&A rodar e **não houver estouro de CAP**, a NRC vai direto pra `RELEASED` (aprovação automática), já que está dentro do contrato e não precisa de aprovação do cliente.

**`_Bibliotecas_Internas/mro_engine.php`** — função `fn_calcular_oa_nrc`
- Adicionado bloco `if ($status_atual == 'PENDING_PROG')` dentro da seção "Não estourou o CAP"
- Quando `PENDING_PROG` e `$hh_oa_final == 0`: status vai para `RELEASED`
- Registra `AUTO_APPROVE` em `mro_nrc_approval_log` com `user_login = 'mro_engine'` e descrição explicativa
- Backup do arquivo original anexo: `_Bibliotecas_Internas/backup_18072026_mro_engine.php`

### Registro em `mro_nrc_approval_log` para todas as transições de status

**Motivo:** Durante a revisão do código identificou-se que nem todas as mudanças de status geradas pelo motor O&A estavam sendo registradas. As transições do bloco "Estourou o CAP" vindas de `PENDING_ENG`/`PENDING_PROG`/`PENDING_COORD` mudavam o status para `PENDING_OA` sem deixar rastro no histórico.

**Consolidação:** Decidiu-se usar apenas `mro_nrc_approval_log` (tabela já existente) para todos os registros de auditoria, eliminando a tabela `mro_task_history`. O motor registra com `user_login = 'mro_engine'` para distinguir eventos automáticos de ações manuais.

**`_Bibliotecas_Internas/mro_engine.php`** — função `fn_calcular_oa_nrc`

**Alteração no `elseif ($status_atual != 'DRAFT')`:**
- Adicionado `$msg` com descrição do evento
- Adicionado `INSERT INTO mro_nrc_approval_log` com `action_taken = 'OA_REVISION'` e `user_login = 'mro_engine'`

**Rotas do `mro_engine.php` que agora registram em `mro_nrc_approval_log`:**

| Situação | Transição | `action_taken` |
|----------|-----------|:--------------:|
| Já rodando (`RELEASED`/`APPROVED`/`COMPLETED`) + estourou CAP | → `PENDING_OA` | `OA_REVISION` |
| Pendente (`PENDING_ENG`/`PENDING_PROG`/`PENDING_COORD`) + estourou CAP | → `PENDING_OA` | `OA_REVISION` |
| `PENDING_PROG` + dentro do CAP | → `RELEASED` | `AUTO_APPROVE` |
| `PENDING_OA` + dentro do CAP | → `RELEASED` | `AUTO_APPROVE` |


