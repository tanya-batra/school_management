<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}
include('db_connect.php');
$sql = "SELECT * FROM session WHERE status = 1 LIMIT 1"; // Only get the active session
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Session is found, get the session data
    $sessionData = mysqli_fetch_assoc($result);
    $session = $sessionData['session'];  // E.g., "2025-26"
} else {
    // No active session found, handle accordingly
    $_SESSION['errorMessage'] = 'No active session found.';
    header('Location: dashboard.php');
    exit;
}

// Check if the necessary data is posted
if (isset($_POST['student_id'], $_POST['amount'], $_POST['payment_mode'])) {
    $studentId = $_POST['student_id'];
    $amount = $_POST['amount'];
    $paymentMode = $_POST['payment_mode'];
    $createdBy = $_SESSION['email']; // Get the email from session
   
    // Get the current total_fees and balance of the student from the students table
    $query = "SELECT total_fees, balance FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $stmt->bind_result($totalFees, $currentBalance);
    $stmt->fetch();
    $stmt->close();

    // Check if student exists
    if (!$totalFees) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        exit;
    }

    // Calculate the new balance after payment
    $newBalance = $currentBalance - $amount;

    // Prepare the SQL statement to insert the fee transaction record
    $sql = "INSERT INTO fee_transactions (student_id, amount, payment_mode, session ,created_by,date) 
            VALUES (?, ?, ?, ?,?,NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $studentId, $amount, $paymentMode,$session,$createdBy);
    
    // Execute the query
    if ($stmt->execute()) {
        // Update the student's balance after the transaction
        $updateBalanceSql = "UPDATE students SET balance = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateBalanceSql);
        $updateStmt->bind_param("di", $newBalance, $studentId);
        $updateStmt->execute();
        $updateStmt->close();

        // Send a success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Fee transaction successfully added!',
            'new_balance' => $newBalance
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error occurred while adding the fee transaction']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
}
?>
