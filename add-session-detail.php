<?php
session_start();

// Include database connection file
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$student = null; // Initialize variable to avoid undefined variable warning

// Check if 'student_id' is passed via GET and is numeric
if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id); // "i" denotes integer type
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Debugging: Check if the query was successful
    if ($result === false) {
        error_log("SQL query failed: " . $conn->error); // Log any query failure
    }

    // Fetch student data if found
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        error_log("No student found for ID: " . $student_id); // Log if no results were found
    }

    // Assign values to variables if student data exists
    if ($student) {
        $status = $student['status'];
        $adhar_no = $student['adhar_no'];
        $name = $student['name'];
        $father_name = $student['father_name'];
        $mother_name = $student['mother_name'];
        $contact_no = $student['contact_no'];
        $profile_img = $student['profile_img']; // Assuming the profile image is stored as the filename or path
    } else {
        // Set default empty values if no student data found
        $status = "";
        $adhar_no = "";
        $name = "";
        $father_name = "";
        $mother_name = "";
        $contact_no = "";
        $profile_img = "";
    }

    $stmt->close();
} else {
    error_log("Invalid or missing ID parameter."); // Log if the ID is invalid or missing
}
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Student | Your Website</title>
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
        <h5 class="card-title fw-semibold mb-4">Add Student</h5>
        <div class="col-lg-12">
          <!-- Display success/error messages -->
          <?php
              if (isset($_SESSION['successMessage'])) {
                  echo '<div class="alert alert-success mt-4">' . $_SESSION['successMessage'] . '</div>';
                  unset($_SESSION['successMessage']);
              } elseif (isset($_SESSION['errorMessage'])) {
                  echo '<div class="alert alert-danger mt-4">' . $_SESSION['errorMessage'] . '</div>';
                  unset($_SESSION['errorMessage']);
              }
          ?>
        </div>

        <?php

?>

<!-- Form HTML: Populate fields with data -->
<form id="studentForm" method="POST" enctype="multipart/form-data">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="mb-3 col-xxl-2">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" name="status" id="status" disabled required>
                <option value="1" <?php echo ($status == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo ($status == 0) ? 'selected' : ''; ?>>Inactive</option>
              </select>
            </div>

            <div class="mb-3 col-xxl-2">
              <label for="adhar_no" class="form-label">Aadhar No</label>
              <input type="text" class="form-control" disabled id="adhar_no" name="adhar_no" placeholder="Enter Aadhar No" value="<?php echo htmlspecialchars($adhar_no); ?>" required>
            </div>

            <div class="mb-3 col-xxl-2">
              <label for="name" class="form-label">Student Name</label>
              <input type="text" class="form-control" disabled id="name" name="name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Student Name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="mb-3 col-xxl-2">
              <label for="father_name" class="form-label">Father's Name</label>
              <input type="text" class="form-control" disabled id="father_name" name="father_name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Father's Name" value="<?php echo htmlspecialchars($father_name); ?>" required>
            </div>

            <div class="mb-3 col-xxl-2">
              <label for="mother_name" class="form-label">Mother's Name</label>
              <input type="text" class="form-control" disabled id="mother_name" name="mother_name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Mother's Name" value="<?php echo htmlspecialchars($mother_name); ?>" required>
            </div>

            <div class="mb-3 col-xxl-2">
              <label for="contact_no" class="form-label">Contact No</label>
              <input type="text" class="form-control" disabled id="contact_no" name="contact_no" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" placeholder="Please enter 10 digit Mobile number" value="<?php echo htmlspecialchars($contact_no); ?>" required>
            </div>

            <hr>

           
          </div>
        </div>
      </div>
    </div>
  </div>
</form>



<form id="sessionForm" method="POST">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <!-- Session -->
            <div class="mb-3 col-xxl-6">
              <label for="session" class="form-label">Session</label>
              <input type="text" class="form-control" id="session" name="session" placeholder="Enter Session" required>
            </div>

            <!-- Class -->
            <div class="mb-3 col-xxl-6">
              <label for="class" class="form-label">Class</label>
              <input type="text" class="form-control" id="class" name="class" placeholder="Enter Class" required>
            </div>

            <!-- Section -->
            <div class="mb-3 col-xxl-6">
              <label for="section" class="form-label">Section</label>
              <input type="text" class="form-control" id="section" name="section" placeholder="Enter Section" required>
            </div>

            <!-- Total Fee -->
            <div class="mb-3 col-xxl-6">
              <label for="total_fee" class="form-label">Total Fee</label>
              <input type="number" class="form-control" id="total_fee" name="total_fee" placeholder="Enter Total Fee" required>
            </div>
            
            <br>
            <button type="submit" name="submit" class="btn btn-primary">
              Add Session Detail &nbsp;
              <span><i class="ti ti-check"></i></span>
            </button>
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

    $(document).ready(function () {
  // Handle form submission via AJAX
  $('#sessionForm').on('submit', function (e) {
    e.preventDefault();  // Prevent default form submission

    var formData = {
      student_id: <?php echo $student_id; ?>,  // Pass the student ID
      session: $('#session').val(),
      class: $('#class').val(),
      section: $('#section').val(),
      total_fee: $('#total_fee').val()
    };

    // Log form data to console for debugging
    console.log(formData);

    // Proceed with the AJAX request
    $.ajax({
      url: 'save_session.php',  // PHP file to handle form submission
      type: 'POST',
      data: formData,
      success: function (response) {
        console.log(response);  // Log the raw response for debugging
        
        try {
          var responseData = JSON.parse(response);  // Parse the JSON response
          if (responseData.success) {
            alert(responseData.message);  // Success alert
            // Optionally reset the form or update the UI here
            $('#sessionForm')[0].reset();
          } else {
            alert("Error: " + responseData.message); // Detailed error message
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          alert('Failed to parse response. Check the server logs.');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX error:", textStatus, errorThrown);
        alert('An error occurred! Check console for details.');
      }
    });
  });
});


  </script>
</body>

</html>

