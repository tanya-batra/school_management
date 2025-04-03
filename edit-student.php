<?php
ob_start();
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$viewStudents = "active";
include('db_connect.php');

// Check if the student ID is provided in the URL
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Fetch the student details along with the class name
    $stmt = $conn->prepare("SELECT s.*, c.class_name as class_name FROM students s
                            JOIN class c ON s.class_id = c.id
                            WHERE s.id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student data is found
    if ($result->num_rows > 0) {
        // Fetch student details
        $student = $result->fetch_assoc();
    } else {
        echo "Student not found!";
        exit;
    }
} else {
    echo "No student ID provided!";
    exit;
}

// Check if the form is submitted for fee change
if (isset($_POST['submit'])) {
    $studentId = $_GET['id'];
    $studentName = $_POST['name'];
    $feeChange = $_POST['fee_change'];
    $amount = floatval($_POST['amount']);
    $comment = $_POST['comment'];
    $fatherName = $_POST['father_name'];
    $motherName = $_POST['mother_name'];
    $relation = $_POST['relation'];
    $adhar_no = $_POST['adhar_no'];
    $contact_no = $_POST['contact_no'];
    $profileImg = $student['profile_img']; // Default to current image

    // Handling file upload for the profile image
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



    // Get current total fees from students table
    $stmt = $conn->prepare("SELECT total_fees, balance FROM students WHERE id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $currentTotalFees = $student['total_fees'];
    $currentBalance = $student['balance'];

    // Update total fees based on fee_change (Add or Subtract)
    if ($feeChange == 1) {
        $newTotalFees = $currentTotalFees + $amount;
        $newBalance = $currentBalance + $amount;
    } else {
        $newTotalFees = $currentTotalFees - $amount;
        $newBalance = $currentBalance - $amount;
    }

    // Update total fees in students table
    $updateStmt = $conn->prepare("UPDATE students SET  total_fees = ?, balance = ?,name = ?, father_name = ?, mother_name = ?, relation = ?, profile_img = ? , adhar_no = ?, contact_no = ? WHERE id = ?");
    $updateStmt->bind_param("dssssssssi",  $newTotalFees, $newBalance, $studentName, $fatherName, $motherName, $relation, $fileDestination, $adhar_no,$contact_no, $studentId);
    $updateStmt->execute();

    // Insert transaction into change_fee table
    $feeChangeText = ($feeChange == 1) ? 'Add' : 'Subtract';
    $transactionStmt = $conn->prepare("INSERT INTO change_fee (student_id, fee_change, amount, comment) VALUES (?, ?, ?, ?)");
    $transactionStmt->bind_param("isss", $studentId, $feeChangeText, $amount, $comment);
    $transactionStmt->execute();

    // Redirect with success message
    $_SESSION['successMessage'] = "Student details updated successfully!";
    header("Location:view-students.php");
    exit;
}

// Query to get all classes from the classes table
$classesQuery = "SELECT * FROM class";
$classesResult = $conn->query($classesQuery);
?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Student | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .form-select {
      padding: 12px 16px;
    }

    .body-wrapper > .container-fluid.x-padd {
      padding: 36px;
    }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="dashboard.php" class="text-nowrap logo-img">
            <img src="assets/images/logo.png" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <?php include('sidebar.php') ?>
    </aside>
    <!-- Sidebar End -->
    <!-- Main wrapper -->
    <div class="body-wrapper">
    <?php include('header.php') ?>
    
    <div class="container-fluid x-padd" style="max-width: 100%;">
      <div class="row">
        <h5 class="card-title fw-semibold mb-4">Edit Student for the Session 2025-26</h5>
      

        <form id="studentForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <!--<div class="mb-3 col-xxl-3 col-lg-4">
                    <label for="status" class="form-label">Status</label>
                      <select class="form-select" name="status" id="status" required>
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                      </select>
                    </div>-->
					
					<div class="mb-3 col-xxl-3 col-lg-3">
						<label for="admission_no" class="form-label">Admission No.</label>
						<input type="text" class="form-control" id="admission_no" name="admission_no" value="<?php echo $student['admission_no']; ?>" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Admission No." readonly >
                    </div>
					
					<!-- <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="name" class="form-label">Select Class</label>
                      <select class="form-select" name="class_id" value="<?php echo $student['class_id']; ?>" required>
                        <option value="1" selected>Class 1st</option>
                        <option value="2" >Class 2nd</option>
                        <option value="3" >Class 3rd</option>                       
                      </select>
                    </div> -->

                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="class" class="form-label">Class Name</label>

                      <label for="class_name" class="form-label">Class</label>
                    <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo $student['class_name']; ?>" readonly placeholder="Class Name">
              <!-- Hidden field to store the class_id -->
                     <input type="hidden" name="class_id" value="<?php echo $student['class_id']; ?>">
                     
                    </div>
					
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="name" class="form-label">Student Name</label>
                      <input type="text" class="form-control" id="name" name="name" value="<?php echo $student['name']; ?>" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Student Name" required>
                    </div>
					
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="relation" class="form-label">Relation</label>
                      <select class="form-select" name="relation" id="relation" required>
                        <option value="S/O" <?php echo ($student['relation'] == 'S/O') ? 'selected' : ''; ?>>S/O</option>
                        <option value="D/O" <?php echo ($student['relation'] == 'D/O') ? 'selected' : ''; ?>>D/O</option>
                        <option value="C/O" <?php echo ($student['relation'] == 'C/O') ? 'selected' : ''; ?>>C/O</option>
                      </select>
                    </div>


                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="father_name" class="form-label">Father's Name</label>
                      <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo $student['father_name']; ?>" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Father's Name" required>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="mother_name" class="form-label">Mother's Name</label>
                      <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php echo $student['mother_name']; ?>" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Mother's Name" required>
                    </div>					
					
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="adhar_no" class="form-label">Aadhar No</label>
                      <input type="text" class="form-control" id="adhar_no" name="adhar_no" value="<?php echo $student['adhar_no']; ?>" placeholder="Enter 12 digit Aadhar No" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==12) return false;" inputmode="numeric" autocomplete="off" required>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="contact_no" class="form-label">Contact No</label>
                      <input type="text" class="form-control" id="contact_no" name="contact_no"  value="<?php echo $student['contact_no']; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" placeholder="Please enter 10 digit Mobile number" required>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="total_fee" class="form-label">Total Fees</label>
                      <input type="text" class="form-control" id="total_fee" name="total_fees" value="<?php echo $student['total_fees']; ?>" readonly oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" placeholder="Please enter fees amount" required>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="fee_change" class="form-label">Select Action</label>
                       <select class="form-select" name="fee_change" id="fee_change" required>
                        <option value="1" selected >Add</option>
                        <option value="2">Subtract</option>
                      </select>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="amount" class="form-label">Amount</label>
                      <input type="text" class="form-control" id="amount" name="amount"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" placeholder="Please enter amount" >
                    </div>
					
					<div class="mb-3 col-xxl-12 col-lg-3">
                      <label for="comment" class="form-label">Comments</label>
                      <input type="text" class="form-control" id="comment" name="comment" onkeyup="this.value=toTitleCase(this.value)" autocomplete="off" placeholder="Please enter comments" >
                    </div>					
					
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="profile_img" class="form-label">Existing Image</label>					  
                      <?php if (!empty($student['profile_img'])): ?>
							<img src="<?php echo $student['profile_img']; ?>" alt="Student Image" class="img-fluid d-block" style="max-width: 150px; max-height: 150px; border-radius: 20px;">
						<?php else: ?>
							<img src="assets/images/placeholder.png" alt="Placeholder Image" class="img-fluid" style="max-width: 150px; max-height: 150px;">
						<?php endif; ?> 
						<input type="file" class="form-control mt-3" id="profile_img" name="profile_img" accept="image/*">
                    </div>

                    <div class="mb-3">
						<button type="submit" name="submit" class="btn btn-primary">
						  Edit Student&nbsp;&nbsp;<span><i class="ti ti-check"></i></span>				  
						</button>
					</div>
					
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  </div>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
  <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="assets/js/dashboard.js"></script>

  <script>
    // Convert text to title case
    function toTitleCase(str) {
      return str.replace(/\b(\w)/g, function (char) {
        return char.toUpperCase();
      });
    }
  </script>
</body>

</html>
