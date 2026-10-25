let page = document.body.getAttribute('data-page');
let parameters = new URLSearchParams(window.location.search);
const searchInput = document.querySelector('.search input');
const studentRows = document.getElementById('student-rows');
let studentForm = document.getElementById('student-form');
let studentModal = document.getElementById('student-modal');
let csrfToken = '';
const dashboardPage = page === 'dashboard';
let openingModal = false;
let modalTrigger = null;
let pageReady;

async function requestJSON(url, options = {}) {
    const response = await fetch(url, options);
    let data;
    try {
        data = await response.json();
    } catch (error) {
        throw new Error('The server could not return student data. Check Apache and MySQL.');
    }
    if (!response.ok || !data.ok) {
        let message = data.message || 'The request could not be completed.';
        if (data.errors) {
            message = data.errors.join('\n');
        }
        throw new Error(message);
    }
    return data;
}

function showMessage(message, isError = false) {
    let box = document.getElementById('page-message');
    if (isError && studentForm) {
        box = document.getElementById('form-message');
    }
    if (!box) {
        return;
    }
    box.textContent = message;
    box.className = isError ? 'alert error' : 'alert success';
    box.hidden = !message;
}

async function submitStudent(event) {
    event.preventDefault();
    const textFields = studentForm.querySelectorAll('input:not([type="hidden"])');
    for (const field of textFields) {
        field.value = field.value.trim();
    }
    if (!studentForm.reportValidity()) {
        return;
    }
    const saveButton = document.getElementById('save-student');
    saveButton.disabled = true;
    document.getElementById('form-message').hidden = true;
    try {
        const formData = new FormData(studentForm);
        const data = await requestJSON(studentForm.action, {method: 'POST', body: formData});
        sessionStorage.setItem('student-records-message', data.message);
        window.location.href = 'dashboard.html';
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        saveButton.disabled = false;
    }
}

function closeStudentModal(event) {
    event.preventDefault();
    if (!dashboardPage) {
        window.location.href = 'dashboard.html';
        return;
    }
    studentModal.close();
    studentModal.remove();
    studentModal = null;
    studentForm = null;
    page = 'dashboard';
    parameters = new URLSearchParams(window.location.search);
    document.body.classList.remove('modal-open');
    modalTrigger.focus();
}

async function initializePage() {
    if (!studentRows) {
        return;
    }
    initializeSearch();

    if (studentModal && studentForm) {
        studentModal.close();
        studentModal.showModal();
        studentModal.addEventListener('cancel', closeStudentModal);
    }
    try {
        await loadStudents();
        if (studentForm) {
            studentForm.elements.csrf.value = csrfToken;
            if (page === 'add') {
                initializeAddForm();
            } else if (page === 'edit') {
                await initializeEditForm();
            }
        } else {
            const message = sessionStorage.getItem('student-records-message');
            if (message) {
                showMessage(message);
                sessionStorage.removeItem('student-records-message');
            }
        }
    } catch (error) {
        showMessage(error.message, true);
        if (!csrfToken) {
            showEmptyRow('Student records could not be loaded. Please try again.');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    pageReady = initializePage();
});

async function openStudentModal(link) {
    if (openingModal || studentModal) {
        return;
    }
    openingModal = true;
    modalTrigger = link;
    try {
        await pageReady;
        const response = await fetch(link.href);
        if (!response.ok) {
            throw new Error('Could not load the student form. Please try again.');
        }
        const html = new DOMParser().parseFromString(await response.text(), 'text/html');
        const dialog = html.getElementById('student-modal');
        const formPage = html.body.getAttribute('data-page');
        if (!dialog || (formPage !== 'add' && formPage !== 'edit')) {
            throw new Error('Could not load the student form. Please try again.');
        }
        const scriptPath = 'js/' + formPage + '.js';
        if (!document.querySelector('script[src="' + scriptPath + '"]')) {
            await new Promise(function (resolve, reject) {
                const script = document.createElement('script');
                script.src = scriptPath;
                script.onload = resolve;
                script.onerror = function () {
                    script.remove();
                    reject(new Error('Could not load the student form script.'));
                };
                document.head.appendChild(script);
            });
        }
        dialog.removeAttribute('open');
        document.body.appendChild(dialog);
        studentModal = dialog;
        studentForm = dialog.querySelector('form');
        page = formPage;
        parameters = new URL(link.href).searchParams;
        studentForm.elements.csrf.value = csrfToken;
        studentModal.addEventListener('cancel', closeStudentModal);
        try {
            if (page === 'add') {
                initializeAddForm();
            } else {
                await initializeEditForm();
            }
        } catch (error) {
            showMessage(error.message, true);
        }
        document.body.classList.add('modal-open');
        studentModal.showModal();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        openingModal = false;
    }
}

if (dashboardPage) {
    document.addEventListener('click', function (event) {
        if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }
        const link = event.target.closest('a');
        if (!link) {
            return;
        }
        if (studentModal && studentModal.contains(link) && link.matches('.modal-close, .modal-footer > a.neutral')) {
            closeStudentModal(event);
        } else if (link.matches('.records-toolbar > a, .edit-button')) {
            event.preventDefault();
            openStudentModal(link);
        }
    });
}
