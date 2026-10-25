function showEmptyRow(message) {
    studentRows.textContent = '';
    const row = document.createElement('tr');
    const cell = document.createElement('td');
    cell.colSpan = 6;
    cell.className = 'empty';
    const text = document.createElement('span');
    text.textContent = message;
    cell.appendChild(text);
    row.appendChild(cell);
    studentRows.appendChild(row);
}

function renderStudents(students) {
    const yearLabels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    studentRows.textContent = '';
    for (const student of students) {
        const row = document.createElement('tr');
        const values = [student.student_number, student.first_name + ' ' + student.last_name,
            student.email, student.course, yearLabels[student.year_level - 1]];
        const classes = ['student-id', 'student-name', '', '', 'year-level'];
        for (let index = 0; index < values.length; index++) {
            const cell = document.createElement('td');
            cell.className = classes[index];
            cell.textContent = values[index];
            row.appendChild(cell);
        }
        const actionCell = document.createElement('td');
        actionCell.innerHTML = '<div class="actions"><a class="button edit-button"><img class="button-icon" src="assets/pencil.png" width="16" height="16" alt=""> Edit</a><button class="button delete-button" type="button"><img class="button-icon" src="assets/trashcan.png" width="16" height="16" alt=""> Delete</button></div>';
        const editLink = actionCell.querySelector('.edit-button');
        editLink.href = 'edit.html?v=student-id-2&id=' + encodeURIComponent(student.id);
        editLink.setAttribute('aria-label', 'Edit ' + student.first_name + ' ' + student.last_name);
        const deleteButton = actionCell.querySelector('.delete-button');
        deleteButton.setAttribute('aria-label', 'Delete ' + student.first_name + ' ' + student.last_name);
        deleteButton.setAttribute('data-student-id', student.id);
        row.appendChild(actionCell);
        studentRows.appendChild(row);
    }
    if (students.length === 0) {
        let message = 'No student records yet. Select Add Student to get started.';
        if (getSearchTerm() !== '') {
            message = 'No matching students found. Try another search.';
        }
        showEmptyRow(message);
    }

}

async function loadStudents() {
    const search = getSearchTerm();
    const data = await requestJSON('../backend/read.php?q=' + encodeURIComponent(search));
    csrfToken = data.csrf;
    renderStudents(data.students);
}

