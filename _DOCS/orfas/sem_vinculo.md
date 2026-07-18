# Aplicações sem vínculo de menu identificado

As aplicações abaixo não puderam ser posicionadas em nenhum bloco de menu existente por tratarem-se de funcionalidades transversais ou utilitárias que não se encaixam diretamente em um módulo específico da árvore de navegação.

---

## Logs de Integração Sistêmica (form_public_sys_integration_logs)

Módulo transversal — aplicação do tipo Form.

Registro de logs das integrações do MRO com sistemas externos (TOTVS Protheus, SharePoint, etc.). Cada entrada armazena o tipo de integração, a requisição enviada, a resposta recebida, o status e o timestamp.

**Não foi possível posicionar** por ser uma aplicação transversal, utilizada por todos os módulos para auditoria de integrações.

---

## Grid de Logs de Integração (grid_public_sys_integration_logs)

Módulo transversal — aplicação do tipo Grid.

Grade de consulta dos logs de integração sistêmica. Permite auditar o histórico de chamadas API para o Protheus, SharePoint e outros sistemas.

**Não foi possível posicionar** por ser uma aplicação transversal, utilizada por todos os módulos para auditoria de integrações.

---

## Importação de Empenhos (control_import_empenhos)

Módulo Compras — aplicação do tipo Form.

Ferramenta de importação de empenhos de compra a partir de planilha Excel (.xlsx). Carrega os dados de ordem de compra, materiais, saldo empenhado e status de cada item, atualizando o estoque e o bloqueio das tarefas vinculadas.

**Não foi possível posicionar** por ser uma aplicação do módulo de Compras (que não possui menu_tree.md próprio nem apps no menu principal). Ela pode ser associada ao fluxo de integração com o ERP Protheus.

### Regras de negócio implementadas

- **Flag is_blocking_task**: Cada material possui uma flag `is_blocking_task` (booleano, default true) que define se a falta daquele item bloqueia a execução da tarefa. Materiais com `is_blocking_task = false` (ex: itens de consumo opcional) nunca bloqueiam a tarefa, mesmo sem status na planilha.
- **Saldo empenhado (committed_qty)**: Só é contabilizado se o material não bloqueia a tarefa (`!is_blocking_task`) ou se tem status OK na planilha. Caso contrário, o `committed_qty` é zerado.
- **Bloqueio automático**: Se um material bloqueante (`is_blocking_task = true`) estiver sem status (vazio, nulo ou "-") na planilha, o sistema marca a tarefa como bloqueada (`is_blocked_material = true`), impedindo o mecânico de iniciar o apontamento.
- **Atualização de custos**: O custo unitário e total do empenho é calculado e registrado. Se o material for NRC, dispara abertura de Ordem de Alteração (O&A).

### Fluxo de importação

1. PASSO A — Inicializa estrutura de tarefas alteradas
2. PASSO B — Lê `is_blocking_task` do banco para cada material
3. PASSO C — Decide se contabiliza saldo ou zera, com base na flag + status
4. PASSO D — Marca bloqueio se material bloqueante sem status
5. PASSO E — Atualiza `is_blocked_material` e `committed_qty` no banco

### Dados envolvidos

Tabelas `public.mro_materials`, `public.mro_task_materials`, `public.mro_tasks`.

---

## Blank Report Test (blank_report_test)

Módulo Reports — aplicação do tipo Blank.

Tela de teste para desenvolvimento de relatórios. Utilizada pela equipe técnica para validar novos formatos de relatório antes de disponibilizá-los aos usuários.

**Não foi possível posicionar** por ser uma aplicação exclusivamente interna/ de desenvolvimento, sem uso direto no fluxo de negócio.
