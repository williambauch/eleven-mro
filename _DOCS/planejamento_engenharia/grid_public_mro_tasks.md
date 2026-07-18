# Tarefas (grid_public_mro_tasks)

Módulo Planejamento e Engenharia — aplicação do tipo Grid.

Tela central de gerenciamento das tarefas de manutenção. Reúne tanto as rotinas (importadas do Primavera P6 ou cadastradas manualmente) quanto as não-rotinas (NRCs). É a partir desta grid que o planejamento libera as tarefas para execução no hangar.

## O que o usuário pode fazer

- Visualizar todas as tarefas do sistema com seus status, prazos, referências AMM, capítulos ATA e indicadores de bloqueio.
- Identificar visualmente se uma tarefa é rotina ou não-rotina (NRC) através de ícones na lista.
- Identificar bloqueios de material, ferramenta ou mão de obra através de ícones coloridos.
- Para cada tarefa com bloqueio, clicar no ícone correspondente para alternar o status do bloqueio (material, ferramenta ou mão de obra).
- Liberar uma tarefa do status PLANNED/NOT_STARTED para RELEASED usando o botão "Liberar para Execução".

## Comportamentos e regras importantes

- **Filtro por projeto**: Ao acessar a tela a partir de um projeto específico, a grid filtra automaticamente as tarefas daquele projeto usando a variável global `var_project_id` e a macro `sc_select_where(add)`.
- **Filtro não acumula mais**: A configuração "Salvar o estado da Consulta na sessão" foi desativada para evitar que filtros de projetos anteriores se acumulassem, gerando SQLs inválidos com múltiplas cláusulas `project_id`.
- **Ao liberar uma tarefa** (botão "Liberar para Execução"):
  - A tarefa precisa estar nos status PLANNING, NOT_STARTED ou PLANNED.
  - O sistema atualiza o status para RELEASED.
  - Em seguida, busca os recursos de mão de obra (LABOR) importados do P6 para a tarefa.
  - Para cada especialidade encontrada, cria um registro de alocação (`mro_task_assignments`) com status NOT_STARTED e as horas planejadas.
  - Caso o P6 não tenha enviado recursos para a tarefa, o sistema cria uma alocação genérica como fallback usando as horas estimadas.
- **Indicadores visuais**:
  - `btn_mat` (Material): ícone vermelho se bloqueado, cinza se OK.
  - `btn_fer` (Ferramenta): ícone roxo se bloqueado, cinza se OK.
  - `btn_mao` (Mão de Obra): ícone laranja se bloqueado, cinza se OK.
  - Cada bloqueio é alternado via Ajax diretamente no banco (coluna `is_blocked_material`, `is_blocked_tool` ou `is_blocked_labor`).
- **Mensagens**: Exibe alertas globais quando acionada a partir de outros processos (ex: execução de tarefa concluída), evitando repetição da mensagem.

## Para onde essa tela leva

As tarefas liberadas podem ser acessadas no Painel do Supervisor (`tabs_supervisor`) e no Painel do Mecânico (`grid_my_tasks`) para alocação e execução.

## Dados envolvidos

Tabelas `public.mro_tasks`, `public.mro_task_resources`, `public.mro_resources`, `public.mro_skills` e `public.mro_task_assignments`.
