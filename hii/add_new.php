<?php
require_once "db_conn.php";

if (isset($_POST["submit"])) {
    // Validate and sanitize user inputs here
    $indentName = mysqli_real_escape_string($conn, $_POST['indentName']);
    $indentorName = mysqli_real_escape_string($conn, $_POST['indentorName']);
    $poValue = mysqli_real_escape_string($conn, $_POST['poValue']);
    $pdStartDate = mysqli_real_escape_string($conn, $_POST['pdStartDate']);
    $pdEndDate = mysqli_real_escape_string($conn, $_POST['pdEndDate']);
    $supplierName = mysqli_real_escape_string($conn, $_POST['supplierName']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Fetch the user_icNo from the session
    $user_icNo = $_SESSION['user_icNo'];

    // Prepared statement for 'purchase_details' table
    $stmtPurchase = $conn->prepare("INSERT INTO purchase_details (indentName, indentorName, poValue, pdStartDate, pdEndDate, supplierName, status, user_icNo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtPurchase->bind_param("sssssssi", $indentName, $indentorName, $poValue, $pdStartDate, $pdEndDate, $supplierName, $status, $user_icNo);

    if ($stmtPurchase->execute()) {
        // Get the ID of the last inserted record
        $purchaseId = $stmtPurchase->insert_id;

        // Prepared statement for 'contacts' table
        $stmtContacts = $conn->prepare("INSERT INTO contacts (user_icNo, phone, email) VALUES (?, ?, ?)");

        // Loop through phone and email arrays
        foreach ($_POST['phone'] as $key => $phone) {
            $emailContact = $_POST['email'][$key];
            // Bind parameters and execute the statement
            $stmtContacts->bind_param("iss", $user_icNo, $phone, $emailContact);
            $stmtContacts->execute();
        }

        header("Location: index.php?msg=New record created successfully");
    } else {
        // Handle error gracefully
        echo "An error occurred while saving data. Please try again later.";
        // Log the error for debugging purposes
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Add custom styles for error messages */
        .text-danger {
            color: red;
            margin-top: 5px;
        }
    </style>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Purchase Details</title>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
        Purchase Details Application
    </nav>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Add New User</h3>
            <p class="text-muted">Complete the form below to add a new user</p>
        </div>
        <div class="container mt-5">
            <div class="card mx-auto" style="max-width: 700px;">
                <div class="card-body">
                    <h2 class="mt-4 mb-4 text-center" style="color: aqua;">Purchase Details</h2>
                    <!-- Display validation errors at the top -->
                    <div id="validationErrors" class="text-danger"></div>


                    <form id="pdForm" class="form-floating" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" novalidate>

                        <div class="row mb-3">
                            <!-- Indent Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="indentName" name="indentName"
                                        placeholder="Indent Name" required>
                                    <label for="indentName">Indent Name</label>
                                    <div id="indentNameValidationError" class="text-danger mt-1"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
    <div class="form-floating">
        <input type="text" class="form-control" id="indentorName" name="indentorName" placeholder="Indentor Name" onfocus="showDropdown()" oninput="checkManualInput(this)">
        <label for="indentorName" id="indentorNameLabel">Indentor Name</label>
        <div id="indentorNameValidationError" class="text-danger mt-1"></div>
        <div id="indentorNameDropdown" class="dropdown-menu" style="display: none;">
            <?php
$sql = "SELECT name, IcNo FROM user"; // Fetch both name and IcNo columns
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<a class='dropdown-item' href='#' onclick='selectOption(\"" . $row['name'] . " (" . $row['IcNo'] . ")\")'>" . $row['name'] . " (" . $row['IcNo'] . ")</a>";
    }
} else {
    echo "<p class='dropdown-item'>No options available</p>";
}
?>
        </div>
    </div>
</div>


                        <!-- Purchase Order (PO) Value and Status -->
                        <h5>Purchase Order Details:</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="poValue" name="poValue"
                                        placeholder="Purchase Order (PO) Value" required>
                                    <label for="poValue">Purchase Order (PO) Value</label>
                                    <div id="poValueValidationError" class="text-danger mt-1"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="" disabled selected>Select...</option>
                                        <option value="active">Active</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                    <label for="status">Status</label>
                                    <div id="validationError" class="text-danger mt-1"></div>
                                </div>
                            </div>
                        </div>
                        <!-- PD Starting Date and Ending Date -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="pdStartDate" name="pdStartDate"
                                        required>
                                    <label for="pdStartDate">PD Starting Date</label>
                                    <div id="pdStartDateValidationError" class="text-danger mt-1"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="pdEndDate" name="pdEndDate"
                                        required>
                                    <label for="pdEndDate">PD Ending Date</label>
                                    <div id="pdEndDateValidationError" class="text-danger mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <h5>Supplier Contact Details:</h5>
<div class="row mb-3" id="contactFields">
    <!-- Supplier Name -->
    <div class="col-md-6 mb-3">
        <div class="form-floating">
            <input type="text" class="form-control" id="supplierName" name="supplierName" placeholder="Supplier Name" required>
            <label for="supplierName">Supplier Name</label>
            <div id="supplierNameValidationError" class="text-danger mt-1"></div>
        </div>
    </div>

    <!-- Supplier Phone Number and Email -->
    <div class="row mb-3">
        <!-- Supplier Phone Number -->
        <div class="col-md-6">
            <div class="form-floating">
                <input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>
                <label for="phone">Phone Number</label>
                <div id="phoneError" class="text-danger mt-1"></div>
            </div>
        </div>
        <!-- Supplier Email -->
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" class="form-control" name="email[]" placeholder="Email" required>
                <label for="email">Email</label>
                <div id="emailError" class="text-danger mt-1"></div>
            </div>
        </div>
    </div>
</div>




                    <div class="mb-3">
                        <button type="button" class="btn btn-success me-2" onclick="addContact()">
                            <i class="bi bi-plus"></i> Add Contact
                        </button>
                        <button type="button" class="btn btn-danger" onclick="removeContact()">
                            <i class="bi bi-trash"></i> Remove Contact
                        </button>
                   </div>


<!-- Save and Cancel Buttons -->
<div class="mb-3">
    <button type="submit" class="btn btn-success" name="submit">Save</button>
    <a href="index.php" class="btn btn-danger">Cancel</a>
</div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script>
       // Add event listeners to close dropdown when clicking outside or another label
       document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('indentorNameDropdown');
    const target = event.target;
    const isClickInsideDropdown = dropdown.contains(target);
    const isClickOnLabel = target.tagName.toLowerCase() === 'label' && target.getAttribute('for') === 'indentorName';

    if (!isClickInsideDropdown && !isClickOnLabel) {
        hideDropdown();
    }
});

function showDropdown() {
    document.getElementById('indentorNameDropdown').style.display = 'block';
}

function hideDropdown() {
    document.getElementById('indentorNameDropdown').style.display = 'none';
}

function checkManualInput(input) {
    if (input.value.trim() === '') {
        showDropdown();
    } else {
        hideDropdown();
    }
}

function selectOption(option) {
    document.getElementById('indentorName').value = option;
    hideDropdown();
}

// Add event listener for the label
document.querySelector('label[for="indentorName"]').addEventListener('click', function(event) {
    // Stop event propagation to prevent the dropdown from hiding immediately
    event.stopPropagation();
});


  </script>
    <script src="script.js"></script>
</body>

</html>
