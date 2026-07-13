# Credenciais de Acesso — MRO System

> ⚠️ **Aviso:** Este arquivo contém credenciais reais do ambiente de desenvolvimento/homologação. Não compartilhe com pessoas não autorizadas. Em produção, todas as senhas devem ser alteradas.

## Grupos e usuários do sistema

| Grupo ID | Perfil | Login | Senha |
|:--------:|--------|-------|-------|
| 1 | **Administrador** | `william` | `Eleven@2026` |
| 2 | **Group Default** | `william` | `Eleven@2026` |
| 3 | **MECANICO** | `mecanico` | `Mecanico@321` |
| 4 | **ENGENHARIA** | `engenheiro` | `Engenheiro@321` |
| 5 | **COORDENADOR** | `coordenador` | `Coordenador@321` |
| 6 | **SUPERVISOR** | `supervisor` | `Programador@321` |
| 7 | **PROGRAMACAO** | `programador` | `Programador@321` |
| 8 | **PLANEJAMENTO** | `planejador` | `Planejador@321` |
| 9 | **CLIENTE** | `cliente` | `Cliente@321` |
| 10 | **COMERCIAL** | `comercial` | `Comercial@321` |
| 11 | **COMPRAS** | `william` | `Eleven@2026` |
| 12 | **FERRAMENTARIA** | `ferramentaria` | `Ferramentaria@321` |

## Observações

- O usuário `william` está vinculado a múltiplos grupos (Administrador, Default e Compras), acumulando as permissões de todos eles.
- As permissões são **cumulativas** entre grupos — um usuário em mais de um grupo herda as permissões de todos.
- Alguns perfis contêm acentos nos nomes (MECANICO, ENGENHARIA, PROGRAMACAO, PLANEJAMENTO) conforme cadastrados no sistema.
- Para acessar o sistema, utilize a URL do ambiente correspondente (desenvolvimento, homologação ou produção).

## Mapeamento de perfis x operadores

| Perfil | Operador no sistema |
|--------|---------------------|
| Administrador | Gestão de Frota / TI |
| MECANICO | Mecânico / Técnico (tablet) |
| ENGENHARIA | Planejamento / Engenharia |
| COORDENADOR | Coordenação de Produção |
| SUPERVISOR | Supervisor de Produção |
| PROGRAMACAO | Programação (P6) |
| PLANEJAMENTO | Planejamento |
| CLIENTE | Cliente (acompanhamento) |
| COMERCIAL | Comercial / Vendas |
| COMPRAS | Compras / Suprimentos |
| FERRAMENTARIA | Ferramentaria / Almoxarifado |
