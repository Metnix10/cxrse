document.addEventListener('DOMContentLoaded', function () {
    const categoryForm = document.getElementById('categoryForm');
    const categoryId = document.getElementById('categoryId');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const formTitle = document.getElementById('formTitle');
    const btnSubmitCategory = document.getElementById('btnSubmitCategory');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const tableBody = document.getElementById('categoriesTableBody');

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    async function cargarCategorias() {
        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=categorias', { credentials: 'include' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Error al cargar categorías.');

            if (!Array.isArray(data) || data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No hay categorías registradas.</td></tr>';
                return;
            }

            tableBody.innerHTML = data.map(cat => `
                <tr>
                    <td><strong>${escaparHTML(cat.nombre)}</strong></td>
                    <td>${escaparHTML(cat.descripcion || '')}</td>
                    <td>${escaparHTML(cat.fecha_creacion)}</td>
                    <td>
                        <button type="button" class="btn-action btn-edit"
                            data-id="${cat.id}"
                            data-nombre="${escaparHTML(cat.nombre)}"
                            data-desc="${escaparHTML(cat.descripcion || '')}">Editar</button>
                        <button type="button" class="btn-action btn-delete" data-id="${cat.id}">Eliminar</button>
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    categoryId.value = btn.getAttribute('data-id');
                    nombreInput.value = btn.getAttribute('data-nombre');
                    descripcionInput.value = btn.getAttribute('data-desc');
                    formTitle.textContent = 'Editar Categoría';
                    btnSubmitCategory.textContent = 'Actualizar Categoría';
                    btnCancelEdit.style.display = 'inline-block';
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', () => eliminarCategoria(btn.getAttribute('data-id')));
            });
        } catch (error) {
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    function resetCategoryForm() {
        categoryId.value = '';
        categoryForm.reset();
        formTitle.textContent = 'Crear Nueva Categoría';
        btnSubmitCategory.textContent = 'Guardar Categoría';
        btnCancelEdit.style.display = 'none';
    }

    btnCancelEdit.addEventListener('click', resetCategoryForm);

    categoryForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = categoryId.value.trim();
        const payload = {
            id: id ? parseInt(id, 10) : null,
            nombre: nombreInput.value.trim(),
            descripcion: descripcionInput.value.trim()
        };

        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=categorias', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo guardar la categoría.');

            alert(data.mensaje);
            resetCategoryForm();
            cargarCategorias();
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    async function eliminarCategoria(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) return;

        try {
            const res = await fetch(`../app/controllers/AdminController.php?recurso=categorias&id=${id}`, {
                method: 'DELETE',
                credentials: 'include'
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo eliminar.');

            alert(data.mensaje);
            cargarCategorias();
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    cargarCategorias();
});