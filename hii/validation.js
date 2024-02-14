function addContact() {
    const contactFields = document.getElementById('contactFields');

    const newContact = document.createElement('div');
    newContact.classList.add('row', 'mb-3'); // Add Bootstrap classes for styling

    newContact.innerHTML =
        `<div class="col-md-6">
            <div class="form-floating">
                <input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>
                <label for="phone">Phone Number</label>
                <div class="text-danger mt-1"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" class="form-control" name="email[]" placeholder="Email" required>
                <label for="email">Email</label>
                <div class="text-danger mt-1"></div>
            </div>
        </div>`;

    contactFields.appendChild(newContact);
}

function removeContact() {
    const contactFields = document.getElementById('contactFields');
    const lastContact = contactFields.lastElementChild;

    if (lastContact) {
        contactFields.removeChild(lastContact);
    }
}



function validateForm(event) {
    var isValid = true;
    var validationErrors = document.getElementById("validationErrors");
    validationErrors.innerHTML = ""; // Clear previous validation errors

    // Validate Indent Name
    var indentName = document.getElementById("indentName").value.trim();
    if (indentName === "") {
        displayValidationError("Indent Name is required");
        isValid = false;
    }

    // Validate Indentor Name
    var indentorName = document.getElementById("indentorName").value.trim();
    if (indentorName === "") {
        displayValidationError("Indentor Name is required");
        isValid = false;
    }

    // Validate Purchase Order Value
    var poValue = document.getElementById("poValue").value.trim();
    if (poValue === "") {
        displayValidationError("Purchase Order (PO) Value is required");
        isValid = false;
    }

    // Validate Status
    var status = document.getElementById("status").value;
    if (status === "") {
        displayValidationError("Status is required");
        isValid = false;
    }

    // Validate Supplier Name
    var supplierName = document.getElementById("supplierName").value.trim();
    if (supplierName === "") {
        displayValidationError("Supplier Name is required");
        isValid = false;
    }

    // Validate Supplier Phone Number
    var phoneNumberInputs = document.getElementsByName("phone[]");
    for (var i = 0; i < phoneNumberInputs.length; i++) {
        var phoneNumber = phoneNumberInputs[i].value.trim();
        if (phoneNumber === "") {
            displayValidationError("Phone Number is required");
            isValid = false;
            break; // Exit loop if any phone number is empty
        }
    }

    // Validate Supplier Email
    var emailInputs = document.getElementsByName("email[]");
    for (var i = 0; i < emailInputs.length; i++) {
        var email = emailInputs[i].value.trim();
        if (email === "") {
            displayValidationError("Email is required");
            isValid = false;
            break; // Exit loop if any email is empty
        }
    }

    if (!isValid) {
        event.preventDefault(); // Prevent form submission if there are validation errors
    }

    return isValid;
}

function displayValidationError(message) {
    var validationErrors = document.getElementById("validationErrors");
    var errorElement = document.createElement("div");
    errorElement.textContent = message;
    validationErrors.appendChild(errorElement);
}
