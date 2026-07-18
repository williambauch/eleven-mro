# Cadastro de Materiais (form_public_mro_materials)

Módulo Logística e Ferramentaria — aplicação do tipo Form.

Formulário de cadastro e edição de materiais e peças de reposição. Permite registrar part number, descrição, unidade de medida, indicador de consumível e a flag de bloqueio operacional.

## O que o usuário pode fazer

- Cadastrar novos materiais no catálogo.
- Editar materiais existentes.
- Definir se a falta do material bloqueia ou não a execução da tarefa.

## Comportamentos e regras importantes

- **Flag is_blocking_task**: Campo do tipo select com duas opções:
  - **Bloqueia (1)** — Padrão. A falta deste material impede o mecânico de iniciar o apontamento e zera o saldo empenhado.
  - **Nao Bloqueia (0)** — A falta não bloqueia a tarefa. Use para materiais de consumo opcional ou substituíveis.
- O campo possui tooltip explicativo (tippy) orientando o usuário sobre o impacto de cada opção.
- A flag é consultada pelo motor de importação de empenhos para decidir se o material bloqueia a tarefa.

## Dados envolvidos

Tabela `public.mro_materials`.
