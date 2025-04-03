<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}
include('db_connect.php');
$sql = "SELECT * FROM session WHERE status = 1 LIMIT 1"; // Only get the active session
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Session is found, get the session data
    $sessionData = mysqli_fetch_assoc($result);
    $session = $sessionData['session'];  // E.g., "2025-26"
} else {
    // No active session found, handle accordingly
    $_SESSION['errorMessage'] = 'No active session found.';
    header('Location: dashboard.php');
    exit;
}

$query = "SELECT * FROM class "; // Only fetch active classes
$result = mysqli_query($conn, $query);
$classes = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Student | The Little Legends</title>
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
    
    <div class="container-fluid" style="max-width: 100%;">
      <div class="row">
        <h5 class="card-title fw-semibold mb-4">Add Student for the Session <?php echo $session; ?></h5>
        <div class="col-lg-12">
          <!-- Display success/error messages -->
          <?php
              if (isset($_SESSION['successMessage'])) {
                  echo '<div class="alert alert-success">' . $_SESSION['successMessage'] . '</div>';
                  unset($_SESSION['successMessage']);
              } elseif (isset($_SESSION['errorMessage'])) {
                  echo '<div class="alert alert-danger">' . $_SESSION['errorMessage'] . '</div>';
                  unset($_SESSION['errorMessage']);
              }
          ?>
        </div>


        <?php if (isset($sessionData)): ?>

          <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
        <div id="messageContainer" class="alert alert-info" style="display: none;"></div>
        <form id="studentForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <input type="hidden" id="sessionStatus" value="<?php echo isset($sessionData) ? 'active' : 'inactive'; ?>">

            <!-- Admission No -->
            <div class="mb-3 col-xxl-3 col-lg-4">
              <label for="admission_no" class="form-label">Admission No.</label>
              <input type="text" class="form-control" id="admission_no" name="admission_no" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Admission No." required>
            </div>

            <!-- Class Selection (dynamic from database) -->
            <div class="mb-3 col-xxl-3 col-lg-4">
              <label for="class" class="form-label">Select Class</label>
              
              <select class="form-select" name="class" id="class" required>
              <option value="" selected disabled>Select Class</option>
                <?php foreach ($classes as $class): ?>
                  <option value="<?php echo $class['id']; ?>"><?php echo $class['class_name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
					
                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="name" class="form-label">Student Name</label>
                      <input type="text" class="form-control" id="name" name="name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Student Name" required>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="relation" class="form-label">Relation</label>
                      <select class="form-select" name="relation" id="status" required>
                        <option value="S/O" selected >S/O</option>
                        <option value="D/O">D/O</option>
                        <option value="C/O">C/O</option>
                      </select>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="father_name" class="form-label">Father's Name</label>
                      <input type="text" class="form-control" id="father_name" name="father_name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Father's Name" required>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="mother_name" class="form-label">Mother's Name</label>
                      <input type="text" class="form-control" id="mother_name" name="mother_name" onkeyup="this.value=toTitleCase(this.value)" placeholder="Enter Mother's Name" required>
                    </div>					
					
                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="adhar_no" class="form-label">Aadhar No</label>
                      <input type="text" class="form-control" id="adhar_no" name="adhar_no" placeholder="Enter 12 digit Aadhar No" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==12) return false;" inputmode="numeric" autocomplete="off" required>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="contact_no" class="form-label">Contact No</label>
                      <input type="text" class="form-control" id="contact_no" name="contact_no" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" placeholder="Please enter 10 digit Mobile number" required>
                    </div>

                    <div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="profile_img" class="form-label">Student Image</label>
                      <input type="file" class="form-control" id="profile_img" name="profile_img" accept="image/*">
                    </div>
					
                    <div class="mb-3 col-xxl-3 col-lg-4">
					  <label for="total_fees" class="form-label">Total Fees</label>
					  <input type="text" class="form-control" id="total_fees" name="total_fees" readonly placeholder="Total Fees will be displayed here" required>
					</div>
                    
					<div class="mb-3">
						<button type="submit" name="submit" class="btn btn-primary">
						  <span><i class="ti ti-plus"></i></span>&nbsp;&nbsp;Add Student						  
						</button>
					</div>
					
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>

        <?php else: ?>
  <div class="alert alert-danger mt-4">No active session available. Please contact admin.</div>
<?php endif; ?>
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

if ($('#sessionStatus').val() !== 'active') {
  $('#messageContainer').removeClass('alert-success alert-danger').addClass('alert-danger')
                        .text('No active session found for the year. Cannot submit the form.')
                        .show();
  return;  // Prevent form submission
}

// Handle form submission via AJAX
$('#studentForm').on('submit', function (e) {
  e.preventDefault();  // Prevent default form submission

  var formData = new FormData(this);  // Collect all form data including file

  // Log form data to console for debugging
  for (var [key, value] of formData.entries()) { 
    console.log(key + ": " + value);  // Print each key-value pair in the form data
  }

  // Proceed with the AJAX request
  $.ajax({
    url: 'save_student.php',  // PHP file to handle form submission
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      console.log(response);  // Log the raw response for debugging
      try {
        var responseData = JSON.parse(response);  // Parse the JSON response
        if (responseData.success) {
          $('#messageContainer').removeClass('alert-danger').addClass('alert-success')
                                .text(responseData.message)
                                .show();
                                $('#studentForm')[0].reset();
                                setTimeout(function() {
                                $('#messageContainer').fadeOut();
                            }, 5000);
                            
        } else {
          $('#messageContainer').removeClass('alert-success').addClass('alert-danger')
                                .text("Error: " + responseData.message)
                                .show();
                                setTimeout(function() {
                                $('#messageContainer').fadeOut();
                            }, 5000);
        }
      } catch (error) {
        console.error("Error parsing response:", error);
        $('#messageContainer').removeClass('alert-success').addClass('alert-danger')
                              .text('Failed to parse response. Check the server logs.')
                              .show();
                              setTimeout(function() {
                                $('#messageContainer').fadeOut();
                            }, 5000);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX error:", textStatus, errorThrown);
      $('#messageContainer').removeClass('alert-success').addClass('alert-danger')
                            .text('An error occurred! Check console for details.')
                            .show();
                            setTimeout(function() {
                                $('#messageContainer').fadeOut();
                            }, 5000);
    }
  });
});

});

  

  
  document.getElementById('class').addEventListener('change', function() {
    var classId = this.value;  // Get selected class ID

    // Make an AJAX call to get the fees for the selected class
    $.ajax({
      url: 'get_class_fees.php', // PHP file to fetch fees based on class ID
      type: 'GET',
      data: { class_id: classId },
      success: function(response) {
        var data = JSON.parse(response);
        if (data.success) {
          // Update the Total Fees input field
          document.getElementById('total_fees').value = data.fees;
        } else {
          alert('Error fetching fees data.');
        }
      },
      error: function(xhr, status, error) {
        console.log('Error: ' + error);
        alert('Failed to fetch fees.');
      }
    });
  });
</script>

</body>

</html>
