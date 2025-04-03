<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$payFees = "active";
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pay Fee | The Little Legends</title>
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
      <h5 class="card-title fw-semibold mb-4">Search Student and Pay Fees</h5>

      <div class="card mb-4">
      
        <div class="card-body">  
        <div id="messageDiv"  style="display: none;"></div>      
            <form id="searchForm">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <!-- Student Name -->
                            <div class="mb-3 col-xxl-9 col-lg-9 position-relative">
                                <label for="name" class="form-label">Search Student</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name, Admission No., etc." autocomplete="off" required autofocus >
                                <div class="suggestions-box" id="nameSuggestions"></div>
                            </div>
                        </div> 
                    </div>
                </div>                        
            </form>

            <form id="pay_student_fee"  method="POST">
    <div class="row">
        <!-- Total Fees -->
        <div class="mb-3 col-xxl-3 col-lg-3 position-relative">
            <label for="total_fees" class="form-label">Total Fees</label>
            <input type="text" class="form-control" id="total_fees" name="total_fees" placeholder="Total Fees" autocomplete="off" required disabled>
        </div>
        
        <!-- Pending Fees -->
        <div class="mb-3 col-xxl-3 col-lg-3 position-relative">
            <label for="balance" class="form-label">Pending Fees</label>
            <input type="text" class="form-control" id="balance" name="balance" placeholder="Pending Fees" autocomplete="off" required disabled>
        </div>

        <!-- Pay Amount -->
        <div class="mb-3 col-xxl-3 col-lg-3 position-relative">
            <label for="amount" class="form-label">Pay Amount</label>
            <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" required>
        </div>

        <!-- Payment Mode -->
        <div class="mb-3 col-xxl-3 col-lg-3 position-relative">
            <label for="payment_mode" class="form-label">Payment Mode</label>
            <select class="form-select" name="payment_mode" id="payment_mode" required>
                <option value="cash" selected>Cash</option>
                <option value="online">Online</option>
                <option value="bank">AXIS Bank</option>
                <option value="bank">SBI</option>
            </select>
        </div>

        <!-- Hidden field to store student ID -->
        <input type="hidden" id="student_id" name="student_id" value="">

        <!-- Submit Button -->
        <div class="mb-3 col-xxl-6 col-lg-3">
            <button type="submit" class="btn btn-primary d-block"> 
                Pay Fees&nbsp;&nbsp;<span><i class="ti ti-check"></i></span>
            </button>
        </div>
    </div>
</form>
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
                url: 'search_students_for_pay.php',  // PHP file that handles the search
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
        var selectedName = $(this).text();  // Get the name from the clicked suggestion
        var studentId = $(this).data('id');  // Get the student ID from the clicked suggestion

        $('#name').val(selectedName);  // Set the input field to the selected name
    $('#student_id').val(studentId);  // Set the student ID in the hidden field
    $('#nameSuggestions').hide();  // Hide the suggestions box

        // Now submit the form to fetch and display the student details in the fields
        $.ajax({
            url: 'pay_student_fee.php',  // PHP file that fetches full student details
            type: 'POST',
            data: { id: studentId },  // Send the selected student's ID
            success: function(response) {
                var studentData = JSON.parse(response);  // Assuming the response is JSON
                
                if (studentData.error) {
                    alert(studentData.error);
                } else {
                    // Populate the form with the student data
                    $('#total_fees').val(studentData.totalFees);
                    $('#balance').val(studentData.balance);
                     // Enable the 'amount' field
                }
            },
            error: function() {
                alert('Error fetching student details.');
            }
        });
    });

    // Hide suggestions when clicking outside the input field
    $(document).click(function(event) {
        if (!$(event.target).closest('#name, #nameSuggestions').length) {
            $('#nameSuggestions').hide();
        }
    });

        
   
      

    });


    $(document).ready(function() {
    // Handle form submission
    $('#pay_student_fee').on('submit', function(e) {
        e.preventDefault();  // Prevent the default form submission

        // Get form data
        var formData = $(this).serialize();  // Serialize form data for the AJAX request
        console.log( formData);
        // Clear previous messages
        $('#messageDiv').html('').hide();

        // Send AJAX request
        $.ajax({
            url: 'process_payment.php',  // PHP script to handle the payment
            type: 'POST',
            data: formData,  // Send form data
            dataType: 'json',  // Expect JSON response
            success: function(response) {
                // Clear previous messages
                $('#messageDiv').html('').show();

                // Check if the response status is success
                if (response.status === 'success') {
                    // Show success message in the suggestions box
                    $('#messageDiv').html('<div class="alert alert-success">' + response.message + '</div>').show();
                    $('#pay_student_fee')[0].reset();
					$('#searchForm')[0].reset();
                    setTimeout(function() {
                                $('#messageDiv').fadeOut();
                            }, 5000);
                } else {
                    // Show error message in the suggestions box
                    $('#messageDiv').html('<div class="alert alert-danger">' + response.message + '</div>').show();
                    setTimeout(function() {
                                $('#messageDiv').fadeOut();
                            }, 5000);
                }
            },
            error: function() {
                // Display a generic error message in case of AJAX failure
                $('#messageDiv').html('<div class="alert alert-danger">There was an error processing your request. Please try again.</div>').show();
                setTimeout(function() {
                                $('#messageDiv').fadeOut();
                            }, 5000);
            }
        });
    });

    // Hide suggestions when clicking outside the input field
    $(document).click(function(event) {
        if (!$(event.target).closest('#name, #messageDiv').length) {
            $('#messageDiv').hide();
        }
    });
});



  </script>
</body>
</html>
