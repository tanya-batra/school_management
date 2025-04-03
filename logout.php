<?php
session_start();  // Start the session

// Destroy the session to log the user out
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

if (!isset($_SESSION['username'])) {
  // Redirect to the login page if not logged in
  header("Location: index.php");
  exit;
}
// Redirect to the login page
//header("Location: index.php");  // Redirect to the login page
exit;  // Make sure the rest of the script does not run
?>
