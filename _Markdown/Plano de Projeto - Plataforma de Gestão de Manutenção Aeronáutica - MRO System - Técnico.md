## Plano de Projeto: Plataforma de Gestão de Manutenção Aeronáutica (MRO)

## 1.0 Introdução e Visão Geral

## 1.1 Propósito do Documento

Este Plano de Projeto constitui o guia oficial para o desenvolvimento, implantação e gestão do projeto da Plataforma de Gestão de Manutenção Aeronáutica (MRO-PM). Este documento serve como referência central para todas as partes interessadas, incluindo as equipes da DIGEX AIRCRAFT MAINTENANCE LTDA (Contratante) e da Eleven Consultoria (Contratada), garantindo o alinhamento de expectativas e uma execução controlada. Nele estão definidos o escopo detalhado, os objetivos estratégicos, o cronograma de entregas, o orçamento, a estrutura de governança e as responsabilidades de cada equipe.

O objetivo é fornecer uma base clara e consensual para a tomada de decisões, o monitoramento do progresso e a gestão de quaisquer mudanças ao longo do ciclo de vida do projeto.

## 1.2 Visão Geral e Estratégica do Projeto

A visão estratégica do projeto MRO-PM é modernizar e centralizar a gestão de manutenção de aeronaves na Digex, substituindo sistemas legados, como o Primavera P6, e eliminando processos manuais baseados em planilhas e comunicações não rastreáveis. A iniciativa atende a uma necessidade crítica de integrar e automatizar o fluxo de trabalho, desde o planejamento inicial até a execução final, logística e arquivamento documental.

O projeto visa criar uma solução web centralizada e moderna, capaz de gerenciar o ciclo de vida completo da manutenção. A plataforma integrará o planejamento (Estrutura Analítica do Projeto - EAP), o controle de execução (Timesheet), a gestão de discrepâncias (Over & Above), a logística de materiais (integração com o ERP Protheus) e o arquivamento de documentos técnicos (integração com o SharePoint). A implementação desta solução proporcionará visibilidade total da operação, maior rastreabilidade para fins de conformidade e auditoria, e agilidade decisória, resultando em maior eficiência operacional e redução do tempo de solo das aeronaves.

## 1.3 Partes Interessadas (Stakeholders)

A execução bem-sucedida deste projeto depende da colaboração estreita entre as seguintes partes interessadas:

| Organização |   | Papel no Projeto Principais Representantes |
| --- | --- | --- |
| DIGEX AIRCRAFT MAINTENANCE LTDA | Cliente (Contratante) | Paj Sang Ki (CEO), Diego Ramirez (Adm/Fin/TI), Volnei Melo (Gerente PMO), Francisco Torres (Gerente Engenharia), Alessandra Del Guerra (Gestora SGSO), Marcelo Aragão (Gerente Qualidade), Carlos Araújo (Gerente Produção), André Santos (Coord. TI) |
| ELEVEN CONSULTORIA | Fornecedor (Contratada) | Rodrigo Souza (Responsável pelo Projeto) |


| Estaleiro Mauá | Provedor de Infraestrutura (Servidores) | Marcelo Cohen (Gerente de TI), Guilherme (Gerente de Infraestrutura) |
| --- | --- | --- |

A identificação clara desses stakeholders é fundamental para definir formalmente o escopo e as expectativas de cada parte envolvida no projeto.

## 2.0 Escopo do Projeto

## 2.1 Objetivos e Benefícios Estratégicos

A definição clara dos objetivos de negócio é crucial para orientar o desenvolvimento e medir o sucesso do projeto. Esta seção articula o valor estratégico que a plataforma MRO-PM agregará à operação da Digex, transformando processos e otimizando resultados.

Os principais objetivos estratégicos do projeto são:

- Reduzir o "Ground Time" das aeronaves, por meio da aceleração drástica do ciclo de aprovação de custos de serviços não rotineiros (Over & Above - O&A).

- Aumentar a Eficiência Operacional, eliminando controles paralelos em planilhas, comunicações não rastreáveis (e-mails, telefone) e retrabalhos decorrentes de informações descentralizadas.

- Garantir Rastreabilidade e Conformidade (Compliance), criando um registro digital centralizado e auditável de todas as etapas da manutenção, desde a aprovação de custos até a execução, inspeção e uso de ferramentas.

