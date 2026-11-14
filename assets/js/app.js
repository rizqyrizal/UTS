document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('projectSearch');
  if (search) {
    search.addEventListener('input', () => {
      const term = search.value.toLowerCase();
      document.querySelectorAll('#projectTable tbody tr').forEach((row) => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

  const modal = document.getElementById('editProject');
  if (modal) {
    modal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      if (!button) return;
      modal.querySelector('#edit-id').value = button.dataset.id;
      modal.querySelector('.project-field-name').value = button.dataset.name;
      modal.querySelector('.project-field-category').value = button.dataset.category;
      modal.querySelector('.project-field-status').value = button.dataset.status;
      modal.querySelector('.project-field-deadline').value = button.dataset.deadline;
      modal.querySelector('.project-field-owner').value = button.dataset.owner;
    });
  }

  const createModal = document.getElementById('createProject');
  if (createModal) {
    createModal.addEventListener('shown.bs.modal', () => {
      createModal.querySelector('.project-field-deadline').value = new Date().toISOString().slice(0, 10);
    });
  }
});
