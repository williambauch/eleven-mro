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