- Centralizar a Inteligência de Negócios, fornecendo dados estruturados para análises estratégicas via Power BI e dashboards nativos, permitindo a identificação de gargalos, custos recorrentes e oportunidades de melhoria.

- Modernizar a Operação, substituindo sistemas legados por uma plataforma web integrada e avançando em direção a um processo com "zero papel" através de assinaturas digitais e arquivamento eletrônico.

## 2.2 Módulos e Funcionalidades da Plataforma

A plataforma MRO-PM será composta por um conjunto de módulos integrados, projetados para cobrir todas as fases do processo de manutenção.

| Módulo |   | Descrição do Propósito Principais Funcionalidades |
| --- | --- | --- |
| Gestão de Projetos (Core PM) | como ferramenta central de planejamento. | Substituir o Primavera P6 Gestão de EAP (Projeto > Fase > Tarefa)<br>Gráfico de Gantt e Kanban interativos<br>Templates de "Projetos Base"<br>Importador de Pacote de Manutenção (Excel) |


| Operações e Documentos | Digitalizar e controlar a execução das tarefas no chão de fábrica. | Geração automática de documentos (JIC/JMC/JEC) em PDF<br>Gestão de tipos de contrato (para cálculo de GAP) |
| --- | --- | --- |
| Gestão de Over & Above (O&A) | automatizar o fluxo de aprovação de custos. | Registro de Cartões de Serviço Não Rotineiros Gerenciar discrepâncias e (NRCs)<br>Orçamentação de HH e materiais<br>Aplicação de regras de negócio (GAP)<br>Workflow de aprovação do cliente (automático ou externo) |
| Comunicação Automatizada | Agilizar a comunicação com o cliente para aprovações de O&A. | Notificações via E-mail e WhatsApp (Integração PlugMessage)<br>Link seguro para página de aprovação/rejeição externa |
| Acompanhamento e Execução | Fornecer visibilidade sobre o status da produção e garantir a qualidade. | Visão em tempo real do status de JCI/NRC<br>Assinatura digital para inspetores de qualidade |
| Timesheet | Rastrear o tempo de mão de manutenção. | Interface de "Clock-in / Clock-out" para de obra aplicado às tarefas mecânicos<br>Associação automática de horas às tarefas da EAP |
| Ferramentaria | Garantir o controle, rastreabilidade e conformidade das ferramentas. | Controle de estoque e calibração (com alertas/bloqueios)<br>Gestão de empréstimo (checkout/check-in)<br>Bipagem de código de barras para controle automático |
| Integração ERP (Protheus) | Conectar a operação de manutenção à logística e ao financeiro. | API de Leitura: Consulta de saldo de materiais em tempo real<br>API de Escrita: Criação automática de Ordens de Compra (PO) |
| Integração de Dados arquivamento de (BI & Docs) | Automatizar o documentos e a extração de dados para análise. | Arquivamento automático de documentos no SharePoint<br>API segura para exposição de dados ao Power BI |
| Dashboards e Relatórios | Fornecer painéis de controle e relatórios para gestão e auditoria. | KPIs operacionais e financeiros (Ex: NRCs pendentes, tempo médio de aprovação)<br>Relatórios de faturamento, auditoria e defeitos recorrentes |

## 2.3 Principais Entregáveis do Projeto

Ao final do ciclo de desenvolvimento e implantação, a Eleven Consultoria entregará à Digex os seguintes

artefatos:


Aircraft Maintenance Software Aviation Management

- 1. Plataforma MRO-PM: O sistema web completo, com todos os módulos e funcionalidades descritos no escopo, implantado no ambiente de produção e plenamente funcional para uso da Digex.

- 2. Código-Fonte: A entrega do código-fonte completo da aplicação desenvolvida na plataforma Scriptcase, garantindo a titularidade patrimonial do software para a Digex.

- 3. Documentação Técnica: A documentação completa da arquitetura do software, do modelo de banco de dados e das especificações das APIs de integração, permitindo futuras manutenções e evoluções.

## 2.4 Limites e Exclusões do Escopo

Para garantir o alinhamento de expectativas e evitar o desvio de escopo ("scope creep"), é fundamental definir explicitamente o que não está incluído neste projeto. Os seguintes itens estão fora do escopo contratado:

