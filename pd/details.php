<?php
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to your MySQL database
    $conn = mysqli_connect('localhost', 'root', '', 'amc');

    // Check connection
    if (!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }

    // Enable error reporting for MySQLi
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // Process form data
    $indentName = $_POST['indentName'];
    $indentorName = $_POST['indentorName'];
    $poValue = $_POST['poValue'];
    $pdStartDate = $_POST['pdStartDate'];
    $pdEndDate = $_POST['pdEndDate'];
    $supplierName = $_POST['supplierName'];
    $status = $_POST['status'];

    // Validate required fields
    if (empty($indentName) || empty($indentorName) || empty($poValue) || empty($pdStartDate) || empty($pdEndDate) || empty($supplierName) || empty($status)) {
        $_SESSION['error'] = '<div class="alert alert-danger" role="alert">Please fill in all details.</div>';
        exit;
    }

    // Insert data into the 'purchase_details' table using prepared statement
    $sql = "INSERT INTO purchase_details (indent_name, indentor_name, po_value, pd_start_date, pd_end_date, supplier_name, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters to the statement
    mysqli_stmt_bind_param($stmt, "sssssss", $indentName, $indentorName, $poValue, $pdStartDate, $pdEndDate, $supplierName, $status);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Get the ID of the inserted record
        $lastInsertId = mysqli_insert_id($conn);

        // Process multiple phone numbers and emails
        if (!empty($_POST['phone'])) {
            foreach ($_POST['phone'] as $phone) {
                $sqlPhone = "INSERT INTO phone_numbers (purchase_id, phone_number) VALUES (?, ?)";
                $stmtPhone = mysqli_prepare($conn, $sqlPhone);
                mysqli_stmt_bind_param($stmtPhone, "is", $lastInsertId, $phone);
                mysqli_stmt_execute($stmtPhone);
            }
        }

        if (!empty($_POST['email'])) {
            foreach ($_POST['email'] as $email) {
                $sqlEmail = "INSERT INTO emails (purchase_id, email_address) VALUES (?, ?)";
                $stmtEmail = mysqli_prepare($conn, $sqlEmail);
                mysqli_stmt_bind_param($stmtEmail, "is", $lastInsertId, $email);
                mysqli_stmt_execute($stmtEmail);
            }
        }

        $_SESSION['success'] = '<div class="alert alert-success" role="alert">Record added successfully</div>';
    } else {
        $_SESSION['error'] = '<div class="alert alert-danger" role="alert">Error adding record: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }

    // Close the statement
    mysqli_stmt_close($stmt);

    // Close the database connection
    mysqli_close($conn);
}

// Redirect back to the form
header('Location: index.html');
exit;

// Clear session data
session_destroy();
?>