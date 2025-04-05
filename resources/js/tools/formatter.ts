export function formatFileSize(
    bytes: number,
    locale: string = 'pt-BR',
): string {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;

    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }

    return (
        new Intl.NumberFormat(locale, {
            minimumFractionDigits: 1,
            maximumFractionDigits: 1,
        }).format(bytes) +
        ' ' +
        units[i]
    );
}
