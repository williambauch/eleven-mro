# Credenciais de Acesso — MRO System

> ⚠️ **Aviso:** Este arquivo contém credenciais reais do ambiente de desenvolvimento/homologação. Não compartilhe com pessoas não autorizadas. Em produção, todas as senhas devem ser alteradas.

## Grupos e usuários do sistema

| Grupo ID | Perfil | Login | Senha | Papel / Função |
|:--------:|--------|-------|-------|----------------|
| 1 | **Administrador** | `admin` | `Eleven@321` | Gestão de Frota / TI |
| 2 | **Group Default** | `william` | `Eleven@2026` | Usuário padrão (múltiplos grupos) |
| 3 | **MECANICO** | `mecanico` | `Mecanico@321` | Mecânico / Técnico (tablet) |
| 4 | **ENGENHARIA** | `engenheiro` | `Engenheiro@321` | Planejamento / Engenharia |
| 5 | **COORDENADOR** | `coordenador` | `Coordenador@321` | Coordenação de Produção |
| 6 | **SUPERVISOR** | `supervisor` | `Supervisor@321` | Supervisor de Produção |
| 7 | **PROGRAMACAO** | `programador` | `Programador@321` | Programação (P6) |
| 8 | **PLANEJAMENTO** | `planejador` | `Planejador@321` | Planejamento |
| 9 | **CLIENTE** | `cliente` | `Cliente@321` | Cliente (acompanhamento) |
| 10 | **COMERCIAL** | `comercial` | `Comercial@321` | Comercial / Vendas |
| 11 | **COMPRAS** | `william` | `Eleven@2026` | Compras / Suprimentos |
| 12 | **FERRAMENTARIA** | `ferramentaria` | `Ferramentaria@321` | Ferramentaria / Almoxarifado |

## Atuação de cada perfil no workflow de Não-Rotinas (NRC)

### ADMINISTRADOR (`admin`)
Perfil com **acesso total ao sistema** — pode visualizar, editar e executar ações em qualquer registro, independentemente de status ou grupo. Usado pela Gestão de Frota / TI para suporte e configurações.

**No workflow NRC:** Não tem restrições. Enxerga todos os botões e blocos em qualquer fase.

---

### MECANICO (`mecanico`)
Perfil de **Mecânico / Técnico (tablet)** — é o usuário de campo que cria as NRCs e executa as tarefas.

**Quando uma NRC chega em DRAFT (Fase 1):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_enviar_eng` | **Enviar pra Engenharia** | Se precisar de análise técnica |
| `btn_enviar_prog` | **Enviar pra Programação** | Se tiver HH estimado, pula engenharia |
| `delete` | **Excluir NRC** | Remove o rascunho |
| `update` | **Salvar alterações** | Pode editar campos |

**Regra:** DRAFT somente o criador da NRC pode alterar (`[usr_login] == {created_by}`).

**Em fases seguintes (PENDING_OA, COMMERCIAL_REVIEW, RELEASED):** O MECANICO **não vê o bloco financeiro** (`bloco_financeiro` fica oculto para ele).

**Resumo do papel:** O mecânico é o **ponto de partida** do workflow — ele cria a demanda, descreve o relato e dispara o fluxo de aprovação. Depois que a NRC sai de DRAFT, ele perde o controle sobre ela e vira apenas um observador (sem botões de ação).

---

### ENGENHARIA (`engenheiro`)
Perfil de **Planejamento / Engenharia** — analisa tecnicamente as NRCs enviadas a ele.

**Quando uma NRC chega em PENDING_ENG (Fase 2):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_enviar_prog` | **Enviar pra Programação** | Se a análise foi concluída, segue direto |
| `btn_enviar_coord` | **Enviar pra Coordenação** | Se precisar de validação do coordenador |
| `btn_cancelar` | **Cancelar NRC** | Se não for viável tecnicamente |
| `update` | **Salvar alterações** | Pode complementar informações |
| `{instruction_text}` | Read-only | Não altera o relato original do mecânico |

**Resumo do papel:** A engenharia é o **primeiro filtro técnico** — analisa a viabilidade, complementa dados e decide se a NRC segue direto pra programação ou passa pela coordenação. Pode cancelar se não fizer sentido tecnicamente.

---

### COORDENADOR (`coordenador`)
Perfil de **Coordenação de Produção** — atua como ponto de decisão no meio do fluxo.

