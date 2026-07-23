# Criar "Pack JIC" para impressão

Resumo:
Criar relatório para imprimir os documentos relacionados a tarefa: JIC, JEC, JMC, Shift Turnover e Calibrated Tool.


Já foi criado o primeiro documento, JIC. Esse precisa de revisão para garantir todos os campos preenchidos nela.

Os outros precisa criar.

Fiz com base numa imagem de fundo, só posicionando os campos pelo Report do scriptcase.

Não precisa seguir da mesma forma, se achar melhor por alterar.

*Pack_JIC.pdf é o documento modelo que precisa ser gerado pelo sistema.*

as imagens foram criadas com base nesse modelo para servir de fundo para o relatório.

A app é chamada no grid das tarefas e no form tbm

A app chamado hoje é a pdf_pack_jic que fica na pasta Reports.





## Sumário das alterações implementadas - WILLIAM BAUCH

## `pdf_pack_jic`

### Movimentacao da aplicacao para pasta Reports

**`pdf_pack_jic`**
- Aplicacao **`pdf_pack_jic`** foi movida da pasta `Timesheet` (raiz) para a pasta `Reports`.
- Motivo: manter a consistencia de organizacao do projeto, agrupando os relatorios de impressao no local correto.
- Chamada permanece no grid de tarefas e no form de tarefas, apenas a localizacao da app foi alterada.

---

## `form_public_mro_task_resources`

### Corrigido SQL do schema — adicionado filtro task_id mantendo validacao original

**`sql/schema.sql`**
- SQL original ja continha `WHERE resource_code IN (SELECT DISTINCT skill_code FROM mro_skills)` para garantir que apenas resources com skill valido fossem retornados.
- Adicionado `AND task_id = [glo_task_id]` para filtrar corretamente pelos recursos da tarefa mestre, sem perder a validacao de skill existente.

---

## `reports/blank_pdf_pack_jic`

### Relatorio JIC (Job Instruction Card) — Pagina 1

**`events/onExecute`**
- Recebe o ID da tarefa e carrega todos os dados necessarios (task_code, model, ATA, matricula, cliente, executor, NRC, RII, deferimento, skills, ferramentas, materiais).

**`methods/mGerarPagina1JIC.php`**
- Exibe os dados da tarefa no layout do JIC: cabecalho (task_code, modelo, ATA, registro, cliente), secao de Mao de Obra (sub-tasks, skills, executor) e checkboards.
- Checkboxes sao exibidas condicionalmente conforme a tarefa: NR Aberta (is_nrc), IIO (is_rii), Diferimento aprovado, Ferramentas Calibraveis (quando ha ferramentas vinculadas).
- Campos sem preenchimento automatico (01/02 List if applicable, Revisao da Referencia, Aqui Assinatura do Cliente, Assinatura do IIO) sao mantidos em branco para preenchimento manual.

---

### Relatorio JEC (Job Equipment and Tool Card) — Pagina 2

**`methods/mGerarPagina2JEC.php`**
- Exibe cabecalho com dados da tarefa (task_code, modelo, ATA, registro, cliente).
- Lista as ferramentas associadas a tarefa em formato de tabela (P/N, descricao, quantidade).
- Exibe "Sem ferramentas para listar" quando a tarefa nao possui ferramentas vinculadas.

---

### Relatorio JMC (Job Material Card) — Pagina 3

**`methods/mGerarPagina3JMC.php`**
- Exibe cabecalho com dados da tarefa.
- Lista os materiais associados a tarefa em formato de tabela com 9 colunas (P/N, S/N, descricao, quantidade, unidade, etc.).
- Exibe "Sem materiais para listar" quando a tarefa nao possui materiais vinculados.

---

### Relatorio Shift Turnover — Pagina 4

**`methods/mGerarPagina4Shift.php`**
- Exibe os registros de passagem de turno (timesheet) da tarefa.
- Cada registro vira um card contendo: ACTION, Data da acao, MTR, SHIFT (preenchimento manual), STAMP (preenchimento manual), Data do STAMP.
- Quando ha mais de 6 registros, quebra automaticamente para uma nova pagina.

---

### Relatorio Calibrated Tool — Pagina 5

**`methods/mGerarPagina5Calibrated.php`**
- Exibe as ferramentas calibráveis vinculadas a tarefa.
- Cada ferramenta vira um card com: sub-tarefa, inspector (preenchimento manual), measure, incerteza/erro/desvio (preenchimento manual), proxima calibracao, equipment pass (preenchimento manual), P/N, S/N.
- Quando ha mais de 8 ferramentas, quebra automaticamente para uma nova pagina.

---

### Regras de negocio transversais

**`events/onExecute`** e todos os metodos
- O codigo de barras usa o numero do documento de origem (origin_document) quando preenchido; caso contrario, usa o codigo da tarefa (task_code).
- Habilidades (skills) dos recursos sao listadas no formato "Codigo -- Horas" (ex: "Solda -- 2.33h") e exibem "0h" quando o registro nao tem horas preenchidas.
- Quando um campo nao possui coluna correspondente no banco de dados, ele e impresso com indicacao visual de preenchimento manual.
- A geracao do relatorio e registrada em log (sc_log_add) para auditoria.

### Consultas de apoio

**`__Tarefas/MRO-118/queries_pack_jic.sql`**
- Arquivo com todas as 7 consultas SQL utilizadas no relatorio, organizadas por pagina.
- Inclui exemplos com task IDs validados no ambiente de producao (SSH).

---

## Migrations

### Adicionado blank_pdf_pack_jic na seguranca do sistema

**`__Tarefas/MRO-118/migrations/MRO-118_add_blank_pdf_pack_jic_sec_apps.sql`**
- Registra `blank_pdf_pack_jic` na tabela `sec_apps` como tipo `blank`.
- Concede acesso aos grupos: Administrador, COMERCIAL, COORDENADOR, ENGENHARIA, Group Default, MECANICO, PLANEJAMENTO, PROGRAMACAO e SUPERVISOR.
- Permissoes definidas: acesso, export e print ativos (mesmo modelo da `grid_public_mro_tasks`).
- Grupos CLIENTE, COMPRAS e FERRAMENTARIA nao receberam acesso.

