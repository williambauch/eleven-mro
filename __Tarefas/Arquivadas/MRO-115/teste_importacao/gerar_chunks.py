import openpyxl

wb = openpyxl.load_workbook(r'c:\xampp\htdocs\MRO_System\__Tarefas\Arquivadas\MRO-115\COMPRAS-PRODUTO_EMPENHADOS_POR_PROJETO.xlsx', read_only=True)
ws = wb.active
codes = set()
for row in ws.iter_rows(min_row=2, values_only=True):
    if row[0] is not None:
        codes.add(str(row[0]).strip())
codes = sorted(codes)
print('Total de task codes distintos no arquivo:', len(codes))

# Gera chunks de 100 para as queries
for i in range(0, len(codes), 100):
    chunk = codes[i:i+100]
    in_clause = ','.join("'" + c + "'" for c in chunk)
    print('--- CHUNK ' + str(i // 100) + ' (' + str(len(chunk)) + ' codes) ---')
    print(in_clause)
