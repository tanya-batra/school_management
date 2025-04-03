<?php
// Include your database connection
include('db_connect.php');

if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    // Query to fetch student details
    $query = "SELECT total_fees, balance FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->bind_result($totalFees, $pendingFees);

    // Check if student data exists
    if ($stmt->fetch()) {
        // Return the data as JSON
        echo json_encode([
            'totalFees' => $totalFees,
            'balance' => $pendingFees
        ]);
    } else {
        // If no student is found, return an error message
        echo json_encode(['error' => 'Student not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No student ID provided']);
}
?>