**Quando uma NRC chega em PENDING_COORD (Fase 3):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_enviar_eng` | **Voltar pra Engenharia** | Se precisar de mais análise |
| `btn_enviar_prog` | **Enviar pra Programação** | Se já estiver ok, segue direto pra execução |
| `btn_cancelar` | **Cancelar NRC** | Se não for viável |
| `update` | **Salvar alterações** | Pode editar campos |

**Resumo do papel:** O coordenador é um **gatekeeper** — ele revisa o que veio da engenharia e decide se a NRC está pronta pra programação ou se precisa voltar pra ajustes. É um ponto de controle antes de liberar pra execução.

---

### PROGRAMACAO (`programador`)
Perfil de **Programação (P6)** — responsável por alocar recursos e validar a NRC no sistema de programação.

**Quando uma NRC chega em PENDING_PROG (Fase 4):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_validar_prog` | **Validar NR** | Dispara lógica de O&A, status vai pra `PENDING_OA` |
| `btn_enviar_coord` | **Enviar p/ Coordenador** | Se precisar de reavaliação |
| `btn_cancelar` | **Cancelar NRC** | Se não for viável |
| `update` | **Salvar alterações** | Pode editar campos |
| `{instruction_text}` | Read-only | Não altera o relato original |

**Resumo do papel:** A programação é responsável por **validar a NRC no P6** (sistema de programação). Depois de validada, dispara o módulo de Over & Above (O&A) e a NRC segue pra aprovação do cliente. É o último filtro interno antes da negociação comercial.

---

### SUPERVISOR (`supervisor`)
Perfil de **Supervisor de Produção** — supervisiona as atividades da equipe em campo.

**No workflow NRC:** O supervisor tem permissão de **visualização** em todas as fases, mas não possui botões de ação específicos no workflow de NRC. Pode ver o bloco financeiro quando aplicável (não é MECANICO).

**Resumo do papel:** Acompanha o andamento das NRCs e tarefas da equipe, mas não participa ativamente do fluxo de aprovação de NRCs.

---

### PLANEJAMENTO (`planejador`)
Perfil de **Planejamento** — cria tarefas e NRCs, similar ao MECANICO mas com visão mais gerencial.

**No workflow NRC:** Na **Fase 1 (DRAFT)**, se for o criador da NRC, vê os mesmos botões do MECANICO: `btn_enviar_eng`, `btn_enviar_prog`, `delete`, `update`. Pode disparar NRCs diretamente sem passar pelo mecânico.

**Resumo do papel:** O planejador pode **criar NRCs diretamente** e iniciar o fluxo de aprovação, sem depender do mecânico. É um perfil híbrido entre criação e gestão.

---

### CLIENTE (`cliente`)
Perfil de **Cliente (acompanhamento)** — visualiza NRCs e aprova ou rejeita orçamentos.

**Quando uma NRC chega em PENDING_OA (Fase 5):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_aprovar_cliente` | **Aprovar** | Cliente aprovou o orçamento, NRC segue pra liberação |
| `btn_reprovar_cliente` | **Recusar** | Cliente recusou, NRC vai pra negociação comercial |

**Resumo do papel:** O cliente é o **aprovador final** do orçamento — ele decide se aceita o valor da NRC. Se aprovar, a NRC é liberada pra execução. Se recusar, o time comercial entra em ação pra renegociar.

---

### COMERCIAL (`comercial`)
Perfil de **Comercial / Vendas** — gerencia a negociação com o cliente e o bloco financeiro.

**Atua em duas fases:**

**PENDING_OA (Fase 5):** junto com o CLIENTE, vê `btn_aprovar_cliente` e `btn_reprovar_cliente`.

**COMMERCIAL_REVIEW (Fase 6 — Cliente Recusou):**
| Botão | Ação | Significado |
|-------|------|-------------|
| `btn_enviar_prog` | **Enviar pra Programação** | Após renegociação, segue pra reprogramação |
| `btn_enviar_cliente` | **Enviar pra Cliente** | Reenvia orçamento ajustado pro cliente |
| `btn_cancelar` | **Cancelar NRC** | Se não houver acordo |
| `update` | **Salvar alterações** | Ajusta valores e condições |

**Nas fases 5 e 6:** Se o usuário não for MECANICO, o **bloco financeiro** (`bloco_financeiro`) fica visível para consulta dos valores de O&A.

**Resumo do papel:** O comercial é o **negociador** — quando o cliente recusa, ele reajusta os valores e tenta um novo acordo. Também acompanha o bloco financeiro para embasar a negociação.

---

### COMPRAS (`william`)
Perfil de **Compras / Suprimentos** — atua na aquisição de materiais e suprimentos.

**No workflow NRC:** Não possui botões de ação específicos no workflow de NRC. Atua mais nas tarefas de rotina (compras de materiais).

---

### FERRAMENTARIA (`ferramentaria`)
Perfil de **Ferramentaria / Almoxarifado** — gerencia ferramentas e movimentação de estoque.

**No workflow NRC:** Não possui botões de ação específicos no workflow de NRC. Atua no controle de ferramentas e almoxarifado associado às tarefas.

