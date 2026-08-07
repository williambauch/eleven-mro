# Abertura de NRC (ctrl_abertura_nrc)

> ⚠️ **DESATIVADA (MRO-122)** — A abertura de NRC agora é feita pela aplicação [blank_abertura_nrc](blank_abertura_nrc.md) (abertura direta e automática, sem tela intermediária e sem digitação de dados). A `ctrl_abertura_nrc` permanece apenas como histórico/referência do fluxo anterior.

Módulo Produção e Manutenção — aplicação do tipo Form.

Formulário rápido de abertura de Não-Rotina (NRC) pelo mecânico no tablet. Permite relatar uma discrepância encontrada durante a execução e anexar fotos ou observações.

## O que o usuário pode fazer

- Abrir uma nova NRC a partir de uma tarefa em execução.
- Descrever a discrepância encontrada.
- Anexar fotos ou documentos como evidência.
- A NRC criada entra no fluxo de análise (Engenharia, Programação, O&A).

## Dados envolvidos

Tabela `public.mro_tasks` (com `is_nrc = true`).
