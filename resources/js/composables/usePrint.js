// composables/usePrint.js
import { ref } from 'vue'

export function usePrint() {
    const isLoading = ref(false)

    /**
     * Main print handler function
     * Creates a new window with custom print layout
     */
    const handlePrint = (data, config = {}) => {
        isLoading.value = true

        try {
            // Generate the HTML content for printing
            const printContent = generatePrintHTML(data, config)

            // Open new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600')

            if (!printWindow) {
                alert('Please allow popups for this website to enable printing')
                isLoading.value = false
                return
            }

            // Write the complete HTML document to the new window
            printWindow.document.write(getCompleteHTMLDocument(printContent, config))

            // Close the document stream
            printWindow.document.close()

            // Wait for content to load, then print
            printWindow.onload = () => {
                printWindow.focus()
                printWindow.print()
                isLoading.value = false

                // Optional: Close window after printing
                // printWindow.onafterprint = () => printWindow.close()
            }

        } catch (error) {
            console.error('Print error:', error)
            alert('An error occurred while preparing the print document')
            isLoading.value = false
        }
    }

    /**
     * Generates the complete HTML document structure
     */
    const getCompleteHTMLDocument = (content, config = {}) => {
        const title = config.title || 'Print Document'
        const direction = config.direction || 'rtl'
        const lang = config.lang || 'ar'

        return `
            <!DOCTYPE html>
            <html lang="${lang}" dir="${direction}">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>${title}</title>
                ${getPrintStyles(config)}
            </head>
            <body>
                ${content}
            </body>
            </html>
        `
    }

    /**
     * Returns the CSS styles for the print layout
     */
    const getPrintStyles = (config = {}) => {
        const fontFamily = config.fontFamily || "'speda', Tahoma, Geneva, Verdana, sans-serif"
        const primaryColor = config.primaryColor || '#2c3e50'
        const accentColor = config.accentColor || '#27ae60'

        return `
            <style>
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }

                body {
                    font-family: ${fontFamily};
                    font-size: 12pt;
                    line-height: 1.2;
                    color: #333;
                    background: white;
                    padding: 10mm;
                    direction: ${config.direction || 'rtl'};
                    text-align: ${config.direction === 'ltr' ? 'left' : 'right'};
                }

                .print-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 15px;
                }

                .print-header h1 {
                    font-size: 22pt;
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: ${primaryColor};
                }

                .print-header h2 {
                    font-size: 17pt;
                    color: #34495e;
                    margin-bottom: 5px;
                }

                .print-date {
                    font-size: 10pt;
                    color: #7f8c8d;
                }

                .print-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 7px 15px;
                    margin-bottom: 10px;
                }

                .field-group {
                    margin-bottom: 10px;
                    break-inside: avoid;
                }

                .field-label {
                    font-weight: bold;
                    font-size: 11pt;
                    color: ${primaryColor};
                    margin-bottom: 5px;
                    display: block;
                }

                .field-value {
                    border-bottom: 1px solid #bdc3c7;
                    padding: 8px 5px;
                    font-size: 12pt;
                    min-height: 20px;
                    background-color: #f8f9fa;
                }

                .field-value.number {
                    text-align: left;
                    direction: ltr;
                    font-family: 'Courier New', monospace;
                    font-weight: bold;
                }

                .total-section {
                    background-color: #e8f5e8;
                    border: 2px solid ${accentColor};
                    border-radius: 5px;
                    padding: 10px;
                    margin: 15px 0;
                    text-align: center;
                }

                .total-section .field-label {
                    font-size: 14pt;
                    color: ${accentColor};
                }

                .total-section .field-value {
                    font-size: 16pt;
                    font-weight: bold;
                    background-color: white;
                    border: 1px solid ${accentColor};
                    border-radius: 3px;
                }

                .note-section {
                    margin-top: 10px;
                    page-break-inside: avoid;
                }

                .note-section .field-label {
                    font-size: 12pt;
                    margin-bottom: 5px;
                }

                .note-content {
                    border: 1px solid #bdc3c7;
                    border-radius: 5px;
                    padding: 10px;
                    min-height: 70px;
                    background-color: #f8f9fa;
                    white-space: pre-wrap;
                    font-size: 11pt;
                    line-height: 1.6;
                }

                .print-footer {
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 1px solid #bdc3c7;
                    font-size: 10pt;
                    color: #7f8c8d;
                    text-align: center;
                }

                /* Print-specific styles */
                @media print {
                    body {
                        padding: 15mm;
                        font-size: 11pt;
                    }

                    .print-grid {
                        grid-template-columns: repeat(3, 1fr);
                        gap: 7px 15px;
                        margin-bottom: 10px;
                    }

                    .field-group {
                        page-break-inside: avoid;
                    }

                    .total-section {
                        page-break-inside: avoid;
                    }

                    .note-section {
                        page-break-inside: avoid;
                    }
                }

                @page {
                    size: A4;
                    margin: 10mm;
                }
            </style>
        `
    }

    /**
     * Generates the main print content HTML for deposits
     */
    const generateDepositPrintHTML = (deposit) => {
        return `
            <div class="print-header">
                <h1>بڕی پارەی وەرگیراو</h1>
            </div>

            <div class="print-grid">
                ${generateFieldHTML('ناوی بازرگان', deposit.customer?.name)}
                ${generateFieldHTML('ژمارە مۆبایل ', deposit.customer?.mobile)}
                ${generateFieldHTML('ڕێکەوت و کاتی وەرگرتن', getDate(deposit.created_at))}
                ${generateFieldHTML('ڕێکەوت و کاتی وەرگرتن', getDate(deposit.created_at))}
            </div>

            <div class="total-section">
                <div class="field-label">کۆی گشتی</div>
                <div class="field-value number">${formatNumber(deposit.amount)} دینار</div>
            </div>

            <div class="note-section">
                <div class="field-label">تیبینی:</div>
                <div class="note-content">${escapeHtml(deposit.note || 'هیچ تیبینیەک نییە')}</div>
            </div>

            <div class="print-footer">
                <div>چاپکراوە لە: ${new Date().toLocaleString('en-GB')}</div>
            </div>
        `
    }

    /**
     * Generic print content generator
     */
    const generatePrintHTML = (data, config = {}) => {
        // If it's a deposit, use the specific template
        if (config.type === 'deposit' || data.customer) {
            return generateDepositPrintHTML(data)
        }

        // Generic template for other types
        return generateGenericPrintHTML(data, config)
    }

    /**
     * Generic print HTML generator
     */
    const generateGenericPrintHTML = (data, config = {}) => {
        const title = config.title || 'Document'
        const fields = config.fields || []

        let fieldsHTML = ''
        fields.forEach(field => {
            fieldsHTML += generateFieldHTML(field.label, data[field.key], field.isNumber)
        })

        return `
            <div class="print-header">
                <h1>${title}</h1>
            </div>

            <div class="print-grid">
                ${fieldsHTML}
            </div>

            ${config.showTotal && data.amount ? `
                <div class="total-section">
                    <div class="field-label">${config.totalLabel || 'Total'}</div>
                    <div class="field-value number">${formatNumber(data.amount)} ${config.currency || ''}</div>
                </div>
            ` : ''}

            ${data.note ? `
                <div class="note-section">
                    <div class="field-label">${config.noteLabel || 'Notes'}:</div>
                    <div class="note-content">${escapeHtml(data.note)}</div>
                </div>
            ` : ''}

            <div class="print-footer">
                <div>Printed on: ${new Date().toLocaleString('en-GB')}</div>
            </div>
        `
    }

    /**
     * Generates HTML for individual field
     */
    const generateFieldHTML = (label, value, isNumber = false) => {
        const displayValue = isNumber ? formatNumber(value) : (value || '---')
        const numberClass = isNumber ? 'number' : ''

        return `
            <div class="field-group">
                <label class="field-label">${escapeHtml(label)}:</label>
                <div class="field-value ${numberClass}">${escapeHtml(displayValue)}</div>
            </div>
        `
    }

    /**
     * Escapes HTML characters to prevent XSS
     */
    const escapeHtml = (text) => {
        if (!text) return ''
        const div = document.createElement('div')
        div.textContent = text
        return div.innerHTML
    }

    /**
     * Formats numbers with thousand separators
     */
    const formatNumber = (value) => {
        if (!value || value === '0' || value === 0) return '0'
        const numericValue = parseFloat(value.toString().replace(/,/g, ''))
        if (isNaN(numericValue)) return '0'
        return new Intl.NumberFormat('en-US').format(numericValue)
    }

    /**
     * Formats date
     */
    const getDate = (date) => {
        const d = new Date(date)
        const day = d.getDate()
        const month = d.getMonth() + 1
        const year = d.getFullYear()
        const hour = d.getHours()
        const minute = d.getMinutes()
        return `${year}-${month}-${day} ${hour}:${minute}`
    }

    // Convenience methods for different document types
    const printDeposit = (deposit, config = {}) => {
        const printConfig = {
            ...config,
            type: 'deposit',
            title: `پسوڵەی کاڵای - ${deposit.id || 'N/A'}`,
            direction: 'rtl',
            lang: 'ar'
        }
        handlePrint(deposit, printConfig)
    }

    const printInvoice = (invoice, config = {}) => {
        const printConfig = {
            ...config,
            type: 'invoice',
            title: `Invoice - ${invoice.id || 'N/A'}`,
            showTotal: true,
            totalLabel: 'Total Amount',
            currency: 'USD'
        }
        handlePrint(invoice, printConfig)
    }

    const printGeneric = (data, config = {}) => {
        handlePrint(data, config)
    }

    return {
        isLoading,
        handlePrint,
        printDeposit,
        printInvoice,
        printGeneric,
        formatNumber,
        getDate,
        escapeHtml
    }
}
