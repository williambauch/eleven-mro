# Diagrama de Fluxo — Operadores do MRO System

```mermaid
flowchart TB
    subgraph GF["👤 Gestão de Frota"]
        direction TB
        A1["📋 Cadastrar Aeronave<br/><i>grid_public_mro_aircraft</i>"]
        A2["📋 Criar Projeto/Check<br/><i>grid_public_mro_projects</i>"]
        A3["📋 Gerenciar Lotes O&A<br/><i>grid_mro_oa_batches</i>"]
        A1 --> A2
    end

    subgraph PE["👤 Planejamento / Engenharia"]
        direction TB
        B1["📥 Importar do P6<br/><i>ctrl_import_excel</i>"]
        B2["🗂️ Estruturar WBS<br/><i>form_public_mro_wbs</i>"]
        B3["📝 Cadastrar Tarefas<br/><i>form_public_mro_tasks</i>"]
        B4["📦 Alocar Materiais<br/><i>form_public_mro_task_materials</i>"]
        B5["👷 Alocar Mão de Obra<br/><i>form_public_mro_task_resources</i>"]
        B6["🚀 Liberar p/ Execução<br/><i>grid_public_mro_tasks</i>"]
        B1 --> B2 --> B3 --> B4 --> B5 --> B6
    end

    subgraph SV["👤 Supervisor de Produção"]
        direction TB
        C1["📊 Painel do Supervisor<br/><i>tabs_supervisor</i>"]
        C2["📋 Dispatch - Alocar Mecânicos<br/><i>grid_mro_dispatch</i>"]
        C3["▶️ A Distribuir<br/><i>form_planned</i>"]
        C4["🔧 Em Andamento<br/><i>form_progress</i>"]
        C5["⚠️ Com Impedimentos<br/><i>form_blocked</i>"]
        C6["✅ Concluídas<br/><i>form_completed</i>"]
        C7["📈 Kanban / Gantt<br/><i>blank_kanban_board / gantt</i>"]
        C8["⛔ Gerenciar Bloqueios<br/><i>material / ferramenta / mão de obra</i>"]
        C1 --> C3 & C4 & C5 & C6
        C2 --> C3
        C4 --> C8
    end

    subgraph MK["👤 Mecânico / Técnico (Tablet)"]
        direction TB
        D1["📱 Painel do Mecânico<br/><i>grid_my_tasks</i>"]
        D2["▶️ Clock-in na Tarefa"]
        D3["⏸️ Pausar / Clock-out"]
        D4["🔧 Executar Serviço"]
        D5["⚠️ Abrir Não-Rotina (NRC)<br/><i>ctrl_abertura_nrc</i>"]
        D6["🏁 Finalizar Tarefa"]
        D1 --> D2 --> D4
        D4 --> D3
        D4 --> D5
        D3 --> D2
        D4 --> D6
    end

    subgraph FT["👤 Ferramentaria / Almoxarifado"]
        direction TB
        E1["🎯 Terminal de Bipagem<br/><i>blank_mro_ferramentaria</i>"]
        E2["🔴 Check-out<br/><i>retirar ferramenta</i>"]
        E3["🟢 Check-in<br/><i>devolver ferramenta</i>"]
        E4["📦 Controle de Materiais<br/><i>grid_public_mro_materials</i>"]
        E5["🔧 Gestão de Calibração<br/><i>grid_public_mro_tools</i>"]
        E6["📋 Relatar Incidente<br/><i>form_public_mro_tool_incidents</i>"]
        E1 --> E2
        E1 --> E3
        E2 -->|"ferramenta calibração vencida"| E5
        E3 -->|"DANO / PERDA"| E6
    end

    subgraph QL["👤 Qualidade / Inspetoria"]
        direction TB
        F1["✍️ Assinar Digitalmente<br/><i>form_public_mro_task_signoffs</i>"]
        F2["🔍 Auditar Assinaturas<br/><i>grid_public_mro_task_signoffs</i>"]
        F3["📋 Relatórios SGSO<br/><i>perda / dano de ferramenta</i>"]
        F1 --> F2
        E6 --> F3
    end

    subgraph CFG["⚙️ Configurações (Base)"]
        G1["👥 Colaboradores<br/><i>grid_public_mro_employees</i>"]
        G2["🧠 Skills / Licenças<br/><i>grid_public_mro_skills</i>"]
        G3["📊 Matriz de Qualificação<br/><i>grid_public_mro_resource_roster</i>"]
        G4["🎓 Treinamentos<br/><i>grid_public_mro_resource_trainings</i>"]
        G5["📋 Recursos (Mão de Obra)<br/><i>grid_public_mro_resources</i>"]
        G1 --> G5 --> G3
        G2 --> G3
        G4 --> G3
    end

    subgraph CL["👤 Cliente"]
        H1["✅ Aprovar Lotes O&A<br/><i>via grid_mro_oa_batches</i>"]
    end

    %% Conexões entre operadores
    GF -->|"aeronave + projeto"| PE
    PE -->|"tarefas liberadas<br/>(alocações criadas autom.)"| SV
    SV -->|"mecânico alocado"| MK
    MK -->|"solicita ferramenta"| FT
    FT -->|"ferramenta liberada"| MK
    MK -->|"NRC aberta"| PE
    MK -->|"tarefa concluída"| QL
    PE -->|"O&A p/ aprovação"| CL
    CL -->|"O&A aprovado"| PE
    SV -->|"bloqueios resolvidos"| MK
    CFG ---|"base de dados"| PE
    CFG ---|"base de dados"| SV
    CFG ---|"base de dados"| MK
    CFG ---|"base de dados"| QL

    classDef gestao fill:#2d5a7d,color:#fff,stroke:#1a3a5a
    classDef planej fill:#1b7a4a,color:#fff,stroke:#0f5a35
    classDef superv fill:#b45f1b,color:#fff,stroke:#8a4515
    classDef mech fill:#7a2d6e,color:#fff,stroke:#5a1f52
    classDef tools fill:#a83232,color:#fff,stroke:#802525
    classDef qual fill:#2d6e6e,color:#fff,stroke:#1f5252
    classDef config fill:#555,color:#fff,stroke:#3a3a3a
    classDef client fill:#5a7a2d,color:#fff,stroke:#3f5a1f

    class GF gestao
    class A1,A2,A3 gestao
    class PE planej
    class B1,B2,B3,B4,B5,B6 planej
    class SV superv
    class C1,C2,C3,C4,C5,C6,C7,C8 superv
    class MK mech
    class D1,D2,D3,D4,D5,D6 mech
    class FT tools
    class E1,E2,E3,E4,E5,E6 tools
    class QL qual
    class F1,F2,F3 qual
    class CFG config
    class G1,G2,G3,G4,G5 config
    class CL client
    class H1 client
```

