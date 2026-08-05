# PLANO - Item 6: Detalhamento Multiarmazem de Materiais

## Melhorar vinculacao de recursos nas tarefas, visualizacao de estoques e acesso a documentos

---

## 1. OBJETIVO

No `grid_public_mro_materials`, ao clicar em um item especifico (campo part_number),
abrir detalhamento mostrando o saldo dividido por armazem. O detalhamento sera
uma nova grid do ScriptCase.

---

## 2. ESTADO ATUAL (verificado no banco)

| Fato | Valor |
|------|-------|
| Modelo atual | `mro_materials` tem 1 registro por armazem (`stock_location` + `stock_balance`) |
| Tabela separada de saldo | NAO existe (nem `mro_material_warehouse_balance`, nem `mro_warehouses`) |
| Armazens existentes | 01, 02, 1, FB |
| Mesmo PN em multiplos locais | Somente `BACB30NX6K14` (01 + FB) |
| Saldos atuais | Todos zerados (dados de import sem saldo) |
| Chave unica | `(product_code, part_number, stock_location)` |
| Outras fontes de saldo | NAO existem - `mro_material_returns` e somente devolucoes (sem saldo) |

**Confirmado**: a unica fonte de saldo e `mro_materials.stock_balance` por `stock_location`.

---

## 3. DECISOES DE DESIGN

- **Nova app**: `grid_public_mro_material_stock_location`
- **Query**: filtra `mro_materials` por `part_number` e lista os locais com saldo
- **WHERE**: `WHERE part_number = [glo_part_number]` (variavel global recebida do link)
- **Abertura**: via recurso nativo do ScriptCase **"Ligacao de campo"** no campo
  `part_number` da `grid_public_mro_materials` (link para a nova grid)
- A ligacao passa o valor do part_number como parametro/global para a app destino

---

## 4. ARQUIVOS

| Arquivo | Acao |
|---------|------|
| `Almoxarifado/grid_public_mro_material_stock_location/sql/schema.sql` | CRIAR - SELECT por armazem |
| `Almoxarifado/grid_public_mro_material_stock_location/config.json` | CRIAR - config da grid |
| `Almoxarifado/grid_public_mro_materials` | EDITAR (no ScriptCase) - Ligacao de campo no part_number |

---

## 5. SQL DA NOVA GRID

```sql
SELECT
    material_id as material_id,
    part_number as part_number,
    description as description,
    product_code as product_code,
    stock_location as stock_location,
    stock_balance as stock_balance,
    unit_measure as unit_measure,
    is_consumable as is_consumable,
    is_blocking_task as is_blocking_task
FROM
    public.mro_materials
WHERE
    part_number = [glo_part_number]
ORDER BY
    stock_location
```

Filtra por `part_number` (mesmo PN em varios locais) em vez de `material_id`
(pois cada material_id ja e um local diferente).

---

## 6. ABERTURA PELA GRID PRINCIPAL (no ScriptCase)

- Usar o recurso nativo **"Ligacao de campo"** no campo `part_number`
  da `grid_public_mro_materials`
- Aplicacao destino: `grid_public_mro_material_stock_location`
- Forma de exibicao: definir no ScriptCase (modal, iframe, mesma janela ou nova janela)
- O valor do campo part_number da linha clicada sera passado como `[glo_part_number]`
  para a app destino (configurado na propria ligacao)

---

## 7. RISCOS E PONTOS DE ATENCAO

- **Saldos zerados**: o detalhamento vai mostrar 0 em todos - e o dado atual,
  nao erro. Confirmado que nao ha outra fonte de saldo no banco
- **Filtro por part_number**: se o mesmo PN existir com `product_code`
  diferentes, pode misturar. A maioria dos PNs tem 1 local apenas;
  considerar filtrar por `(product_code, part_number)` se necessario no futuro
- **`[glo_part_number]`**: a global deve ser configurada na "Ligacao de campo"
  do ScriptCase para receber o valor da linha clicada
