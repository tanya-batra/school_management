<?php
include('db_connect.php');

// Check if the 'name' query parameter is set
if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Prepare the query to search students by name (partial matching)
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? LIMIT 5");
    $stmt->bind_param("s", $nameParam);
    $nameParam = $name . '%';  // Adding a wildcard for partial matching
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results are found
    if ($result->num_rows > 0) {
        // Loop through each student and show the name with their parent details
        while ($row = $result->fetch_assoc()) {
            $studentName = $row['name'];
			$admissionNo = $row['admission_no'];
            $fatherName = $row['father_name'];
            $aadharNo = $row['adhar_no'];
            $relation = $row['relation']; // This is either S/o or D/o

            // Format the student's name along with the relation and parent details
            $formattedName = $studentName;

            // Append the relation and Father's name
            if (!empty($fatherName)) {
                $formattedName .= ' (' . $relation . ') ' . $fatherName;
            }

            // Append the Mother's name if it exists
            if (!empty($admissionNo)) {
                $formattedName .= ' - ' . $admissionNo . ' - ' . $aadharNo;
            }

            // Display the suggestion as an item in the list
            echo '<div class="suggestion-item" data-id="' . $row['id'] . '">' . $formattedName . '</div>';
        }
    } else {
        echo '<div>No students found</div>';
    }
}
?>
