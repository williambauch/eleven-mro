## Especificação Funcional: Plataforma MRO System

Projeto: Plataforma Centralizada de Gestão de Manutenção (MRO-PM)

Cliente: Digex Aircraft Maintenance

Objetivo: Substituição de sistemas legados (Primavera P6, DJAX ADM, planilhas) e implantação do conceito "Zero Papel" integrado ao ERP TOTVS Protheus.

## 1. Visão Geral e Arquitetura de Negócio

O sistema gerencia todo o ciclo de manutenção de aeronaves (Heavy Check), englobando o escopo de trabalho (Workscope), rotinas, não-rotinas, suprimentos e aprovações de clientes. O sistema foi construído sob rigorosos padrões de conformidade da ANAC.

- Zero Papel e Rastreabilidade: Eliminação de carimbos físicos. Implementação de assinaturas digitais com logs imutáveis (Timestamp, IP, Geolocalização) aderentes à Resolução 458 da ANAC.

- Matriz de Qualificação (Roster): Motor de conformidade que cruza em tempo real a licença do mecânico (CHT), a validade dos treinamentos (ex: NR-35, Cursos de Modelo) e as regras do cliente. Bloqueia sistemicamente a assinatura em caso de inconformidade.

- Geração de Documentos: Emissão automática em PDF dos cartões de tarefa (JIC, JMC, JEC) e arquivamento automatizado no SharePoint da Digex para o book final de manutenção (APRS).

## 2. Fluxo Operacional (End-to-End)

## Fase A: Setup e Planejamento

- 1. Cadastro e Templates: Criação da Estrutura Analítica do Projeto (EAP) com base em modelos predefinidos de aeronaves (ex: Boeing 737, A320, etc).

- 2. Importador P6: Ferramenta de leitura de arquivos CSV do Primavera P6 para montar automaticamente a árvore de tarefas (Projeto > Fase > Tarefa > Sub-tarefa).

- 3. Integração TOTVS Protheus (Materiais): Consulta via API em tempo real para validação de saldos de estoque (Part Numbers). Sistema avisa se a peça está Disponível, em Ruptura ou Em Trânsito.

- 4. Liberação para o Supervisor: O Planejamento/Programação atesta que a tarefa possui recursos e a move para o status RELEASED. O Supervisor a aloca aos mecânicos de acordo com a especialidade (Skill).

## Fase B: Execução no Chão de Fábrica

- 1. Ferramentaria e Estoque (Checkout/Check-in): Mecânico utiliza bipagem de crachá e código de barras. Bloqueio automático do empréstimo de ferramentas com calibração vencida ou caso o mecânico não possua tarefas ativas alocadas a ele.


- 2. Apontamento (Timesheet Nativo): O mecânico acessa o Kanban via tablet e inicia a execução (Clock-in). O sistema calcula o custo de Homem-Hora (HH) real gerado.

- 3. Impedimentos e Repasse (Clock-out): Em caso de gargalo (falta de acesso, quebra de ferramenta) ou fim de turno, o mecânico pausa a atividade. O sistema sinaliza o bloqueio no dashboard gerencial visualmente.

## Fase C: Over & Above (O&A) e Não-Rotinas

A descoberta de defeitos não previstos gera as Não-Rotinas (NRCs), que representam o principal gargalo logístico

e de faturamento.

- Abertura: Mecânico pausa a Rotina e gera um rascunho de NRC anexando fotos ou relatos.

- Análise: A Engenharia define os planos de ação e a Programação quantifica insumos e tempos (HH).

- Motor de Validação CAP: O sistema valida os custos orçados contra o teto contratual estipulado. Se estiver abaixo, é aprovado. Se exceder, a NRC entra no fluxo de O&A e aguarda aprovação direta no Portal do Cliente.

## Fase D: Ferramentaria (Estoque e Empréstimo)

- Bipagem e Autenticação: Módulo 100% testado e validado. O sistema obriga a bipagem do código de barras da ferramenta (ex: com crachá do mecânico, Part Number e Centro de Custo).

- Bloqueios de Segurança: O mecânico só consegue retirar uma ferramenta se estiver com um Clock-in ativo em alguma tarefa. O sistema também lê datas de calibração para bloquear ferramentas "sucata" ou vencidas.

## 3. Motor de Status (Máquina de Estados)

O banco de dados opera com duas camadas de controle de status para segregar a camada de aprovação/escritório da camada de execução/hangar.

## Tabela: mro_tasks (Status de Workflow e Documento)

| Status Code | Tipo Alvo | Descrição de Negócio |
| --- | --- | --- |
| PLANNED | Rotina | Tarefa importada do Primavera P6, aguardando liberação e análise de recursos. |
| DRAFT | NRC | Não-Rotina (NRC) recém-criada pelo mecânico via tablet. |


