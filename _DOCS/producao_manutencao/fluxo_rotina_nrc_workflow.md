# Fluxo de Trabalho — NRC e Rotina Padrão

> ⚠️ **FONTE DA VERDADE ATUALIZADA (MRO-122)**
>
> O diagrama de fluxo de trabalho (NRC + Rotina Padrão) agora é mantido no arquivo **HTML interativo**:
>
> **📄 [`fluxo_trabalho_nrc_rotina.html`](../fluxo_trabalho_nrc_rotina.html)**
>
> Este arquivo HTML é a **fonte da verdade** sobre os status e transições do workflow, pois:
> - Reflete **fielmente o código atual** das aplicações (botões, motor O&A, blank de abertura)
> - É **interativo** (zoom, pan) — baseado em Mermaid JS via CDN
> - Contém **marcações visuais** para pontos a reavaliar (ex: auto-approve do `mro_engine.php`)
>
> Para visualizar: abra o arquivo no navegador (duplo clique) ou aponte para ele no repositório.

---

## Resumo do fluxo (referência rápida)

### Fluxo NRC (aprovação)

```
blank_abertura_nrc → DRAFT → PENDING_ENG / PENDING_PROG / PENDING_COORD
  → btn_validar_prog → Dentro do CAP?
      → Sim (auto-approve) → RELEASED
      → Não → PENDING_OA
  → PENDING_OA → btn_aprovar_cliente → APPROVED
                → btn_reprovar_cliente → COMMERCIAL_REVIEW
                → auto-approve motor O&A → RELEASED ⚠️ (reavaliar)
  → APPROVED → btn_liberar_para_execucao → RELEASED
  → COMMERCIAL_REVIEW → btn_enviar_cliente → PENDING_OA
                      → btn_enviar_prog → PENDING_PROG
                      → btn_cancelar → CANCELLED
```

### Fluxo Rotina Padrão (execução)

```
Importação P6 → PLANNED → NOT_STARTED
  → btn_liberar_para_execucao → RELEASED (+ assignments por skill)
  → Painel Supervisor (decisão: alocar?) → IN_PROGRESS
  → COMPLETED → SUPSIG → CLOSED
  → PENDING_HANDOVER → PENDING_PROG → btn_validar_prog_rotina → HH dentro do orçado?
      → Sim → RELEASED novamente
      → Não → BLOCKED → ajusta orçamento → PENDING_PROG
```

---

## Detalhes dos botões (transições reais)

| Botão | De | Para | Aplicação |
|---|---|---|---|
| `btn_enviar_eng` | DRAFT / PENDING_COORD | PENDING_ENG | form_public_mro_tasks |
| `btn_enviar_prog` | DRAFT / PENDING_ENG / PENDING_COORD / COMMERCIAL_REVIEW | PENDING_PROG | form_public_mro_tasks |
| `btn_enviar_coord` | PENDING_ENG / PENDING_PROG | PENDING_COORD | form_public_mro_tasks |
| `btn_cancelar` | PENDING_ENG / PENDING_COORD / PENDING_PROG / COMMERCIAL_REVIEW | CANCELLED | form_public_mro_tasks |
| `btn_validar_prog` | PENDING_PROG | Dentro do CAP? (motor O&A) | form_public_mro_tasks |
| `btn_aprovar_cliente` | PENDING_OA | APPROVED | form_public_mro_tasks |
| `btn_reprovar_cliente` | PENDING_OA | COMMERCIAL_REVIEW | form_public_mro_tasks |
| `btn_enviar_cliente` | COMMERCIAL_REVIEW | PENDING_OA | form_public_mro_tasks |
| `btn_liberar_para_execucao` | PLANNING / NOT_STARTED / PLANNED / APPROVED | RELEASED (+ assignments) | grid_public_mro_tasks |
| `btn_validar_prog_rotina` | PENDING_PROG | HH dentro do orçado? | form_public_mro_tasks |

## Pontos a reavaliar (marcados no HTML)

- **Auto-approve do motor O&A** (`mro_engine.php`): `PENDING_OA → RELEASED` direto, sem criar assignments — mesmo problema do Bug 1 (MRO-122). Aguarda decisão do superior. Ver seção "Bug 1 — EXTENSAO" no `__Tarefas/MRO-122/MRO-122.md`.
