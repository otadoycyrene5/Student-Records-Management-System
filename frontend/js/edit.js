async function initializeEditForm() {
    const saveButton = document.getElementById('save-student');
    if (page !== 'edit' || !studentForm || !saveButton) {
        return;
    }
    const id = parameters.get('id');
    if (!id) {
        throw new Error('Select a student from the records table before editing.');
    }
    const data = await requestJSON('../backend/read.php?id=' + encodeURIComponent(id));
    const student = data.student;
    const fields = ['id', 'student_number', 'first_name', 'last_name', 'email', 'course', 'year_level'];
    for (const field of fields) {
        studentForm.elements[field].value = student[field];
    }
    studentForm.elements.csrf.value = data.csrf;
    studentForm.addEventListener('submit', submitStudent);
    saveButton.disabled = false;
}
