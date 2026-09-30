/**
 * Client-Side Form Validation & Draft Storage
 * Course: CSE 472 Web and Internet Programming Lab
 * References: Lab Manual 03 (DOM Interactivity), Lab Manual 04 (Validation & LocalStorage)
 */

document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 1. Skill Form Client-Side Validation & LocalStorage Draft
    // ----------------------------------------------------
    const skillForm = document.getElementById('skillForm');
    if (skillForm) {
        const titleInput = document.getElementById('title');
        const descInput  = document.getElementById('description');
        const availInput = document.getElementById('availability');
        const catSelect  = document.getElementById('category');

        // Only restore draft if creating a new listing (not editing an existing one)
        const isEditMode = document.getElementById('skillId') && document.getElementById('skillId').value !== '';
        
        if (!isEditMode) {
            const savedDraft = localStorage.getItem('cse472_skill_draft');
            if (savedDraft) {
                try {
                    const draft = JSON.parse(savedDraft);
                    if (draft.title && !titleInput.value) titleInput.value = draft.title;
                    if (draft.description && !descInput.value) descInput.value = draft.description;
                    if (draft.availability && !availInput.value) availInput.value = draft.availability;
                    if (draft.category && !catSelect.value) catSelect.value = draft.category;

                    const draftNotice = document.getElementById('draftNotice');
                    if (draftNotice) {
                        draftNotice.style.display = 'block';
                    }
                } catch (e) {
                    console.error('Failed to parse draft JSON', e);
                }
            }

            // Auto-save draft as student types (Lab 04 concept: JSON.stringify + localStorage.setItem)
            const autoSaveFields = [titleInput, descInput, availInput, catSelect];
            autoSaveFields.forEach(field => {
                if (field) {
                    field.addEventListener('input', function () {
                        const draftObj = {
                            title: titleInput ? titleInput.value : '',
                            description: descInput ? descInput.value : '',
                            availability: availInput ? availInput.value : '',
                            category: catSelect ? catSelect.value : '',
                            savedAt: new Date().toISOString()
                        };
                        localStorage.setItem('cse472_skill_draft', JSON.stringify(draftObj));
                    });
                }
            });
        }

        // Validate on Submit
        skillForm.addEventListener('submit', function (e) {
            let isValid = true;
            let errorMessage = '';

            const title = titleInput.value.trim();
            const category = catSelect.value.trim();
            const desc = descInput.value.trim();
            const avail = availInput.value.trim();

            if (title.length < 3) {
                isValid = false;
                errorMessage = 'Please provide a descriptive skill title (at least 3 characters).';
                titleInput.focus();
            } else if (!category) {
                isValid = false;
                errorMessage = 'Please select a skill category from the dropdown.';
                catSelect.focus();
            } else if (desc.length < 10) {
                isValid = false;
                errorMessage = 'Please write a brief description of at least 10 characters.';
                descInput.focus();
            } else if (!avail) {
                isValid = false;
                errorMessage = 'Please specify your general availability.';
                availInput.focus();
            }

            if (!isValid) {
                e.preventDefault();
                showClientError(errorMessage);
            } else {
                // Clear draft upon valid submission
                localStorage.removeItem('cse472_skill_draft');
            }
        });
    }

    // ----------------------------------------------------
    // 2. Auth Form Client-Side Validation
    // ----------------------------------------------------
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const studentId = document.getElementById('regStudentId').value.trim();
            const fullName  = document.getElementById('regFullName').value.trim();
            const email     = document.getElementById('regEmail').value.trim();
            const dept      = document.getElementById('regDepartment').value.trim();
            const pass      = document.getElementById('regPassword').value;
            const confirm   = document.getElementById('regConfirmPassword').value;

            let error = '';

            if (!fullName || !studentId || !email || !dept || !pass) {
                error = 'All fields are required for student registration.';
            } else if (!validateEmail(email)) {
                error = 'Please enter a valid university email address format.';
            } else if (pass.length < 6) {
                error = 'Password must be at least 6 characters long.';
            } else if (pass !== confirm) {
                error = 'Passwords do not match.';
            }

            if (error) {
                e.preventDefault();
                showClientError(error);
            }
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const loginId = document.getElementById('loginId').value.trim();
            const pass    = document.getElementById('loginPassword').value;

            if (!loginId || !pass) {
                e.preventDefault();
                showClientError('Please enter both your Student ID/Email and your Password.');
            }
        });
    }
});

/**
 * Standard Email Validation Regex
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Helper to display client-side validation errors dynamically
 */
function showClientError(message) {
    let errorBox = document.getElementById('clientErrorAlert');
    if (!errorBox) {
        errorBox = document.createElement('div');
        errorBox.id = 'clientErrorAlert';
        errorBox.className = 'alert alert-danger';
        const container = document.querySelector('.content-container');
        if (container) {
            container.insertBefore(errorBox, container.firstChild);
        }
    }
    errorBox.innerHTML = `<span><strong>Validation Notice:</strong> ${message}</span>
                         <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>`;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
