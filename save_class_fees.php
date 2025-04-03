<?php
session_start();

// Include database connection file
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

// Check if the form data is coming through the POST request
if (isset($_POST['class_name']) && isset($_POST['fees'])) {
    $class_name = trim($_POST['class_name']);
    $fees = trim($_POST['fees']);

    // Ensure no empty data is inserted
    if (empty($class_name) || empty($fees)) {
        echo 'Error: Please fill in all the fields.';
        exit();
    }

    // Check if the class already exists
    $checkClassQuery = "SELECT * FROM class WHERE class_name = ?";
    $stmt = $conn->prepare($checkClassQuery);
    $stmt->bind_param("s", $class_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Class already exists
        echo 'Error: Class already exists.';
    } else {
        // Prepare the SQL query to insert new class data
        $insertQuery = "INSERT INTO class (class_name, fees) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sd", $class_name, $fees);

        if ($stmt->execute()) {
            echo 'success'; // Data inserted successfully
        } else {
            echo 'Error: Could not save the class. Please try again later.';
        }
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    echo 'Error: Invalid request.';
}




?>
