<?php
include('db_connect.php');

// Check if the 'name' query parameter is set
if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Prepare the query to search students by name (partial matching)
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR father_name LIKE ?  OR 
    adhar_no LIKE ? OR contact_no LIKE ? OR admission_no LIKE ?
    LIMIT 5");
    $stmt->bind_param("sssss", $nameParam, $father,$adhar_no,$contact_no,$admission_no);
    $nameParam = $name . '%';  
    $father = $name . '%';  
    $adhar_no = $name . '%';  
    $contact_no = $name . '%';  
    $admission_no = $name . '%';  
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results are found
    if ($result->num_rows > 0) {
        // Loop through each student and show the name with their parent details
        while ($row = $result->fetch_assoc()) {
            $studentName = $row['name'];
            $fatherName = $row['father_name'];
            $relation = $row['relation']; // This is either S/o or D/o
            $admission_no = $row['admission_no'];
            $adhar_no = $row['adhar_no'];


            // Format the student's name along with the relation and parent details
            $formattedName = $studentName;

            // Append the relation and Father's name
            if (!empty($fatherName)) {
                $formattedName .= ' (' . $relation . ') ' . $fatherName;
            }

            // Append the Mother's name if it exists
            
            if (!empty($admission_no)) {
                $formattedName .= ' -- ' . $admission_no;
            }

            if (!empty($adhar_no)) {
                $formattedName .= ' -- ' . $adhar_no;
            }

            // Display the suggestion as an item in the list
            echo '<div class="suggestion-item" data-id="' . $row['id'] . '">' . $formattedName . '</div>';
        }
    } else {
        echo '<div>No students found</div>';
    }
}
?>
