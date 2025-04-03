<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$viewStudents = "active";
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Students | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <style>
    .form-select {
      padding: 12px 16px;
    }

    .body-wrapper > .container-fluid.x-padd {
      padding: 36px;}

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
	
	.form-select {
      padding: 12px 16px;
    
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
      <h5 class="card-title fw-semibold mb-4">View Student Details</h5>
      <div class="card mb-4">
        <div class="card-body">
        <div class="col-lg-12">
          <!-- Display success/error messages -->
          <?php
              if (isset($_SESSION['successMessage'])) {
                  echo '<div id="sessionMessage" class="alert alert-success">' . $_SESSION['successMessage'] . '</div>';
                  unset($_SESSION['successMessage']);
              } elseif (isset($_SESSION['errorMessage'])) {
                  echo '<div id="sessionMessage" class="alert alert-danger">' . $_SESSION['errorMessage'] . '</div>';
                  unset($_SESSION['errorMessage']);
              }
          ?>
        </div>

            <form id="searchForm">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <!-- Student Name -->
                            <div class="mb-3 col-xxl-6 col-lg-7 position-relative">
                                <label for="name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Student Name" autocomplete="off" required autofocus >
                                <div id="nameSuggestions" class="suggestions-box"></div>
                            </div>
                        </div>                          
                    </div>
                </div>                        
            </form>
        </div>
      </div>

      <!-- Student Records Table -->
      <div class="card mb-4" id="studentTableDiv">
          <div class="card-body">
              <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="studentTable">
                      <thead class="thead-dark">
                          <tr>
                              <!--<th>Sr&nbsp;No.</th>-->
							  <th>Admission&nbsp;No</th>
                              <th>Profile&nbsp;Image</th>							  
                              <th>Student&nbsp;Name</th>							  
                              <th>Aadhar&nbsp;No</th>
                              <th>Father&nbsp;Name</th>
                              <th>Mother&nbsp;Name</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="studentTableBody">
                          <!-- Dynamic content will go here -->
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/app.min.js"></script>


    <script>
$(document).ready(function() {
    // When the user types in the name input field
    $('#name').on('keyup', function() {
        var query = $(this).val();  // Get the current value of the input field
        
        if (query.length >= 1) {  // Start searching after 3 characters
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
            $('#nameSuggestions').hide();  // Hide the suggestions box if less than 3 characters
        }
    });

    // When the user clicks on a suggestion
    $(document).on('click', '.suggestion-item', function() {
        var selectedName = $(this).text();
        var studentId = $(this).data('id');  // Get the student ID from the clicked suggestion

        $('#name').val(selectedName);  // Set the input field to the selected name
        $('#nameSuggestions').hide();  // Hide the suggestions box

        // Now submit the form to fetch and display the student details in the table
        $.ajax({
            url: 'fetch_student_details.php',  // PHP file that fetches full student details
            type: 'POST',
            data: { id: studentId },  // Send the selected student's ID
            success: function(response) {
                // Populate the student table with the student details
                // $('#studentTable tbody').html(response);
                if (response) {
              $('#studentTable tbody').html(response);
              $('#studentTableDiv').show(); // Show the table only when records are returned
            }
          },
            // },
            error: function() {
                alert('Error fetching student details.');
            }
        });
    });
	
	if (document.getElementById("sessionMessage")) {
      // Set a timeout to hide the message after 5 seconds
      setTimeout(function() {
          document.getElementById("sessionMessage").style.display = "none";
      }, 5000);  // 5000 milliseconds = 5 seconds
  }

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
