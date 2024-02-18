function addContact() {
  const contactFields = document.getElementById("contactFields");

  const newContact = document.createElement("div");
  newContact.classList.add("row", "mb-3"); // Add Bootstrap classes for styling

  newContact.innerHTML = `<div class="col-md-6">
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
  const contactFields = document.getElementById("contactFields");
  const lastContact = contactFields.lastElementChild;

  if (lastContact) {
    contactFields.removeChild(lastContact);
  }
}
// Get the necessary elements
const statusSelect = document.getElementById("status");
const pdStartDateInput = document.getElementById("pdStartDate");
const pdEndDateInput = document.getElementById("pdEndDate");

// Function to set up event listeners and adjust date inputs
function setupEventListeners() {
  const status = statusSelect.value;
  const today = new Date();
  const todayString = today.toLocaleDateString("en-CA"); // Format: YYYY-MM-DD

  if (status === "active") {
    // Set PD Starting Date to today's date
    pdStartDateInput.value = todayString;

    // Set PD Ending Date minimum to one day after today's date
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    pdEndDateInput.min = tomorrow.toLocaleDateString("en-CA");
  } else if (status === "ongoing") {
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    pdStartDateInput.max = yesterday.toLocaleDateString("en-CA");

    // Set PD Ending Date minimum to today's date
    pdEndDateInput.min = today.toLocaleDateString("en-CA");
    console.log("Minimum end date:", pdEndDateInput.min);
  } else if (status === "expired") {
    // Set PD Starting Date maximum to yesterday's date
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    pdStartDateInput.max = yesterday.toLocaleDateString("en-CA");

    // Set PD Ending Date maximum to yesterday's date
    pdEndDateInput.max = yesterday.toLocaleDateString("en-CA");
  }
}

// Add event listener to the status select element
statusSelect.addEventListener("change", setupEventListeners);

// Function to validate PD Starting and Ending Dates
function validateDateInputs() {
  const startDate = new Date(pdStartDateInput.value);
  const endDate = new Date(pdEndDateInput.value);

  if (startDate > endDate) {
    pdEndDateInput.setCustomValidity(
      "PD Ending Date must be after PD Starting Date"
    );
  } else {
    pdEndDateInput.setCustomValidity("");
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
