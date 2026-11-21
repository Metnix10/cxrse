document.addEventListener('DOMContentLoaded', () => {
    const levelsContainer = document.getElementById('levelsContainer');
    const btnAddLevel = document.getElementById('btnAddLevel');
    const form = document.querySelector('form');
    let levelCount = levelsContainer ? levelsContainer.querySelectorAll('.level-card').length || 1 : 1;

    if (btnAddLevel) {
        btnAddLevel.addEventListener('click', () => {
            levelCount++;

            const levelCard = document.createElement('div');
            levelCard.classList.add('level-card');
            levelCard.setAttribute('data-level', levelCount);
            levelCard.style.cssText = 'border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin-bottom: 20px; background-color: #f9fafb;';

            levelCard.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 class="level-title" style="margin: 0;">Nivel ${levelCount}</h3>
                    <button type="button" class="btn-delete-level" style="background-color: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Eliminar</button>
                </div>

                <div class="form-group" style="margin-bottom: 10px;">
                    <label>Título del Nivel / Lección</label>
                    <input type="text" name="niveles[${levelCount}][titulo]" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>

                <div class="form-group" style="margin-bottom: 10px;">
                    <label>Descripción del Nivel</label>
                    <textarea name="niveles[${levelCount}][descripcion]" rows="2" style="width: 100%; padding: 8px; margin-top: 5px;"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 10px;">
                    <label>Video de la Lección (MP4 / WebM - Obligatorio)</label>
                    <input type="file" name="niveles[${levelCount}][video]" accept="video/*" required style="margin-top: 5px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <div class="form-group">
                        <label>Precio individual (MXN, 0 si es gratis)</label>
                        <input type="number" name="niveles[${levelCount}][precio]" min="0" step="0.01" value="0" style="width: 100%; padding: 8px; margin-top: 5px;">
                    </div>
                    <div class="form-group">
                        <label>Material PDF (Opcional)</label>
                        <input type="file" name="niveles[${levelCount}][pdf]" accept="application/pdf" style="margin-top: 5px;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Enlace externo de apoyo (Opcional)</label>
                    <input type="url" name="niveles[${levelCount}][link_externo]" placeholder="https://..." style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>
            `;

            levelsContainer.appendChild(levelCard);

            levelCard.querySelector('.btn-delete-level').addEventListener('click', () => {
                levelCard.remove();
                reindexLevels();
            });
        });
    }

    function reindexLevels() {
        const cards = levelsContainer.querySelectorAll('.level-card');
        levelCount = 0;
        cards.forEach((card, index) => {
            levelCount = index + 1;
            card.setAttribute('data-level', levelCount);
            const titleEl = card.querySelector('.level-title');
            if (titleEl) titleEl.textContent = `Nivel ${levelCount}`;
        });
    }

    async function parsearRespuesta(response) {
        const texto = await response.text();
        if (!texto) {
            throw new Error(`El servidor devolvió una respuesta vacía (HTTP ${response.status}).`);
        }
        try {
            return JSON.parse(texto);
        } catch {
            throw new Error(`Respuesta inválida del servidor: ${texto}`);
        }
    }

    async function subirACloudinary(archivo) {
        const formData = new FormData();
        formData.append('archivo', archivo);

        const response = await fetch('../app/controllers/UploadController.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });

        const data = await parsearRespuesta(response);
        if (!response.ok) {
            throw new Error(data.error || 'Error al subir archivo a Cloudinary.');
        }
        return data.url_archivo;
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btnSubmit = form.querySelector('button[type="submit"]');
            const textoOriginalBtn = btnSubmit ? btnSubmit.textContent : 'Guardar Curso';

            const tituloEl = form.querySelector('[name="titulo"], #titulo');
            const descripcionEl = form.querySelector('[name="descripcion"], #descripcion');
            const categoriaEl = form.querySelector('[name="categoria_id"], [name="categoria"], #categoria_id, #categoria');
            const precioEl = form.querySelector('[name="precio"], #precio');
            const portadaInput = form.querySelector('input[type="file"]:not([name^="niveles"])');

            const titulo = tituloEl ? tituloEl.value.trim() : '';
            const descripcion = descripcionEl ? descripcionEl.value.trim() : '';
            const categoria_id = categoriaEl ? parseInt(categoriaEl.value, 10) : 0;
            const precio = precioEl ? parseFloat(precioEl.value) || 0 : 0;

            if (!titulo || !descripcion || !categoria_id) {
                alert('Por favor completa el título, descripción y categoría del curso.');
                return;
            }

            if (!portadaInput || portadaInput.files.length === 0) {
                alert('Por favor selecciona una imagen de portada.');
                return;
            }

            const cards = levelsContainer.querySelectorAll('.level-card');
            if (cards.length === 0) {
                alert('El curso debe tener al menos un nivel.');
                return;
            }

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Subiendo portada...';
            }

            try {
                const urlPortada = await subirACloudinary(portadaInput.files[0]);

                const nivelesPayload = [];
                for (let i = 0; i < cards.length; i++) {
                    const card = cards[i];
                    const titNivel = card.querySelector('input[name*="[titulo]"]')?.value.trim() || '';
                    const descNivel = card.querySelector('textarea[name*="[descripcion]"]')?.value.trim() || '';
                    const videoInput = card.querySelector('input[type="file"][name*="[video]"]');
                    const precioNivel = parseFloat(card.querySelector('input[name*="[precio]"]')?.value) || 0;
                    const pdfInput = card.querySelector('input[type="file"][name*="[pdf]"]');
                    const linkNivel = card.querySelector('input[name*="[link_externo]"]')?.value.trim() || '';

                    if (!titNivel || !videoInput || videoInput.files.length === 0) {
                        throw new Error(`El Nivel ${i + 1} debe tener título y video obligatorio.`);
                    }

                    if (btnSubmit) {
                        btnSubmit.textContent = `Subiendo video del Nivel ${i + 1} de ${cards.length}...`;
                    }
                    const urlVideo = await subirACloudinary(videoInput.files[0]);

                    let urlPdf = null;
                    if (pdfInput && pdfInput.files.length > 0) {
                        if (btnSubmit) {
                            btnSubmit.textContent = `Subiendo PDF del Nivel ${i + 1}...`;
                        }
                        urlPdf = await subirACloudinary(pdfInput.files[0]);
                    }

                    nivelesPayload.push({
                        titulo: titNivel,
                        descripcion: descNivel || null,
                        video_url: urlVideo,
                        precio: precioNivel,
                        pdf_url: urlPdf,
                        link_externo: linkNivel || null
                    });
                }

                if (btnSubmit) {
                    btnSubmit.textContent = 'Guardando curso...';
                }

                const response = await fetch('../app/controllers/CursoController.php?recurso=cursos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({
                        titulo,
                        descripcion,
                        categoria_id,
                        precio,
                        imagen: urlPortada,
                        niveles: nivelesPayload
                    })
                });

                const resultado = await parsearRespuesta(response);
                if (!response.ok) {
                    throw new Error(resultado.error || 'Error al guardar el curso.');
                }

                alert('¡Curso creado exitosamente!');
                window.location.href = 'index.php?url=dashboard-instructor';

            } catch (error) {
                console.error('Error al crear curso:', error);
                alert('Error: ' + error.message);
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = textoOriginalBtn;
                }
            }
        });
    }
});