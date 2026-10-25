function initializeAddForm() {
    const saveButton = document.getElementById('save-student');
    if (page !== 'add' || !studentForm || !saveButton) {
        return;
    }
    studentForm.addEventListener('submit', submitStudent);
    saveButton.disabled = false;
}
