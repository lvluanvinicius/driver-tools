export function transformSearchParams(query: Record<string, string | number>) {
    const urlParams = new URLSearchParams();

    for (const key in query) {
        const value = query[key];
        if (value) {
            if (Array.isArray(value)) {
                urlParams.append(key, value.join(','));
            } else {
                urlParams.append(key, value as string);
            }
        }
    }

    return urlParams.toString();
}

// Função de manipulação do filtro
export const handleFilter = ({
    filters: dataFilters,
}: {
    filters: {
        value: string;
        field: string;
        operator: string;
    }[];
}) => {
    // Inicializa um array para armazenar as partes da query string
    const queryParts: string[] = [];

    // Itera sobre cada objeto dentro do array filters
    dataFilters.forEach((filters, index) => {
        // Itera sobre as chaves do objeto filters
        const keys = Object.keys(filters);

        // Para cada chave, cria uma parte da query string
        keys.forEach((key) => {
            queryParts.push(
                `filters[${index}][${key}]=${encodeURIComponent(
                    filters[key as keyof typeof filters],
                )}`,
            );
        });
    });

    // Concatena todas as partes em uma string de consulta única
    const queryString = queryParts.join('&');

    return queryString;
};
