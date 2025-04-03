<?php
// get_class_fees.php
include('db_connect.php');

if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];

    $query = "SELECT fees FROM class WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    $stmt->bind_result($fees);
    $stmt->fetch();
    $stmt->close();

    if ($fees) {
        echo json_encode(['success' => true, 'fees' => $fees]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Fee not found']);
    }
}
?>
