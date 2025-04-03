<?php
include('db_connect.php');
session_start();  // Start the session to store the message

// Check if the ID is provided for deletion
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Check if the student has any related fee records
    $stmt_check_fee = $conn->prepare("SELECT * FROM fee_transactions WHERE student_id = ?");
    $stmt_check_fee->bind_param("i", $studentId);
    $stmt_check_fee->execute();
    $fee_result = $stmt_check_fee->get_result();

    // If there are fee records associated with the student
    if ($fee_result->num_rows > 0) {
        // Student has fee records, so do not delete
        $_SESSION['errorMessage'] = "This student has related fee records and cannot be deleted.";
        $_SESSION['message_type'] = 'errorMessage'; // Optional, to specify message type (like success, error, warning)
    } else {
        // If no fee records are found, proceed with deletion
        $stmt_delete = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt_delete->bind_param("i", $studentId);
        
        if ($stmt_delete->execute()) {
            $_SESSION['successMessage'] = "Student deleted successfully.";
            $_SESSION['message_type'] = 'successMessage';
        } else {
            $_SESSION['message'] = "Error deleting student: " . $conn->error;
            $_SESSION['message_type'] = 'error';
        }
    }
    
    // Redirect back to the view-students.php page
    header('Location: view-students.php');
    exit();
} else {
    echo "Student ID not provided.";
}
?>