- Novas Funcionalidades: Quaisquer solicitações de funcionalidades, módulos ou alterações de regras de negócio que não estejam descritas na Proposta Técnica 2025-92 serão tratadas como mudanças de escopo, devendo ser analisadas e orçadas à parte.

- Licenciamento de Software de Terceiros: O projeto não inclui os custos de aquisição ou manutenção de licenças de softwares de terceiros, como TOTVS Protheus, Microsoft SharePoint (Office 365), PlugMessage (WhatsApp) ou Microsoft Power BI. A Digex é responsável por garantir a disponibilidade e o licenciamento adequado desses sistemas.

- Fornecimento de Hardware: A aquisição, instalação ou manutenção de qualquer equipamento físico, como servidores, estações de trabalho, coletores de dados ou dispositivos de rede, não faz parte deste escopo.

A definição clara do escopo e de suas exclusões permite que o projeto avance do "o quê" será entregue para

"como" será estruturado e executado.

## 3.0 Estrutura e Fases do Projeto

## 3.1 Metodologia de Desenvolvimento

A metodologia adotada para este projeto será o Desenvolvimento Ágil, caracterizado por um ciclo de entregas incrementais e validações contínuas. Esta abordagem foi escolhida para proporcionar flexibilidade, permitindo que a solução seja refinada e adaptada às necessidades da Digex ao longo do desenvolvimento. Em vez de uma única entrega ao final do projeto, o valor será entregue em fases, permitindo que os usuários-chave validem as funcionalidades e forneçam feedback constante, garantindo um produto final mais alinhado às expectativas e necessidades operacionais.

## 3.2 Estrutura de Divisão do Trabalho (WBS) por Fases

O projeto foi estruturado em 7 (sete) fases principais, cada uma representando um marco de entrega com um conjunto específico de funcionalidades e objetivos. Esta divisão permite um gerenciamento mais eficaz do cronograma e dos custos, além de mitigar riscos ao entregar valor de forma incremental.

## 1. Fase 0: Fundação (Plan & Design)


- 2. Fase 1: Entrega MVP (O&A + Ponte P6)

- 3. Fase 2: Entrega Timesheet Nativo

- 4. Fase 3: Entrega Core PM (Substitui P6)

- 5. Fase 4: Entrega Módulo de Ferramentaria

- 6. Fase 5: Entrega Integração Protheus

- 7. Fase 6: Entrega BI & Docs

- 3.3 Detalhamento das Fases

- 3.3.1 Fase 0: Fundação (Plan & Design)

- Objetivo: Mapear os processos de negócio, definir a arquitetura da solução e validar o design das interfaces antes do início do desenvolvimento.

- Duração Estimada: Aprox. 3 Semanas.

- Principais Entregas da Fase: Documentação de Arquitetura de Software e Banco de Dados; Especificação de APIs; Backlog priorizado; Protótipo navegável validado com os stakeholders.

- 3.3.2 Fase 1: Entrega MVP (O&A + Ponte P6)

- Objetivo: Entregar rapidamente o núcleo de valor do sistema (MVP - Minimum Viable Product), automatizando o fluxo de O&A e criando uma ponte com o Primavera P6 para manter a operação em andamento.

- Duração Estimada: Aprox. 5 Semanas.

- Principais Entregas da Fase: Módulo de Gestão de O&A (incluindo aprovação via E- mail/WhatsApp); Geração de documentos (JIC/JMC/JEC); Ferramenta de importação e exportação para o P6.

- 3.3.3 Fase 2: Entrega Timesheet Nativo

- Objetivo: Substituir processos manuais de apontamento de horas, integrando um controle nativo de Timesheet à plataforma.

- Duração Estimada: Aprox. 2 Semanas.

- Principais Entregas da Fase: Interface de "Clock-in / Clock-out"; Lógica para associação automática de horas às tarefas (JICs).

## 3.3.4 Fase 3: Entrega Core PM (Substitui P6)

- Objetivo: Tornar a plataforma MRO-PM a fonte central de planejamento, aposentando o uso do Primavera P6.


- Duração Estimada: Aprox. 4 Semanas.

