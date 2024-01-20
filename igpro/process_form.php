<?php
// Debugging code
var_dump($_POST);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to your MySQL database
    $conn = mysqli_connect('localhost', 'root', '', 'your_database');

    // Check connection
    if (!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }

    // Process form data
    $amcName = mysqli_real_escape_string($conn, $_POST['amcName']);
    $initiatorName = mysqli_real_escape_string($conn, $_POST['initiatorName']);
    $amcCost = mysqli_real_escape_string($conn, $_POST['amcCost']);
    $amcStartDate = mysqli_real_escape_string($conn, $_POST['amcStartDate']);
    $amcEndDate = mysqli_real_escape_string($conn, $_POST['amcEndDate']);
    $supplierName = mysqli_real_escape_string($conn, $_POST['supplierName']);

    // Check if "amcStatus" is set in $_POST
    $amcStatus = isset($_POST['amcStatus']) ? mysqli_real_escape_string($conn, $_POST['amcStatus']) : '';

    // Validate required fields
    if (empty($amcName) || empty($initiatorName) || empty($supplierName) || empty($amcCost) || empty($amcStatus)) {
        echo '<div class="alert alert-danger" role="alert">Please fill in all details.</div>';
    } else {
        // Insert data into the 'amc_details' table using prepared statement
        $sql = "INSERT INTO amc_details (amcName, initiatorName, amcCost, amcStartDate, amcEndDate, supplierName, amcStatus)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters to the statement
        mysqli_stmt_bind_param($stmt, "sssssss", $amcName, $initiatorName, $amcCost, $amcStartDate, $amcEndDate, $supplierName, $amcStatus);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Get the ID of the inserted record
            $lastInsertId = mysqli_insert_id($conn);

            // Process multiple phone numbers and emails
            if (!empty($_POST['phone'])) {
                foreach ($_POST['phone'] as $phone) {
                    $phone = mysqli_real_escape_string($conn, $phone);
                    $sqlPhone = "INSERT INTO phone_numbers (amc_id, phone_number) VALUES ('$lastInsertId', '$phone')";
                    mysqli_query($conn, $sqlPhone);
                }
            }

            if (!empty($_POST['email'])) {
                foreach ($_POST['email'] as $email) {
                    $email = mysqli_real_escape_string($conn, $email);
                    $sqlEmail = "INSERT INTO emails (amc_id, email_address) VALUES ('$lastInsertId', '$email')";
                    mysqli_query($conn, $sqlEmail);
                }
            }

            echo '<div class="alert alert-success" role="alert">Record added successfully</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error adding record: ' . mysqli_error($conn) . '</div>';
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
