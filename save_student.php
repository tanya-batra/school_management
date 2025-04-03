<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

include('db_connect.php');

// Fetch the active session dynamically
$sql = "SELECT * FROM session WHERE status = 1 LIMIT 1";  // Only get the active session
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Session is found, get the session data
    $sessionData = mysqli_fetch_assoc($result);
    $session = $sessionData['session'];  // E.g., "2025-26"
} else {
    // No active session found
    $_SESSION['errorMessage'] = 'No active session found.';
    header('Location: dashboard.php');
    exit;
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the form data
    $admission_no = mysqli_real_escape_string($conn, $_POST['admission_no']);
    $class_id = mysqli_real_escape_string($conn, $_POST['class']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $relation = mysqli_real_escape_string($conn, $_POST['relation']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
    $adhar_no = mysqli_real_escape_string($conn, $_POST['adhar_no']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $total_fees = mysqli_real_escape_string($conn, $_POST['total_fees']);
    $balance = mysqli_real_escape_string($conn, $_POST['total_fees']); // Default balance, can be updated dynamically

    // Check if the admission number already exists in the database
    $checkQuery = "SELECT * FROM students WHERE admission_no = '$admission_no' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Admission number already exists, return error message
        $response = [
            'success' => false,
            'message' => 'Admission number already exists.'
        ];
        echo json_encode($response);
        exit;
    }

   // Handling file upload for the profile image
if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
    $profile_img = $_FILES['profile_img'];
    $fileName = time() . '_' . $profile_img['name'];
    $fileTmpName = $profile_img['tmp_name'];
    $fileDestination = 'profile_img/' . $fileName;
    
    // Get the image information (width, height, type)
    list($originalWidth, $originalHeight, $imageType) = getimagesize($fileTmpName);
    
    // Set the new dimensions for the image
    $maxWidth = 300;  // Maximum width
    $maxHeight = 300; // Maximum height
    
    // Calculate the aspect ratio
    $aspectRatio = $originalWidth / $originalHeight;
    
    if ($originalWidth > $originalHeight) {
        // Landscape image
        $newWidth = $maxWidth;
        $newHeight = round($maxWidth / $aspectRatio);  // Calculate height based on the aspect ratio
    } else {
        // Portrait or square image
        $newHeight = $maxHeight;
        $newWidth = round($maxHeight * $aspectRatio);  // Calculate width based on the aspect ratio
    }

    // Create a blank image with the new dimensions
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Based on the image type, create the appropriate image resource
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($fileTmpName);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($fileTmpName);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($fileTmpName);
            break;
        default:
            $response = [
                'success' => false,
                'message' => 'Unsupported image type.'
            ];
            echo json_encode($response);
            exit;
    }

    // Resize the image to the new dimensions
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Save the resized image to the destination folder
    if ($imageType == IMAGETYPE_JPEG) {
        imagejpeg($newImage, $fileDestination, 90); // 90 is the quality (0-100)
    } elseif ($imageType == IMAGETYPE_PNG) {
        imagepng($newImage, $fileDestination);
    } elseif ($imageType == IMAGETYPE_GIF) {
        imagegif($newImage, $fileDestination);
    }

    // Free up memory
    imagedestroy($newImage);
    imagedestroy($sourceImage);
    
    // File upload successful
} else {
    $fileName = null;
}



    // Insert data into the database
    $query = "INSERT INTO students (admission_no, class_id, name, relation, father_name, mother_name, adhar_no, contact_no, profile_img, total_fees, balance, session, status)
              VALUES ('$admission_no', '$class_id', '$name', '$relation', '$father_name', '$mother_name', '$adhar_no', '$contact_no', '$fileDestination', '$total_fees', '$balance', '$session', 1)";  // Default status is 1 (active)

    if (mysqli_query($conn, $query)) {
        $response = [
            'success' => true,
            'message' => 'Student added successfully!'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Error adding student: ' . mysqli_error($conn)
        ];
    }

    // Close the connection and return JSON response
    mysqli_close($conn);
    echo json_encode($response);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
    exit;
}
?>
