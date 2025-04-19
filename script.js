document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('loginForm')) {
        document.getElementById('loginForm').addEventListener('submit', validateLoginForm);
    }
    
    if (document.getElementById('registerForm')) {
        document.getElementById('registerForm').addEventListener('submit', validateRegisterForm);
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect) {
            roleSelect.addEventListener('change', toggleRegistrationFields);
            toggleRegistrationFields();
        }
    }
    
    if (document.getElementById('appointmentForm')) {
        document.getElementById('appointmentForm').addEventListener('submit', validateAppointmentForm);
    }
});


function validateLoginForm(event) {
    const username = document.querySelector('#loginForm input[name="username"]').value.trim();
    const password = document.querySelector('#loginForm input[name="password"]').value.trim();
    let isValid = true;
    
    clearErrors('#loginForm');
    
    if (!username) {
        showError('username', 'Username is required', '#loginForm');
        isValid = false;
    } else if (username.length < 4) {
        showError('username', 'Username must be at least 4 characters', '#loginForm');
        isValid = false;
    }
    
    if (!password) {
        showError('password', 'Password is required', '#loginForm');
        isValid = false;
    } else if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters', '#loginForm');
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
}

function validateRegisterForm(event) {
    const form = document.getElementById('registerForm');
    const role = form.querySelector('select[name="role"]').value;
    let isValid = true;
 
    clearErrors('#registerForm');

    if (!validateField(form, 'username', 'Username is required')) isValid = false;
    if (!validateField(form, 'password', 'Password is required')) isValid = false;
    if (!validateField(form, 'email', 'Email is required')) isValid = false;
    if (!validateField(form, 'full_name', 'Full name is required')) isValid = false;
    
    const password = form.querySelector('input[name="password"]').value;
    if (password && password.length < 6) {
        showError('password', 'Password must be at least 6 characters', '#registerForm');
        isValid = false;
    }

    const email = form.querySelector('input[name="email"]').value;
    if (email && !validateEmail(email)) {
        showError('email', 'Please enter a valid email address', '#registerForm');
        isValid = false;
    }

    if (role === 'patient') {
        if (!validateField(form, 'phone', 'Phone number is required', '#patientFields')) isValid = false;
    } 
    else if (role === 'doctor') {
        if (!validateField(form, 'specialization', 'Specialization is required', '#doctorFields')) isValid = false;
        if (!validateField(form, 'qualification', 'Qualification is required', '#doctorFields')) isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
}

function validateAppointmentForm(event) {
    const form = document.getElementById('appointmentForm');
    let isValid = true;

    clearErrors('#appointmentForm');

    if (!validateField(form, 'doctor_id', 'Doctor is required')) isValid = false;
    if (!validateField(form, 'appointment_date', 'Date is required')) isValid = false;
    if (!validateField(form, 'appointment_time', 'Time is required')) isValid = false;

    const dateInput = form.querySelector('input[name="appointment_date"]');
    if (dateInput && dateInput.value) {
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showError('appointment_date', 'Appointment date cannot be in the past', '#appointmentForm');
            isValid = false;
        }
    }
    
    if (!isValid) {
        event.preventDefault();
    }
}

function toggleRegistrationFields() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('patientFields').style.display = 'none';
    document.getElementById('doctorFields').style.display = 'none';
    
    if (role === 'patient') {
        document.getElementById('patientFields').style.display = 'block';
    } else if (role === 'doctor') {
        document.getElementById('doctorFields').style.display = 'block';
    }
}

function validateField(form, fieldName, errorMessage, container = null) {
    const field = form.querySelector(`[name="${fieldName}"]`);
    if (!field) return true;
    
    const value = field.value.trim();
    if (!value) {
        showError(fieldName, errorMessage, container || form.id);
        return false;
    }
    return true;
}

function showError(fieldName, message, formId) {
    const existingError = document.querySelector(`#${formId} .error-${fieldName}`);
    if (existingError) existingError.remove();
    const errorElement = document.createElement('div');
    errorElement.className = `error error-${fieldName}`;
    errorElement.textContent = message;
    errorElement.style.color = 'red';
    errorElement.style.marginTop = '5px';
    errorElement.style.fontSize = '0.9em';
    const field = document.querySelector(`#${formId} [name="${fieldName}"]`);
    if (field) {
        field.closest('.form-group').appendChild(errorElement);
        field.style.borderColor = 'red';
    }
}

function clearErrors(formId) {
    const errors = document.querySelectorAll(`${formId} .error`);
    errors.forEach(error => error.remove());
 
    const fields = document.querySelectorAll(`${formId} input, ${formId} select`);
    fields.forEach(field => {
        field.style.borderColor = '';
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}


function validatePhone(phone) {
    const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
    return re.test(phone);
}


function validateContactForm() {
    const form = document.getElementById('contactForm');
    let isValid = true;
    
   
    document.querySelectorAll('.error').forEach(el => el.remove());
    
    
    const requiredFields = ['name', 'email', 'subject', 'message'];
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        }
    });
    
    
    const email = form.querySelector('[name="email"]');
    if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        showError(email, 'Please enter a valid email');
        isValid = false;
    }
    
    return isValid;
}

function showError(input, message) {
    const error = document.createElement('div');
    error.className = 'error';
    error.textContent = message;
    error.style.color = 'red';
    error.style.marginTop = '5px';
    error.style.fontSize = '0.9em';
    input.parentNode.appendChild(error);
    input.style.borderColor = 'red';
}


if (document.getElementById('contactForm')) {
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        if (!validateContactForm()) {
            e.preventDefault();
        }
    });
}