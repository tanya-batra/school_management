<?php
// Start the session
session_start(); 

// Database connection
include('db_connect.php');
$username = $_POST['username'];
$password = $_POST['password'];



// Response array
$response = array('success' => false, 'message' => '', 'role' => '', 'status' => 0);

// Check if both fields are filled
if (empty($username) || empty($password)) {
    $response['message'] = 'Username and password are required.';
    echo json_encode($response);
    exit;
}

// Escape username to prevent SQL injection
$username = $conn->real_escape_string($username);

// Query to find user in the database
$sql = "SELECT * FROM users WHERE email = '$username' LIMIT 1";
$result = $conn->query($sql);

// Check if the user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check if password matches
    if ($password === $user['password']) {
        // Set session variables
        $_SESSION['1d'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['status'] = $user['status'];

        // Check user status (active or inactive)
        if ($user['status'] == 1) {
            $response['success'] = true;
            $response['message'] = 'Login successful.';
            $response['role'] = $user['role'];
            $response['status'] = $user['status'];
        } else {
            $response['message'] = 'Your account is inactive. Please contact the administrator.';
        }
    } else {
        // Incorrect password
        $response['message'] = 'Incorrect password.';
    }
} else {
    // User not found
    $response['message'] = 'No user found with that username.';
}

// Send the response in JSON format
header('Content-Type: application/json');
echo json_encode($response);

// Close database connection
$conn->close();
?>
