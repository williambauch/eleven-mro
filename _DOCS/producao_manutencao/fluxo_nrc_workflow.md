# Fluxo NRC — Workflow de Aprovação

> Diagrama de navegação do fluxo de aprovação de Não-Rotinas (NRC) no sistema MRO System.
> As setas **tracejadas laranja** representam retorno para fases anteriores.
> O nó **diamante amarelo** representa a decisão do **motor O&A (`mro_engine`)** acionado por `btn_validar_prog`.

```mermaid
flowchart TD
    A(ctrl_abertura_nrc<br/>única forma de criar NRC) --> B[DRAFT]

    %% DRAFT → saídas
    B -- btn_enviar_eng --> C[PENDING_ENG]
    B -- btn_enviar_prog --> D[PENDING_PROG]

    %% PENDING_ENG → saídas
    C -- btn_enviar_prog --> D
    C -- btn_enviar_coord --> E[PENDING_COORD]
    C -- btn_cancelar --> F[CANCELLED]

    %% PENDING_COORD → saídas
    E -. btn_enviar_eng .-> C
    E -- btn_enviar_prog --> D
    E -- btn_cancelar --> F

    %% PENDING_PROG → saídas
    D -- btn_enviar_coord --> E
    D -- btn_cancelar --> F
    D -- btn_validar_prog --> Z{Dentro do CAP?}
    Z -- Sim (auto-approve) --> H[RELEASED]
    Z -- Não --> G[PENDING_OA]

    %% PENDING_OA → saídas
    G -- btn_aprovar_cliente --> H
    G -- btn_reprovar_cliente --> I[COMMERCIAL_REVIEW]
    G -. auto-approve<br/>motor O&A .-> H

    %% COMMERCIAL_REVIEW → saídas
    I -. btn_enviar_cliente .-> G
    I -. btn_enviar_prog .-> D
    I -- btn_cancelar --> F

    %% Estilo dos nós
    style A fill:#e3f2fd,stroke:#1565c0,color:#000
    style B fill:#fff3e0,stroke:#e65100,color:#000
    style C fill:#fff3e0,stroke:#e65100,color:#000
    style D fill:#fff3e0,stroke:#e65100,color:#000
    style E fill:#fff3e0,stroke:#e65100,color:#000
    style Z fill:#fff8e1,stroke:#f9a825,color:#000
    style G fill:#e8f5e9,stroke:#2e7d32,color:#000
    style H fill:#e8f5e9,stroke:#2e7d32,color:#000
    style I fill:#fce4ec,stroke:#c62828,color:#000
    style F fill:#fce4ec,stroke:#c62828,color:#000

    %% Links de retorno com estilo tracejado e cor laranja
    linkStyle 6 stroke:#ff6f00,stroke-width:2px,stroke-dasharray: 5 5
    linkStyle 17 stroke:#ff6f00,stroke-width:2px,stroke-dasharray: 5 5
    linkStyle 18 stroke:#ff6f00,stroke-width:2px,stroke-dasharray: 5 5

    %% Auto-approve em verde
    linkStyle 12 stroke:#2e7d32,stroke-width:2px
    linkStyle 16 stroke:#2e7d32,stroke-width:2px,stroke-dasharray: 5 5
```
