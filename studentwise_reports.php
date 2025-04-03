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
  <title>Studentwise Reports | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <style>
    .form-select {
      padding: 12px 16px;
    }

    .body-wrapper > .container-fluid.x-padd {
      padding: 36px;
    }
	.suggestions-box {
      border: 1px solid #ccc;
      max-height: 150px;
      overflow-y: auto;
      display: none;
      position: absolute;
      background-color: white;
      width: calc(100% - 24px);
      z-index: 1000;
      left: 12px;
    }

    .suggestions-box div {
        padding: 8px;
        cursor: pointer;
    }

    .suggestions-box div:hover {
        background-color: #f1f1f1;
    }
	#studentTableDiv {
		display: none;
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
        <h5 class="card-title fw-semibold mb-4">Studentwise Reports</h5>
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

        <form id="searchForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    
					<div class="mb-3 col-xxl-6 col-lg-6 position-relative">
						<label for="name" class="form-label">Student Name</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="Enter Student Name" autocomplete="off" required autofocus >
						<div id="nameSuggestions" class="suggestions-box"></div> 
					</div>
					
          <br><br>
               <div>
                <input type="hidden" name="id" id="studentId">
                <a href="export_studentwise_report.php?action=export_pdf&id=" class="btn btn-danger" id="exportPDF">
                 <i class="ti ti-download"></i>&nbsp;&nbsp;Export to PDF
                </a>&nbsp;

                <a href="export_studentwise_report.php?action=export_excel&id=" class="btn btn-primary" id="exportExcel">
                <i class="ti ti-download"></i>&nbsp;&nbsp;Export to Excel (CSV)
                 </a>
                                          
                     </div>

					
                  </div>
                </div>
              </div>
			  
			  <!-- Table to display student details -->
                <div class="card mb-4" id="studentTableDiv">
                    <!--<div class="card-header">
                        <strong>Student Details</strong>
                    </div>-->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="studentTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Sr&nbsp;No.</th>
								                   		  <th>Paid&nbsp;On</th>
									                     	<th>Admission&nbsp;No.</th>
                                        <th>Class</th>
                                        <th>Student&nbsp;Name</th>
							                     			<th>Father&nbsp;Name</th>
                                        <th>Contact&nbsp;No</th>
                                        <th>Fee&nbsp;Paid</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic student records will be inserted here by AJAX -->
                                     <tr>
                                      <th colspan="7" style="text-align:right;">Total&nbsp;Fee&nbsp;Paid </th>
                                      <td></td>
                                     </tr>
                                     <tr>
                                      <th colspan="7" style="text-align:right;">Total&nbsp;Fee&nbsp;Pending </th>
                                      <td></td>
                                     </tr>
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
$(document).ready(function() {
    // When the user types in the name input field
    $('#name').on('keyup', function() {
        var query = $(this).val();  // Get the current value of the input field
        
        if (query.length >= 1) {  // Start searching after 1 character
            $.ajax({
                url: 'search_students.php',  // The PHP file that handles the search
                type: 'GET',
                data: { name: query },  // Send the query string to the server
                success: function(response) {
                    // Display the returned suggestions in the suggestions box
                    $('#nameSuggestions').html(response).show();
                },
                error: function() {
                    alert('Error fetching suggestions.');
                }
            });
        } else {
            $('#nameSuggestions').hide();  // Hide the suggestions box if less than 1 character
        }
    });

    // When the user clicks on a suggestion
    $(document).on('click', '.suggestion-item', function() {
        var selectedName = $(this).text();
        var studentId = $(this).data('id');  // Get the student ID from the clicked suggestion

        // Set the student ID to the hidden input field
        $('#studentId').val(studentId);  

        // Set the input field to the selected name
        $('#name').val(selectedName);
        $('#nameSuggestions').hide();  // Hide the suggestions box

        // Now submit the form to fetch and display the student details in the table
        $.ajax({
            url: 'fetch_studentwise_reports.php',  // PHP file that fetches full student details
            type: 'POST',
            data: { id: studentId },  // Send the selected student's ID
            success: function(response) {
                // Populate the student table with the student details
                if (response) {
                    $('#studentTable tbody').html(response);
                    $('#studentTableDiv').show(); // Show the table only when records are returned
                }
            },
            error: function() {
                alert('Error fetching student details.');
            }
        });

        // Update export links to include the student ID
        var studentId = $('#studentId').val(); // Get the selected student ID
        $('#exportPDF').attr('href', 'export_studentwise_report.php?action=export_pdf&id=' + studentId);
        $('#exportExcel').attr('href', 'export_studentwise_report.php?action=export_excel&id=' + studentId);
    });

    // Hide suggestions when clicking outside the input field
    $(document).click(function(event) {
        if (!$(event.target).closest('#name, #nameSuggestions').length) {
            $('#nameSuggestions').hide();
        }
    });
});

  </script>   
</body>

</html>
