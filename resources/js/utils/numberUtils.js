export function formatNumber(value) {
    if (typeof value !== 'number') {
        value = Number(value);
    }

    if (isNaN(value)) return '';

    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

export function formatCurrency(value, currency = '$') {
    const formatted = formatNumber(value);
    return formatted ? `${currency}${formatted}` : '';
}

export function parseNumber(value) {
    return Number(value.toString().replace(/,/g, ''));
}

export function getDate(dateString) {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0]; // Returns YYYY-MM-DD
}
