<?php
include('db_connect.php');

if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Use the correct column name for student name here (e.g., 'name')
    $stmt = $conn->prepare("SELECT * FROM students WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            // Display student details in a table row
            echo '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row['adhar_no'] . '</td>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['father_name'] . '</td>
                    <td>' . $row['mother_name'] . '</td>
                    <td><img src="' . $row['profile_image'] . '" alt="Profile Image" width="50"></td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="6">No student found with that name.</td></tr>';
    }
}
?>