- Principais Entregas da Fase: Módulo de gestão nativa de EAP; Componentes interativos de Gráfico de Gantt e Kanban; Funcionalidade de "Projeto Base" (templates).

## 3.3.5 Fase 4: Entrega Módulo de Ferramentaria

- Objetivo: Implementar o controle completo de ferramentas para garantir rastreabilidade, conformidade e gestão de calibração.

- Duração Estimada: Aprox. 3 Semanas.

- Principais Entregas da Fase: Módulos de Controle de Estoque, Gestão de Empréstimo e Gestão de Calibração (com alertas e bloqueios).

## 3.3.6 Fase 5: Entrega Integração Protheus

- Objetivo: Conectar a plataforma ao ERP Protheus para automatizar a consulta de materiais e a criação de ordens de compra.

- Duração Estimada: Aprox. 4 Semanas.

- Principais Entregas da Fase: API de Leitura de Saldo de Material; API de Escrita de Ordem de Compra (PO).

## 3.3.7 Fase 6: Entrega BI & Docs

- Objetivo: Finalizar a plataforma com as integrações para análise de dados (BI) e arquivamento documental automático.

- Duração Estimada: Aprox. 4 Semanas.

- Principais Entregas da Fase: API de Integração com SharePoint; API de Exposição de Dados para Power BI; Dashboards nativos; Encerramento do projeto.

A conclusão bem-sucedida de cada fase prepara o terreno para a próxima, culminando na entrega de uma solução completa e robusta conforme o cronograma detalhado a seguir.

## 4.0 Cronograma do Projeto

## 4.1 Linha do Tempo Geral por Fases

O projeto tem uma duração total estimada de aproximadamente 34 semanas, com início planejado para novembro de 2025 e previsão de término em julho de 2026. A tabela abaixo apresenta a visão macro do cronograma, alinhada com as fases de entrega definidas.

| Fase de Entrega | Duração (Semanas de Trabalho Efetivo) | Previsão de Início | Previsão de Fim |
| --- | --- | --- | --- |


| 0. Fundação (Plan & Design) | 2,67 | 05/jan/26 | 23/jan/26 |
| --- | --- | --- | --- |
| 1. Entrega MVP (O&A + Ponte P6) 4,63 |   | 26/jan/26 | 06/mar/26 |
| 2. Entrega Timesheet Nativo | 1,93 | 09/mar/26 | 20/mar/26 |
| 3. Entrega Core PM (Substitui P6) | 3,73 | 23/mar/26 | 17/abr/26 |
| 4. Entrega Módulo de Ferramentaria 2,4 |   | 20/abr/26 | 06/mai/26 |
| 5. Entrega Integração Protheus | 3,8 | 07/mai/26 | 02/jun/26 |
| 6. Entrega BI & Docs | 4,07 | 03/jun/26 | 01/jul/26 |
| TOTAL GERAL (Trabalho Efetivo) | 23,23 |   |   |

## 4.2 Cronograma Detalhado de Tarefas

Esta seção detalha as atividades específicas dentro de cada fase, fornecendo uma visão granular para o gerenciamento e acompanhamento do projeto. O cronograma abaixo reflete o plano de trabalho que será gerenciado na ferramenta Jira.

| Fase (Épico) | Atividade (Tarefa) | Data de Início | Data de Fim | Estimativa (Horas) | Principais Atividades e Entregáveis |
| --- | --- | --- | --- | --- | --- |
| Fase 0: Fundação (Plan Planejamento e & Design) | Semanas 1-2: Análise Global |   | 03/Nov/25 14/Nov/25 80h |   | Atividades: Kick-off, mapeamento de processos e regras de negócio.<br>Entregável: Requisitos iniciais documentados. |
|   | Semanas 3-4: Arquitetura e Backlog |   | 17/Nov/25 28/Nov/25 80h |   | Atividades: Arquitetura de Software e BD, especificação de APIs, criação do Backlog no Jira.<br>Entregável: Arquitetura definida e Backlog priorizado. |
| Fase 1: Entrega MVP (O&A + Ponte P6) | Semanas 5-6: Design MVP e Geração de JICs |   | 01/Dez/25 12/Dez/25 80h |   | Atividades: Design de telas de O&A, Aprovação Cliente e Import/Export P6.<br>Entregável: Wireframes e protótipo inicial do MVP. |
|   | Semana 7: Validação Design MVP |   | 15/Dez/25 19/Dez/25 40h |   | Atividades: Sessões de validação do protótipo com stakeholders.<br>Entregável: Protótipo MVP validado. |


