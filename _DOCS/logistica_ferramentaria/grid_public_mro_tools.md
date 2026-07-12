# Controle de Ferramentas (grid_public_mro_tools)

Módulo Logística e Ferramentaria — aplicação do tipo Grid.

Tela de cadastro e gestão de ferramentas. Controla informações como part number, número de série, localização, status e datas de calibração. Alertas visuais indicam ferramentas com calibração vencida ou próxima do vencimento.

## O que o usuário pode fazer

- Visualizar todas as ferramentas cadastradas com seus dados.
- Identificar rapidamente ferramentas com calibração vencida (fundo vermelho) ou próxima do vencimento (fundo amarelo, até 15 dias).
- Consultar status: disponível, em empréstimo, em calibração, danificada ou extraviada.
- Acessar o cadastro de cada ferramenta para edição.

## Comportamentos e regras importantes

- **Alertas de calibração**: ao carregar cada registro, o sistema compara a data de vencimento da calibração com a data atual:
  - Vencida: destaca as colunas `calibration_due_date` e `tool_id` com fundo vermelho e texto em negrito.
  - Vence em até 15 dias: destaca com fundo amarelo.
  - Em dia: sem destaque especial.
- O status da ferramenta é atualizado automaticamente pelo terminal de ferramentaria durante as operações de check-out e check-in.

## Dados envolvidos

Tabela `public.mro_tools`.
