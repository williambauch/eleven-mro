# Teste manual limpo — Ferramentaria

## Objetivo
Validar em sequência limpa os 3 cenários do check-in:
1. OK
2. DANO
3. PERDA

A execução deve seguir em ordem e usar ferramenta diferente em cada caso, para evitar interferência de estado anterior.

---

## Regras do teste
- Usar uma ferramenta disponível em cada caso.
- NÃO repetir a mesma ferramenta no mesmo fluxo.
- Sempre partir do estado limpo do app.
- **O APONTAMENTO (CHECKOUT) É OBRIGATÓRIO ANTES DO DESAPONTAMENTO (CHECK-IN)** — sem transação ativa, o check-in retorna `Nenhum empréstimo ativo localizado para esta ferramenta.`
- Registrar o resultado de cada etapa antes de prosseguir.
- Preferir ferramentas que apareçam no painel como disponíveis.

---

## Ferramentas sugeridas
Use somente ferramentas em status `AVAILABLE` **sem histórico de transações** (garante estado 100% limpo).

Status atual dos itens (auditado em banco em 2026-08-17):
- Caso OK: `00-010106` (TRANSDUCER CONCAVE SIDE PRESSURE FACE) — ✅ CONCLUÍDO (está `DISPONIVEL`)
- Caso DANO: `002-035IN` (FEELER GAGE) — ✅ CONCLUÍDO (está `FERRAMENTA DANIFICADA`)
- Caso PERDA: `005-1MM` (FEELER GAGE) — ⏳ PENDENTE (está `AVAILABLE`)
- Reserva para PERDA (se necessário): `008407` (SPANNER WRENCH - ULB) — `AVAILABLE`

> Obs.: `002-035IN` já ficou danificada após o caso DANO e não pode mais ser usada em outro cenário. Ferramentas como `F72701-36`, `00-010032`, `ST992B`, `MIL-C-5015`, `F72900-1`, `C27030-33` e `M225220/01` também já estão com status alterado — **não use**.

---

## Passo 1 — Teste do caso OK

### Ação
1. Abra a tela de Ferramentaria.
2. Confirme que o modo está em `APONTAMENTO`.
3. Digite um crachá válido no campo de mecânico.
   - Exemplo: `123456` (mecânico `teste`, com clock-in ativo na JIC 370046)
4. Pressione Enter.
5. Aguarde a lista de ferramentas planejadas aparecer.
   - Retorno esperado: `MECÂNICO: teste` + `JIC ATIVA: 370046` + lista de PNs.
6. No campo `Part Number / Ferramenta`, digite a ferramenta limpa do caso OK.
   - Exemplo: `00-010106`
7. Clique no botão de processar ou pressione Enter.
8. Verifique o retorno do sistema.
   - Retorno esperado: `APONTAMENTO: Crachá: 123456 | PN: 00-010106 | Desc: TRANSDUCER CONCAVE SIDE PRESSURE FACE`
9. Mude para o modo `DESAPONTAMENTO`.
10. Digite a mesma ferramenta (`00-010106`).
11. No modal de inspeção, clique em `OK - Sem Danos`.

### Resultado esperado
- Checkout realizado com sucesso (log `log-success`).
- Check-in realizado com sucesso.
- Mensagem: `DESAPONTAMENTO: Crachá: 123456 | PN: 00-010106 | Desc: TRANSDUCER CONCAVE SIDE PRESSURE FACE (Disponível).`
- Ferramenta volta para `DISPONIVEL`.
- NÃO deve abrir modal de relatório (sem `redirect_form`).
- Toast: `Sucesso — Transação realizada.`

### Observação
Se aparecer `A ferramenta '...' está indisponível para empréstimo no momento.`, o PN sugerido está com status alterado. Use um PN `AVAILABLE` sem histórico (ver seção Ferramentas sugeridas).

---

## Passo 2 — Teste do caso DANO

### ATENÇÃO — fluxo correto
O **apontamento (checkout) é obrigatório antes do desapontamento (check-in)**. Se você for direto no desapontamento sem apontar, o sistema retorna `Nenhum empréstimo ativo localizado para esta ferramenta.` — isso é comportamento correto e não um bug.

### Ação
1. Use uma ferramenta limpa para o caso DANO (ex.: `008407` SPANNER WRENCH - ULB, ou outra `AVAILABLE`).
2. Volte para `APONTAMENTO`.
3. Digite o crachá `123456` e pressione Enter.
4. **Faça o checkout com a ferramenta escolhida** (apontamento).
   - Retorno esperado: `APONTAMENTO: Crachá: 123456 | PN: [PN] | Desc: [Descrição]`
5. Confirme no painel que o apontamento foi registrado (log verde).
6. Só então mude para `DESAPONTAMENTO`.
7. Digite a mesma ferramenta.
8. No modal, clique em `Avariada / Danificada`.
   - Retorno esperado: `RETENÇÃO (DANO): Crachá: 123456 | PN: [PN] | Desc: [Descrição].`

### Resultado esperado
- Checkout bem-sucedido.
- Check-in com condição `DANO`.
- Ferramenta vai para status de avaria.
- O sistema abre o relatório de ocorrência.
- Redirecionamento esperado: `TF-60-013`.

### Validação
- Confirme que o modal de relatório apareceu.
- Verifique se a tela de ocorrência não ficou em branco.
- Confira se os campos relevantes do relatório carregam corretamente.

---

## Passo 3 — Teste do caso PERDA

### Ação
1. Use a ferramenta limpa do caso PERDA: `005-1MM` (FEELER GAGE).
2. Volte para `APONTAMENTO`.
3. Digite o crachá `123456` e pressione Enter.
4. Faça o checkout com `005-1MM`.
   - Retorno esperado: `APONTAMENTO: Crachá: 123456 | PN: 005-1MM | Desc: FEELER GAGE`
5. Mude para `DESAPONTAMENTO`.
6. Digite `005-1MM`.
7. No modal, clique em `Extraviada (Perda)`.
   - Retorno esperado: `EXTRAVIO: Crachá: 123456 | PN: 005-1MM | Desc: FEELER GAGE.`

### Resultado esperado
- Checkout bem-sucedido.
- Check-in com condição `PERDA`.
- Ferramenta vai para status de extravio.
- O sistema abre o relatório de ocorrência.
- Redirecionamento esperado: `TF-60-041`.

### Validação
- Confirme que o modal de relatório abriu.
- Verifique se o conteúdo não ficou em branco.
- Valide a carga do formulário de perda.

---

## Critérios de sucesso do teste
O teste está concluído com sucesso quando:
- Caso OK retorna corretamente sem relatório.
- Caso DANO dispara o formulário de ocorrência e status de avaria.
- Caso PERDA dispara o formulário de ocorrência e status de extravio.
- Nenhum dos cenários trava no modal em branco.
- O estado da ferramenta corresponde ao cenário executado.

---

## Checklist final
- [x] Caso OK executado com ferramenta limpa
- [x] Caso DANO executado com ferramenta limpa
- [ ] Caso PERDA executado com ferramenta limpa
- [ ] Mensagens de sucesso conferidas
- [ ] Relatórios abertos corretamente
- [ ] Estado final da ferramenta coerente com o cenário

---

## Observação
Para evitar falso positivo, nunca reutilize a mesma ferramenta em diferentes cenários do mesmo teste. O ideal é executar em ordem e com ferramenta distinta em cada caso.
