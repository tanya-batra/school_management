<?php
session_start();
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$created_by = $_SESSION['email'];  // Get the logged-in user ID from the session

// Check if the necessary data is available
if (isset($_POST['pay_amount']) && isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']); // Retrieve student_id from the URL (Edit Button click)
    $pay_amount = floatval($_POST['pay_amount']); // Retrieve the payment amount from the form
    
    // Check if both values are valid
    if ($student_id > 0 && $pay_amount > 0) {
        // Prepare SQL query to insert the fee record
        $stmt = $conn->prepare("INSERT INTO fee_details (student_id, pay_amount, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $student_id, $pay_amount, $created_by); // "i" for integer, "d" for double, "s" for string

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Fee payment recorded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error recording fee payment']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID or payment amount']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
}
?>
