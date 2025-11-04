document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');

    if (successMessage) {
        const successAlert = document.getElementById('success-alert');
        successAlert.textContent = successMessage;
        successAlert.style.display = 'block';
        history.replaceState({}, document.title, window.location.pathname);
    }

    if (errorMessage) {
        const errorAlert = document.getElementById('error-alert');
        errorAlert.textContent = decodeURIComponent(errorMessage);
        errorAlert.style.display = 'block';
        history.replaceState({}, document.title, window.location.pathname);
    }
});