|   | Semanas 8-9: Dev MVP - Core O&A e JICs |   | 05/Jan/26 16/Jan/26 80h |   | Atividades: Desenvolvimento do Módulo O&A e Gerador de JICs.<br>Entregável: Módulo O&A e Gerador de JICs (v1) funcionais. |
| --- | --- | --- | --- | --- | --- |
|   | Semanas 10-11: Dev MVP - Ponte P6 e Horas |   | 19/Jan/26 27/Jan/26 46h |   | Atividades: Desenvolvimento de importadores (P6, Horas) e exportador (P6).<br>Entregável: Funcionalidades de integração com P6 prontas para QA. |
| Fase 2: Entrega Timesheet Nativo | Semanas 12-13: Dev Timesheet Nativo |   | 28/Jan/26 10/Fev/26 80h |   | Atividades: Desenvolvimento da interface Clock-in/out e lógica de associação de horas.<br>Entregável: Módulo Timesheet pronto para QA. |
|   | Semana 14: Testes e Implantação Timesheet |   | 11/Fev/26 17/Fev/26 36h |   | Atividades: Testes QA (16h), UAT (8h), implantação e treinamento (12h).<br>Entregável: Módulo Timesheet em produção. |
| Fase 3: Entrega Core PM (Substitui P6) | Semanas 15-17: Dev Core PM - EAP e Templates |   | 18/Fev/26 16/Mar/26 120h |   | Atividades: Desenvolvimento da Gestão Nativa de EAP, lógica de Projeto Base e Importador Excel.<br>Entregável: Módulos de EAP e Templates prontos para QA. |
|   | Semanas 18-19: Dev Core PM - Gantt e Kanban |   | 17/Mar/26 27/Mar/26 80h |   | Atividades: Desenvolvimento dos componentes interativos de Gantt e Kanban.<br>Entregável: Visualizações de Gantt e Kanban prontas para QA. |
|   | Semana 20: Testes e Implantação Core PM |   | 30/Mar/26 03/Abr/26 24h |   | Atividades: Testes QA (16h), UAT (8h), implantação e treinamento (16h).<br>Entregável: Core PM em produção (P6 desativado). |
| Fase 4: Entrega Ferramentaria | Semanas 21-22: Dev Ferramentaria - Estoque e Empréstimo |   | 06/Abr/26 17/Abr/26 80h |   | Atividades: Desenvolvimento dos módulos de Controle de Estoque e Gestão de Empréstimo.<br>Entregável: Módulos de Estoque e Empréstimo prontos para QA. |


|   | Semana 23: Dev Ferramentaria - Calibração |   | 20/Abr/26 23/Abr/26 32h |   | Atividades: Desenvolvimento do módulo de Gestão de Calibração.<br>Entregável: Módulo de Calibração pronto para QA. |
| --- | --- | --- | --- | --- | --- |
|   | Semana 24: Testes e Implantação Ferramentaria |   | 27/Abr/26 30/Abr/26 32h |   | Atividades: Testes QA (16h), UAT (8h), implantação e treinamento (8h).<br>Entregável: Módulo Ferramentaria em produção. |
| Fase 5: Entrega Protheus | Semanas 25-27: Dev Protheus - API Leitura Saldo |   | 01/Mai/26 21/Mai/26 120h |   | Atividades: Mapeamento, desenvolvimento e testes unitários da API de Leitura de Saldo.<br>Entregável: API de Saldo pronta para testes de integração. |
|   | Semanas 28-29: Dev Protheus - API Escrita PO |   | 22/Mai/26 02/Jun/26 80h |   | Atividades: Mapeamento, desenvolvimento e testes unitários da API de Escrita de PO.<br>Entregável: API de PO pronta para testes de integração. |
|   | Semana 30: Testes e Implantação Protheus |   | 03/Jun/26 09/Jun/26 28h |   | Atividades: Testes de Integração E2E (16h), UAT (8h), implantação (4h).<br>Entregável: Integração Protheus em produção. |
| Fase 6: Entrega BI & Docs | Semanas 31-32: API SharePoint | Dev BI & Docs - 10/Jun/26 23/Jun/26 80h |   |   | Atividades: Desenvolvimento da integração com SharePoint para arquivamento.<br>Entregável: Integração SharePoint pronta para QA. |
|   | Semanas 33-34: API Power BI | Dev BI & Docs - 24/Jun/26 07/Jul/26 80h |   |   | Atividades: Desenvolvimento da API de exposição de dados para Power BI.<br>Entregável: API de BI pronta para QA. |
|   | Semana 35: Dev BI & Docs - Dashboards Nativos |   | 08/Jul/26 14/Jul/26 40h |   | Atividades: Desenvolvimento dos dashboards internos.<br>Entregável: Dashboards prontos para QA. |
|   | Semanas 36-37: Testes e |   | 15/Jul/26 28/Jul/26 44h |   | Atividades: Testes QA (24h), UAT (8h), implantação e encerramento |


