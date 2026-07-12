# Fases do Projeto (form_public_mro_project_phases)

Módulo Gestão de Frota e Projetos — aplicação do tipo Form.

Tela de cadastro das fases que compõem a estrutura de um projeto. Cada fase possui um código, um nome descritivo e uma ordem de exibição, permitindo organizar a sequência do trabalho.

## O que o usuário pode fazer

- Cadastrar novas fases de projeto com código e nome.
- Editar fases existentes.
- Definir a ordem de exibição das fases (`sort_order`).

## Comportamentos e regras importantes

- O código da fase (`phase_code`) é a chave primária, portanto não pode haver duplicidade.
- A ordem de exibição (`sort_order`) define a sequência em que as fases são apresentadas nas telas de planejamento e execução.

## Dados envolvidos

Tabela `public.mro_project_phases`.
