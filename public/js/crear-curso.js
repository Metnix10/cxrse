document.addEventListener('DOMContentLoaded', () => {
    const levelsContainer = document.getElementById('levelsContainer');
    const btnAddLevel = document.getElementById('btnAddLevel');
    let levelCount = 1;

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

            <div class="form-group">
                <label>Video de la Lección (MP4 / WebM)</label>
                <input type="file" name="niveles[${levelCount}][video]" accept="video/*" required style="margin-top: 5px;">
            </div>
        `;

        levelsContainer.appendChild(levelCard);

        levelCard.querySelector('.btn-delete-level').addEventListener('click', () => {
            levelCard.remove();
            reindexLevels();
        });
    });

    function reindexLevels() {
        const cards = levelsContainer.querySelectorAll('.level-card');
        levelCount = 0;
        cards.forEach((card, index) => {
            levelCount = index + 1;
            card.setAttribute('data-level', levelCount);
            card.querySelector('.level-title').textContent = `Nivel ${levelCount}`;
        });
    }
});