|   | Implantação BI & Docs |   |   |   | (12h).<br>Entregável: Projeto completo em produção. |
| --- | --- | --- | --- | --- | --- |

Este detalhamento do cronograma orientará a alocação de recursos e o monitoramento do progresso, sendo fundamental para a análise financeira do projeto.

## 5.0 Equipes, Papéis e Responsabilidades

## 5.1 Matriz de Responsabilidades (Key Users)

O sucesso da implantação da plataforma MRO-PM depende fundamentalmente do envolvimento dos Key Users (Usuários-Chave) da Digex. Eles são os especialistas nos processos de negócio e terão o papel crucial de validar as funcionalidades desenvolvidas em cada fase, garantir que a solução atenda às necessidades operacionais e auxiliar na disseminação do conhecimento e no treinamento de suas equipes. A tabela a seguir mapeia os Key Users designados e os gestores responsáveis pela revisão de cada área/módulo principal.

| Módulo/Área | Key User Designado | Setor | Email | Gestor para Revisão |
| --- | --- | --- | --- | --- |
| Gestão de Projetos (Core PM) | Danilo Barletta |   | Planejamento danilo.barletta@digex.com.br | Francisco Torres (Engenharia) |
| Operações e Documentos | Débora Menezes |   | Programação debora.menezes@digex.com.br | Bruno Azevedo (Programação)<br>Alessandra Del Guerra (SGSO) |
| Gestão de O&A / Guilherme Comunicação | Moreira | PMO | guilherme.moreira@digex.com.br Volnei Melo (PMO) |   |
| Acompanhamento Celso e Execução | Carvalho | Qualidade | celso.carvalho@digex.com.br | Marcelo Aragão (Qualidade)<br>Alessandra Del Guerra (SGSO) |
| Timesheet | Guilherme Moreira |   | Planejamento guilherme.moreira@digex.com.br Bruno Azevedo (Programação) |   |
| Ferramentaria e Estoque | Nicolas Minami | Ferramentaria e Estoque | nicolas.minami@digex.com.br Carlos Araújo (Produção) |   |
| Integração ERP (Protheus) | Nicolas Minami | Ferramentaria e Estoque | nicolas.minami@digex.com.br Diego Ramirez (Adm/Fin/TI) |   |


| Integração de Dados (BI & Docs) | Leonel Santos | BI & Programação | leonel.santos@digex.com.br | Bruno Azevedo (Programação) |
| --- | --- | --- | --- | --- |
| Dashboards e Relatórios | Regiane Braga | BI & Programação | regiane.braga@digex.com.br | Bruno Azevedo (Programação) |

## 6.2 Obrigações da Contratada (Eleven Consultoria)

5onforme estabelecido em contrato, as principais responsabilidades da Eleven Consultoria são:

- Executar o desenvolvimento da plataforma MRO-PM utilizando a tecnologia acordada (Scriptcase) e garantir a funcionalidade das integrações com TOTVS Protheus, SharePoint e PlugMessage.

- Disponibilizar uma equipe técnica especializada e um coordenador de projeto para gerenciar todas as fases do desenvolvimento e atuar como ponto de contato principal.

- Manter sigilo absoluto sobre todas as informações, dados e regras de negócio da Digex, tanto durante a vigência do contrato quanto por um período de 5 anos após sua extinção.

