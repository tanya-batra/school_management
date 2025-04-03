
<?php
session_start();

// Include database connection file
include('db_connect.php');

// Check if the form is submitted via POST and required fields are set
if (isset($_POST['student_id']) && isset($_POST['session']) && isset($_POST['class']) && isset($_POST['section']) && isset($_POST['total_fee'])) {

    // Sanitize and escape input data
    $student_id = intval($_POST['student_id']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $total_fee = floatval($_POST['total_fee']);

    // Insert the data into the session_detail table
    $insert_stmt = $conn->prepare("INSERT INTO session_detail (student_id, session, class, section, total_fee) VALUES (?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("isssi", $student_id, $session, $class, $section, $total_fee);
    
    if ($insert_stmt->execute()) {
        // Success
        echo json_encode(['success' => true, 'message' => 'Session details added successfully!']);
    } else {
        // Error
        echo json_encode(['success' => false, 'message' => 'Failed to add session details.']);
    }

    $insert_stmt->close();
} else {
    // Invalid data
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}

$conn->close();
?>
