document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    const errorMsg = document.getElementById('error-msg');
    errorMsg.style.display = 'none';
    errorMsg.innerText = '';

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) {
        mostrarError('Todos los campos son obligatorios.');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarError('Ingresa un formato de correo válido.');
        return;
    }

    alert('¡Inicio de sesión exitoso!');
    window.location.href = 'index.html';
});

function mostrarError(mensaje) {
    const errorMsg = document.getElementById('error-msg');
    errorMsg.innerText = mensaje;
    errorMsg.style.display = 'block';
}