- Entregar, ao final do projeto e após a quitação dos valores, o código-fonte completo da aplicação e toda a documentação técnica correspondente.

## 5.3 Obrigações da Contratante (Digex)

Para o sucesso do projeto, a Digex se compromete com as seguintes obrigações:

- Fornecer todas as informações, regras de negócio, dados e acessos necessários aos sistemas legados (Protheus, SharePoint, etc.) para a equipe da Eleven Consultoria.

- Designar os Key Users e garantir sua disponibilidade para participar de workshops, validar as rotinas e homologar as entregas nos prazos acordados, a fim de não impactar o cronograma do projeto.

- Efetuar os pagamentos referentes ao desenvolvimento e ao suporte contínuo nas datas estipuladas no contrato.

- Custear todas as despesas de viagem (aéreo, translado, hospedagem e alimentação) em caso de solicitação de atendimento presencial por parte da Eleven Consultoria.

Com as responsabilidades definidas, a governança do projeto pode ser estabelecida para garantir uma comunicação fluida e um controle eficaz.

## 6.0 Governança e Gestão do Projeto

## 6.1 Ferramentas e Comunicação

Para garantir a transparência, o alinhamento e a eficiência na execução, o projeto será governado por um conjunto de ferramentas e processos de comunicação bem definidos.


- Gestão de Tarefas: Todo o cronograma e o andamento das atividades serão gerenciados na plataforma Jira. A Digex terá acesso para acompanhar o progresso em tempo real, garantindo total visibilidade sobre o status de cada entrega.

- Cadência de Reuniões: Serão realizadas reuniões de acompanhamento semanais ou quinzenais entre os responsáveis pelo projeto da Digex e da Eleven Consultoria. O objetivo desses encontros é monitorar o progresso em relação ao cronograma, discutir impedimentos, tomar decisões e planejar as próximas atividades.

- Canais de Comunicação: A comunicação formal do projeto (solicitações, aprovações, etc.) será centralizada por meio de canais oficiais a serem definidos, enquanto a comunicação do dia a dia será facilitada por grupos de trabalho e ferramentas de colaboração.

## 6.2 Gestão de Mudanças de Escopo

O processo para gerenciar solicitações de mudança de escopo está formalmente definido para garantir que o projeto permaneça dentro do prazo e do orçamento planejados. Conforme a Cláusula Quinta do contrato, qualquer solicitação de nova funcionalidade ou alteração significativa que não conste na Proposta Técnica 2025-92 deverá ser formalizada por escrito pela Digex. A Eleven Consultoria analisará o impacto da solicitação no cronograma e no custo, apresentando um orçamento à parte. Somente após a aprovação formal da Digex, a mudança será incorporada ao plano do projeto.

## 6.3 Gestão de Riscos Iniciais

Durante a reunião de kick-off, foram identificados alguns riscos iniciais que serão monitorados e gerenciados ativamente ao longo do projeto.

- Risco de Performance: A localização dos servidores da nova plataforma no Estaleiro Mauá, enquanto outros sistemas da Digex estão em um data center em São Paulo, pode gerar impactos na performance da comunicação entre os sistemas.

- o Ação Mitigatória: Realização de testes de performance e carga durante o desenvolvimento para validar a arquitetura e garantir a estabilidade e a velocidade da solução.

- Risco de Adoção: A implementação de um novo sistema centralizado implicará em uma mudança cultural e de processos, podendo haver resistência por parte dos usuários.

- o Ação Mitigatória: Envolvimento contínuo dos Key Users em todas as fases de validação e design, garantindo que a ferramenta seja intuitiva e atenda às necessidades reais da operação. A área de SGSO também apoiará na gestão da mudança.

- Risco Regulatório: É imperativo que os novos processos digitais, incluindo assinaturas eletrônicas e arquivamento de documentos, estejam em total conformidade com as regulamentações da ANAC.

- o Ação Mitigatória: A área de SGSO, representada por Alessandra Del Guerra, participará ativamente da revisão e validação dos fluxos de trabalho e processos documentais para garantir a conformidade regulatória.


A gestão proativa desses riscos, aliada a uma governança clara, pavimenta o caminho para a aprovação e o início

formal dos trabalhos.