## Papéis representados

| Papel | Cor | Responsabilidade principal |
|-------|-----|----------------------------|
| **Gestão de Frota** | Azul | Cadastro de aeronaves, projetos/checks, lotes O&A |
| **Planejamento / Engenharia** | Verde | Importação P6, WBS, tarefas, alocação de recursos, liberação |
| **Supervisor de Produção** | Laranja | Alocação de mecânicos, dispatch, Kanban, gerenciamento de bloqueios |
| **Mecânico / Técnico** | Roxo | Clock-in/out, execução no tablet, abertura de NRC |
| **Ferramentaria / Almoxarifado** | Vermelho | Terminal de bipagem, calibração, materiais, incidentes |
| **Qualidade / Inspetoria** | Teal | Assinatura digital, auditoria, relatórios SGSO |
| **Cliente** | Verde-oliva | Aprovação de lotes Over & Above |

## Fluxo principal

1. **Gestão de Frota** cadastra aeronaves e projetos
2. **Planejamento** importa do Primavera P6, estrutura WBS, cadastra tarefas e libera para execução
3. **Supervisor** distribui tarefas aos mecânicos pelo Dispatch
4. **Mecânico** executa no tablet com clock-in/out, abre NRCs e solicita ferramentas
5. **Ferramentaria** atende via terminal de bipagem com validação ANAC
6. **Qualidade** realiza assinatura digital das tarefas concluídas
7. **Cliente** aprova lotes O&A quando necessário

> ⚠️ **Nota:** O módulo **Security** (login, usuários, grupos, permissões, 2FA) não está representado neste diagrama pois o usuário já conhece seu funcionamento.
