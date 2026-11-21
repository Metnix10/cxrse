document.addEventListener('DOMContentLoaded', function () {
    const profileForm = document.getElementById('profileForm');
    const nombreInput = document.getElementById('nombre');
    const generoSelect = document.getElementById('genero');
    const fechaNacInput = document.getElementById('fechaNacimiento');
    const emailInput = document.getElementById('email');
    const fotoInput = document.getElementById('fotoInput');
    const passwordInput = document.getElementById('password');
    const avatarPreview = document.getElementById('avatarPreview');
    const btnSave = document.getElementById('btnSaveProfile');
    const feedback = document.getElementById('profileFeedback');

    let fotoActualUrl = '';

    async function cargarPerfil() {
        try {
            const res = await fetch('../app/controllers/PerfilController.php', { credentials: 'include' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo cargar el perfil.');

            nombreInput.value = data.nombre || '';
            generoSelect.value = data.genero || 'Otro';
            fechaNacInput.value = data.fecha_nacimiento || '';
            emailInput.value = data.email || '';

            document.getElementById('summaryName').textContent = data.nombre || '';
            document.getElementById('summaryRole').textContent = data.rol || '';
            document.getElementById('summaryEmail').textContent = data.email || '';
            document.getElementById('summaryDate').textContent = data.fecha_registro || '';

            if (data.foto && data.foto.startsWith('http')) {
                fotoActualUrl = data.foto;
                avatarPreview.src = data.foto;
            }
        } catch (error) {
            feedback.style.color = '#dc2626';
            feedback.textContent = error.message;
        }
    }

    fotoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            avatarPreview.src = URL.createObjectURL(file);
        }
    });

    async function subirImagenCloudinary(archivo) {
        const formData = new FormData();
        formData.append('archivo', archivo);
        formData.append('tipo', 'imagen');

        const res = await fetch('../app/controllers/UploadController.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });
        
        const texto = await res.text();
        let data;
        try {
            data = JSON.parse(texto);
        } catch (e) {
            throw new Error('Respuesta inválida del servidor al subir imagen: ' + texto);
        }

        if (!res.ok) throw new Error(data.error || 'Error al subir la fotografía.');
        
        const urlSubida = data.url_imagen || data.url_archivo || data.url || data.secure_url;
        if (!urlSubida) {
            throw new Error('Cloudinary no devolvió una URL válida.');
        }
        return urlSubida;
    }

    profileForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        feedback.textContent = '';
        btnSave.disabled = true;
        btnSave.textContent = 'Guardando...';

        try {
            let urlFotoFinal = fotoActualUrl;
            if (fotoInput.files && fotoInput.files[0]) {
                urlFotoFinal = await subirImagenCloudinary(fotoInput.files[0]);
            }

            const payload = {
                nombre: nombreInput.value.trim(),
                genero: generoSelect.value,
                fecha_nacimiento: fechaNacInput.value,
                foto: urlFotoFinal,
                password: passwordInput.value
            };

            const res = await fetch('../app/controllers/PerfilController.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo actualizar el perfil.');

            alert(data.mensaje || 'Perfil actualizado correctamente.');
            window.location.reload();
        } catch (error) {
            feedback.style.color = '#dc2626';
            feedback.textContent = error.message;
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Guardar Cambios';
        }
    });

    cargarPerfil();
});