document.addEventListener('DOMContentLoaded', function () {
    initInputFilters();
});

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

function validateRequired(id, message) {
    const el = document.getElementById(id);
    if (!el) return true;

    const value = el.value?.trim();

    if (!value) {
        showError(el, message);
        return false;
    }

    return true;
}

function showError(el, message) {
    el.classList.add("input-error");

    let error = el.nextElementSibling;

    if (!error || !error.classList.contains("error-text")) {
        error = document.createElement("div");
        error.className = "error-text";
        el.parentNode.appendChild(error);
    }

    error.innerText = message;
}

function clearErrors() {
    document.querySelectorAll(".input-error").forEach(el => {
        el.classList.remove("input-error");
    });

    document.querySelectorAll(".error-text").forEach(el => el.remove());
}

function clearFieldError(el) {
    el.classList.remove("input-error");

    const error = el.nextElementSibling;
    if (error && error.classList.contains("error-text")) {
        error.remove();
    }
}

function scrollToFirstError() {
    const firstError = document.querySelector(".input-error");
    if (!firstError) return;

    firstError.scrollIntoView({
        behavior: "smooth",
        block: "center"
    });

    firstError.focus();
}