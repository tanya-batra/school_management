<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}
?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monthwise Reports | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
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
        <h5 class="card-title fw-semibold mb-4">Monthwise Reports</h5>
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

        <form id="studentForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    
					<div class="mb-3 col-xxl-3 col-lg-3">
						<label for="status" class="form-label">Choose Month</label>
						<select class="form-select" name="class" required>
							<option value="Jan" selected>Jan</option>
							<option value="Feb" >Feb</option>
							<option value="March" >March</option>                       
						  </select>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-3">
						<label for="status" class="form-label">&nbsp;</label>
						<button type="submit" name="submit" class="btn btn-primary d-block">
						  <span><i class="ti ti-search"></i></span>&nbsp;&nbsp;Search Record						  
						</button>
					</div>
					
                  </div>
                </div>
              </div>
			  
			  <!-- Table to display student details -->
                <div class="card mb-4">
                    <!--<div class="card-header">
                        <strong>Student Details</strong>
                    </div>-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="studentTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Sr&nbsp;No.</th>
										<th>Date</th>
                                        <th>Month</th>
                                        <th>Payment&nbsp;Mode</th>
                                        <th>Amount&nbsp;Received</th>
                                        <th>Pending&nbsp;Amount</th>
                                        <th>Expenditure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic student records will be inserted here by AJAX -->
                                </tbody>
                            </table>
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
            alert(responseData.message);  // Success alert
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
