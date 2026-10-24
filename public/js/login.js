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

    
    const credenciales = {
        email: email,
        password: password
    };

   
    fetch('../app/controllers/AuthController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(credenciales)
    })
    .then(respuesta => {
        if (!respuesta.ok) {
            return respuesta.json().then(err => { throw new Error(err.error); });
        }
        return respuesta.json(); 
    })
    .then(datos => {
        
        alert(datos.mensaje + ". Bienvenido " + datos.usuario.nombre);
        
      
        window.location.href = 'index.php?url=home';
    })
    .catch(error => {
        
        console.error('Error de servidor:', error);
        mostrarError(error.message); 
    });
});

function mostrarError(mensaje) {
    const errorMsg = document.getElementById('error-msg');
    errorMsg.innerText = mensaje;
    errorMsg.style.display = 'block';
}