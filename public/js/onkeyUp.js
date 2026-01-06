document.addEventListener('keydown', preventArrowKeys);

function preventArrowKeys(event) {
    // Prevent the default behavior for arrow keys (38 for up arrow, 40 for down arrow)
    if (event.keyCode === 38 || event.keyCode === 40) {
        event.preventDefault();
    }
}