<?php
session_start();
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

// Check if the AJAX request has been made
if (isset($_POST['father']) && isset($_POST['name'])) {
    $father = mysqli_real_escape_string($conn, $_POST['father']);
    $student_name = mysqli_real_escape_string($conn, $_POST['name']);

    // Ensure the connection uses utf8mb4 (if your DB is utf8mb4)
    mysqli_set_charset($conn, 'utf8mb4');

    // Adjusted query to filter by class, section, and name
    $sql = "SELECT * 
            FROM students             
            WHERE father_name LIKE '%$father%'             
            AND name LIKE '%$student_name%'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if rows were found
    if (mysqli_num_rows($result) > 0) {
        $count = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo <<<HTML
<tr>
    <td>{$count}</td>
    <td>{$row['adhar_no']}</td>
    <td>{$row['name']}</td>
    <td>{$row['father_name']}</td>
    <td>{$row['mother_name']}</td>
    <td><img src="{$row['profile_img']}" alt="" style="width:100px; height:100px; border-radius:100%"></td>
    <td><a href="add-session-detail.php?student_id={$row['id']}" class="btn btn-warning btn-sm">Edit</a></td>
</tr>
HTML;
            $count++;
        }
    } else {
        // No records found
        echo "<tr><td colspan='7'>No records found.</td></tr>";
    }
}
?>
