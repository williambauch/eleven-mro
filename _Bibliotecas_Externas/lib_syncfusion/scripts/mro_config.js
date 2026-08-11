// Arquivo: mro_config.js

// 1. REGISTRO DA LICENÇA (Obrigatório)
// Cole sua chave gigante entre as aspas abaixo
ej.base.registerLicense('Ngo9BigBOggjHTQxAR8/V1JGaF5cXGpCf1FpRmJGdld5fUVHYVZUTXxaS00DNHVRdkdlWX5dcHZURGJdVEFwXEdWYEs=');

// 2. TRADUÇÃO PARA PT-BR (Opcional, mas recomendado)
// Isso traduz os botões do Gantt, Grid e filtros automaticamente
ej.base.L10n.load({
    'pt-BR': {
        'grid': {
            'EmptyRecord': 'Nenhum registro encontrado',
            'GroupDropArea': 'Arraste um cabeçalho de coluna aqui para agrupar'
        },
        'gantt': {
            'emptyRecord': 'Nenhuma tarefa para exibir',
            'id': 'ID',
            'name': 'Nome',
            'startDate': 'Início',
            'endDate': 'Fim',
            'duration': 'Duração',
            'progress': 'Progresso',
        },
        'pager': {
            'currentPageInfo': '{0} de {1} páginas',
            'totalItemsInfo': '({0} itens)',
            'firstPageTooltip': 'Ir para primeira página',
            'lastPageTooltip': 'Ir para última página',
            'nextPageTooltip': 'Próxima página',
            'previousPageTooltip': 'Página anterior',
        }
    }
});

// Define o padrão brasileiro de cultura (datas e números)
ej.base.setCulture('pt-BR');
