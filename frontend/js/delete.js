async function deleteStudent(id, button) {
    if (!window.confirm('Delete this student record? This cannot be undone.')) {
        return;
    }
    button.disabled = true;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('csrf', csrfToken);
    try {
        const data = await requestJSON('../backend/delete.php', {method: 'POST', body: formData});
        showMessage(data.message);
        await loadStudents();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        button.disabled = false;
    }
}

if (studentRows) {
    studentRows.addEventListener('click', function (event) {
        const button = event.target.closest('.delete-button');
        if (!button || !studentRows.contains(button) || button.disabled) {
            return;
        }
        deleteStudent(button.getAttribute('data-student-id'), button);
    });
}
