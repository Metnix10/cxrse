document.getElementById('registroForm').addEventListener('submit', function(event) {
            event.preventDefault(); 
            
            const errorMsg = document.getElementById('error-msg');
            errorMsg.style.display = 'none';
            errorMsg.innerText = '';

            const nombre = document.getElementById('nombre').value.trim();
            const genero = document.getElementById('genero').value;
            const fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
            const foto = document.getElementById('foto').value;
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!nombre || !genero || !fecha_nacimiento || !foto || !email || !password) {
                mostrarError('Todos los campos son obligatorios.');
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

            alert('El registro ha sido un éxito.');
        });

        function mostrarError(mensaje) {
            const errorMsg = document.getElementById('error-msg');
            errorMsg.innerText = mensaje;
            errorMsg.style.display = 'block';
        }