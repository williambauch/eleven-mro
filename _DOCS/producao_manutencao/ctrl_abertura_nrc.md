# Abertura de NRC (ctrl_abertura_nrc)

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário rápido de abertura de Não-Rotina (NRC) pelo mecânico no tablet. Permite relatar uma discrepância encontrada durante a execução e anexar fotos ou observações.

## O que o usuário pode fazer

- Abrir uma nova NRC a partir de uma tarefa em execução.
- Descrever a discrepância encontrada.
- Anexar fotos ou documentos como evidência.
- A NRC criada entra no fluxo de análise (Engenharia, Programação, O&A).

## Dados envolvidos

Tabela `public.mro_tasks` (com `is_nrc = true`).
