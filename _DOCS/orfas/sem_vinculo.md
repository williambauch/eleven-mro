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

Ferramenta de importação de empenhos de compra. Permite carregar arquivos com dados de ordens de compra e empenhos para o sistema.

**Não foi possível posicionar** por ser uma aplicação do módulo de Compras (que não possui menu_tree.md próprio nem apps no menu principal). Ela pode ser associada ao fluxo de integração com o ERP Protheus.

---

## Blank Report Test (blank_report_test)

Módulo Reports — aplicação do tipo Blank.

Tela de teste para desenvolvimento de relatórios. Utilizada pela equipe técnica para validar novos formatos de relatório antes de disponibilizá-los aos usuários.

**Não foi possível posicionar** por ser uma aplicação exclusivamente interna/ de desenvolvimento, sem uso direto no fluxo de negócio.
