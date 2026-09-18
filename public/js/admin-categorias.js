const categoryForm = document.getElementById('categoryForm');
const categoryId = document.getElementById('categoryId');
const nombreInput = document.getElementById('nombre');
const descripcionInput = document.getElementById('descripcion');
const formTitle = document.getElementById('formTitle');
const btnSubmitCategory = document.getElementById('btnSubmitCategory');
const btnCancelEdit = document.getElementById('btnCancelEdit');

function editCategory(id, nombre, descripcion) {
    categoryId.value = id;
    nombreInput.value = nombre;
    descripcionInput.value = descripcion;

    formTitle.textContent = 'Editar Categoría';
    btnSubmitCategory.textContent = 'Actualizar Categoría';
    btnCancelEdit.style.display = 'inline-block';
}

btnCancelEdit.addEventListener('click', () => {
    resetCategoryForm();
});

function resetCategoryForm() {
    categoryId.value = '';
    categoryForm.reset();
    formTitle.textContent = 'Crear Nueva Categoría';
    btnSubmitCategory.textContent = 'Guardar Categoría';
    btnCancelEdit.style.display = 'none';
}

function deleteCategory(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            row.remove();
        }
    }
}