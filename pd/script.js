function addContact() {
    const contactFields = document.getElementById('contactFields');
    const newContact = document.createElement('div');

    newContact.innerHTML =
        '<div class="contact-inputs row">' +
        '<div class="col-md-6 mb-3">' +
        '<input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>' +
        '</div>' +
        '<div class="col-md-6 mb-3">' +
        '<input type="email" class="form-control" name="email[]" placeholder="Email" required>' +
        '</div>' +
        '</div>';

    contactFields.appendChild(newContact);
}

// Function to remove the last contact field
function removeContact() {
    const contactFields = document.getElementById('contactFields');
    const lastContact = contactFields.lastElementChild;

    if (lastContact) {
        contactFields.removeChild(lastContact);
    }
}
function setError(errorElement, message) {
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function clearError(errorElement) {
    errorElement.textContent = '';
    errorElement.style.display = 'none';
}

async function validateForm(event) {
    event.preventDefault(); // Prevent the form from submitting immediately

    // Clear previous validation errors
    document.getElementById('validationErrors').innerHTML = '';
    document.getElementById('generalValidationErrors').innerHTML = '';

    const formElements = {
        'indentName': { label: 'Indent Name', minLength: 5 },
        'indentorName': { label: 'Indentor Name', minLength: 5 },
        'poValue': { label: 'Purchase Order (PO) Value', minLength: 1 },
        'pdStartDate': { label: 'PD Starting Date', minLength: 1 },
        'pdEndDate': { label: 'PD Ending Date', minLength: 1 },
        'supplierName': { label: 'Supplier Name', minLength: 5 },
    };

    let isValid = true;

    for (const [fieldName, config] of Object.entries(formElements)) {
        isValid = isValid && await validateField(document.getElementById(fieldName).value, fieldName, config.minLength, config.label);
    }

    // Validate PD Supplier Contact
    isValid = isValid && await validateContactFields();

    if (isValid) {
        // If all validations pass, submit the form manually
        document.getElementById('pdForm').submit();
    } else {
        // Display a general validation error message
        document.getElementById('validationErrors').innerHTML = 'Please fill in all details.';
    }
}

async function validateField(value, fieldName, minLength, label) {
    const errorElement = document.getElementById(`${fieldName}ValidationError`);
    if (value.trim() === '') {
        setError(errorElement, `*Please enter ${label}`);
        return false;
    } else if (value.length < minLength) {
        setError(errorElement, `*${label} must contain at least ${minLength} characters`);
        return false;
    } else {
        clearError(errorElement);
        return true;
    }
}

async function validateContactFields() {
    const phoneInputs = document.getElementsByName('phone[]');
    const emailInputs = document.getElementsByName('email[]');

    let isValid = true;

    for (const phoneInput of phoneInputs) {
        isValid = isValid && await validateField(phoneInput.value, 'phone', 10, 'Phone Number');
    }

    for (const emailInput of emailInputs) {
        isValid = isValid && await validateField(emailInput.value, 'email', 1, 'Email');
    }

    return isValid;
}

document.getElementById('pdForm').addEventListener('submit', function (event) {
    validateForm(event);
});
