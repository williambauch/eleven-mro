# Colaboradores e Mecânicos (grid_public_mro_employees)

Módulo Configurações / Tabelas Básicas — aplicação do tipo Grid.

Tela de cadastro de colaboradores da empresa, incluindo mecânicos, inspetores e supervisores. Cada registro contém dados pessoais, matrícula, código ANAC, especialidade (skill) e indicadores de função.

## O que o usuário pode fazer

- Visualizar todos os colaboradores cadastrados.
- Consultar nome, matrícula, e-mail, código ANAC e especialidade.
- Identificar quem é inspetor, supervisor ou está ativo.
- Acessar o cadastro de cada colaborador para edição.

## Comportamentos e regras importantes

- O campo `employee_registration` é a matrícula do mecânico, usada no terminal de ferramentaria para bipagem do crachá.
- O campo `login` vincula o colaborador ao seu usuário no sistema de segurança (sec_users).
- O campo `skill_id` define a especialidade primária do colaborador.
- Os booleanos `is_inspector`, `is_supervisor` e `is_active` controlam permissões e visibilidade em todo o sistema.

## Dados envolvidos

Tabela `public.mro_employees`. Relacionada com `mro_skills` pelo campo `skill_id` e com `sec_users` pelo campo `login`.
