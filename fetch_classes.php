<?php
// fetch_classes.php

include('db_connect.php'); // Include your database connection

// Get the page number (default to 1 if not set)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Number of records per page (5 records per page)
$offset = ($page - 1) * $limit; // Calculate the offset based on the page number

// Fetch the data for the current page
$query = "SELECT * FROM class ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$classes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $classes[] = $row;
}

// Get the total number of classes for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM class";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalClasses = $totalRow['total'];  // Total records in the class table
$totalPages = ceil($totalClasses / $limit); // Calculate total pages based on the limit

// Return the data as JSON
echo json_encode([
    'classes' => $classes,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
?>
