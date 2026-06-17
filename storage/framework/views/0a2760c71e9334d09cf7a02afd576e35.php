<script>
(() => {
    const amountInput = document.querySelector('input[name="amount"]');
    const taxInput = document.querySelector('input[name="tax"]');
    const totalInput = document.querySelector('input[name="total"]');
    if (!amountInput || !taxInput || !totalInput) {
        return;
    }

    const bookingSelect = document.getElementById('booking_id');
    const invoiceNumberInput = document.getElementById('invoice_number');
    const defaultInvoiceNumber = invoiceNumberInput?.value || <?php echo json_encode($invoiceNumber ?? '', 15, 512) ?>;

    const toNumber = (value) => {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    };

    const syncTotal = () => {
        const total = toNumber(amountInput.value) + toNumber(taxInput.value);
        totalInput.value = total.toFixed(2);
    };

    const applyBookingDefaults = () => {
        if (!bookingSelect) {
            return;
        }

        const selected = bookingSelect.selectedOptions?.[0];
        if (!selected) {
            return;
        }

        const bookingAmount = toNumber(selected.dataset.amount);
        if (bookingAmount > 0) {
            amountInput.value = bookingAmount.toFixed(2);
        }

        if (invoiceNumberInput && !invoiceNumberInput.value) {
            invoiceNumberInput.value = defaultInvoiceNumber;
        }

        syncTotal();
    };

    bookingSelect?.addEventListener('change', applyBookingDefaults);
    amountInput.addEventListener('input', syncTotal);
    taxInput.addEventListener('input', syncTotal);
    applyBookingDefaults();
})();
</script>
<?php /**PATH F:\xampp\htdocs\Rm1\resources\views/invoices/_total-calculator.blade.php ENDPATH**/ ?>