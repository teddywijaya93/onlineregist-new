function initInputFilters() {
    document.querySelectorAll('.alphabet-only').forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^a-zA-Z\s]/g,'');
        });
    });
    
    document.querySelectorAll('.numeric-only').forEach(input => {
        const max = input.maxLength || 16;
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g,'').slice(0,max);
        });
    });
}