<?php
session_start();

// Include database connection file
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure that necessary POST data is available
    if (isset($_POST['class_id']) && isset($_POST['fees'])) {
        $classId = $_POST['class_id'];
        $fees = $_POST['fees'];

        // Sanitize inputs (recommended for security)
        $classId = mysqli_real_escape_string($conn, $classId);
        $fees = mysqli_real_escape_string($conn, $fees);

        // Update the fees in the database
        $query = "UPDATE class SET fees = '$fees' WHERE id = $classId";

        if (mysqli_query($conn, $query)) {
            echo 'success'; // If update is successful, send a success message
        } else {
            echo 'Error updating fees: ' . mysqli_error($conn); // If error occurs, send error message
        }
    } else {
        echo 'All fields are required!'; // If required data is missing
    }
} else {
    echo 'Invalid request method!';
}
?>
