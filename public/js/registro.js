document.getElementById('registroForm').addEventListener('submit', async function(event) {
    event.preventDefault(); 
    
    const errorMsg = document.getElementById('error-msg');
    errorMsg.style.display = 'none';
    errorMsg.innerText = '';

    const nombre = document.getElementById('nombre').value.trim();
    const genero = document.getElementById('genero').value;
    const fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    const fotoInput = document.getElementById('foto');
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    
    if (!nombre || !genero || !fecha_nacimiento || fotoInput.files.length === 0 || !email || !password) {
        mostrarError('Todos los campos son obligatorios, incluyendo la foto de perfil.');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarError('Ingresa un formato de correo válido.');
        return;
    }

    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+={}\[\]:;"'<>,.?/-]).{8,}$/;
    if (!passwordRegex.test(password)) {
        mostrarError('La contraseña no cumple con los requisitos.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('imagen', fotoInput.files[0]);

        const uploadResponse = await fetch('../app/controllers/UploadController.php', {
            method: 'POST',
            body: formData 
          
        });

        if (!uploadResponse.ok) {
            const err = await uploadResponse.json();
            throw new Error(err.error || 'Error al subir la imagen.');
        }

        const uploadData = await uploadResponse.json();
        const urlImagenCloudinary = uploadData.url_imagen;

        
        const datosUsuario = {
            nombre: nombre,
            genero: genero,
            fecha_nacimiento: fecha_nacimiento,
            foto: urlImagenCloudinary, 
            email: email,
            password: password,
            rol: 'Estudiante' 
        };

        const registroResponse = await fetch('../app/controllers/RegistroController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosUsuario)
        });

        if (!registroResponse.ok) {
            const err = await registroResponse.json();
            throw new Error(err.error || 'Error al registrar usuario en la base de datos.');
        }

        const registroData = await registroResponse.json();
        
        
        alert(registroData.mensaje); 
        document.getElementById('registroForm').reset(); 

    } catch (error) {
        console.error('Error del proceso:', error);
        mostrarError(error.message); 
    }
});

function mostrarError(mensaje) {
    const errorMsg = document.getElementById('error-msg');
    errorMsg.innerText = mensaje;
    errorMsg.style.display = 'block';
}