| Status Code | Tipo Alvo | Descrição de Negócio |
| --- | --- | --- |
| PENDING_ENG | NRC | Aguardando a Engenharia anexar o plano de ação, manuais ou desenhos. |
| PENDING_PROG | NRC | Aguardando a Programação definir as previsões de HH, materiais e custos. |
| PENDING_OA | NRC | Estourou o limite financeiro (CAP). Bloqueado aguardando o "De Acordo" do Cliente no portal. |
| COMMERCIAL_REVIEW |   | Exceção Cliente recusou o custo extra. Devolvido para renegociação comercial interna. |
| RELEASED | Ambos | Aprovada (financeiramente e com recursos em estoque). Liberada para o hangar. |
| COMPLETED | Ambos | Trabalho executado e artefato assinado digitalmente. |

## Tabela: mro_task_assignments (Status de Execução/Timesheet)

| Status Code | Ação (Mecânico/Supervisor) | Impacto |
| --- | --- | --- |
| NOT_STARTED | Alocada | Supervisor designou o recurso, porém o mecânico ainda não iniciou (sem HH correndo). |
| IN_PROGRESS | Clock-in | Serviço iniciado no tablet. Relógio de custo de HH ativado para esta tarefa. |
| PAUSED | Clock-out Comum | Interrupção por repasse de turno, descanso ou refeição. |
| BLOCKED | Clock-out Crítico | Impedimento forçado (falta de peça não previsa, quebra de ferramenta, restrição de acesso). |


| Status Code | Ação (Mecânico/Supervisor) | Impacto |
| --- | --- | --- |
| COMPLETED | Finalizada | Etapa concluída pelo mecânico. Requisição de assinatura ativada. |

## 4. Arquitetura de Integrações

- TOTVS Protheus (ERP):

- o APIs de Leitura: Busca de saldos de estoque e rastreio de pedidos de compra em tempo real.

- o APIs de Escrita: Geração automatizada de Ordens de Compra (PO) quando identificado cenário de ruptura, e estorno virtual de materiais não utilizados (sobra de peças).

- Microsoft SharePoint: Comunicação via API para arquivamento definitivo da documentação "Zero Papel" (Tally Sheets, Cartões assinados) a fim de compilar o book final do cliente e da ANAC.

- Microsoft Power BI: Exposição de tabelas e views estruturadas em banco para leitura do motor analítico de Dashboards da Digex (KPIs de Eficiência, Backlog).

## 5. Orientações Diretas para o Desenvolvedor

O núcleo base (Core PM, Painel do Mecânico, Validação de Timesheet, Engine O&A e Ferramentaria/Empréstimo) encontra-se em UAT (Testes de Aceitação de Key Users). Suas primeiras frentes de atuação com o time serão:

- 1. Compreender o paralelismo intrínseco das tabelas mro_taskse mro_task_assignments. Regra: Uma atribuição de Timesheet jamais pode ocorrer caso a tarefa base não possua o status RELEASED.

- 2. Dar polimento nas chamadas REST para o Protheus (gestão de exceptions e retornos de latência para evitar duplo empenho no ERP).

- 3. Validar as travas do Roster e Matriz de Qualificação durante o momento final da Assinatura Eletrônica (bypassar as travas da ANAC em ambiente de DEV).

- 5. O Maior Desafio Atual: Integração ERP (TOTVS Protheus) e Qualidade

Este é o ponto chave nessas fases finais:

- APIs com o Protheus (Camada de Materiais): O MRO se comunica via API com o Protheus Cloud para ler Saldos de Produtos em tempo real (Fim do ponto cego do estoque). Se não houver saldo,


o MRO já realiza a escrita (criação automática da Ordem de Compra - PO). Status: Em fase de testes

finais/ajustes.

- Matriz de Qualificação (Rosters): O motor de "Skill e Compliance" que cruza os dados do RH/Treinamentos. O sistema barra sistemicamente a assinatura de qualquer usuário cuja habilitação (CHT, curso do equipamento, NR-35, etc.) esteja vencida ou não seja aderente à Roster do cliente.

- Business Intelligence (BI) e SharePoint: Exposição das nossas tabelas via API para consumo no Power BI (KPIs de eficiência, backlog de manutenção) e arquivamento final da papelada gerada (Tally Sheet) para o SharePoint da Digex.

Onde estamos agora (Julho/2026): Estamos rodando UAT (Testes de Aceite de Usuário) com Key Users dos setores de Planejamento, Engenharia, Qualidade e Chão de Fábrica. O MVP e a Ferramentaria estão operacionais, e o foco agora é a consolidação fina do fluxo de Materiais via ERP e ajustes de UX baseados na

planilha de feedback dos treinamentos em andamento.
