<?php
include('db_connect.php');

// Check if the student ID is provided
if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    // Prepare the query to fetch full student details by ID
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the student exists
    if ($result->num_rows > 0) {
        // Fetch the student's data
        $row = $result->fetch_assoc();

        // Get the student details
        $studentName = $row['name'];
        $fatherName = $row['father_name'];
        $motherName = $row['mother_name'];
        $relation = $row['relation']; // S/o or D/o

        
        // Output the full student details as a table row
        echo '<tr>';
        //echo '<td>#</td>';
		echo '<td>' . $row['admission_no'] . '</td>';
        echo '<td><img src="' . $row['profile_img'] . '" alt="Profile Image" class="img-fluid" style="border-radius: 10px; width: 70px;"></td>';


        echo '<td>' . $studentName . '</td>';
        echo '<td>' . $row['adhar_no'] . '</td>';
        echo '<td>' . $fatherName . '</td>';
        echo '<td>' . $motherName . '</td>';
        echo '<td>
        <a href="edit-student.php?id=' . $row['id'] . '" class="btn btn-primary"><i class="ti ti-pencil"></i>&nbsp;&nbsp;Edit</a>&nbsp;
       <a href="delete-student.php?id=' . $row['id'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this student?\');"><i class="ti ti-trash"></i>&nbsp;&nbsp;Delete Student</a>
        </td>';
        echo '</tr>';
    } else {
        echo '<tr><td colspan="7">No student found with the given ID.</td></tr>';
    }
}
?>
