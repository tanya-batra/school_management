<?php
session_start();

// Include database connection file
include('db_connect.php');

// Check if the user is logged in
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
    <title>Add Class and Fees | The Little Legends</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css" />
    <style>.display-none {display: none;}</style>
</head>
<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
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
                <h5 class="card-title fw-semibold mb-4">Add Class and Fees</h5>

                <!-- Display success/error message from session -->
                <div id="messageDiv" class="alert alert-info" style="display: none;"></div>

                <!-- Add Class and Fees Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="add_fee">
                            <div class="row">
                                <div class="mb-3 col-xxl-4 col-lg-4 position-relative">
                                    <label for="class_name" class="form-label">Class Name</label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" placeholder="Enter Class Name" onkeyup="this.value=toTitleCase(this.value)" autocomplete="off" required autofocus>
                                </div>

                                <div class="mb-3 col-xxl-4 col-lg-4 position-relative">
                                    <label for="fees" class="form-label">Yearly Fees</label>
                                    <input type="text" class="form-control" id="fees" name="fees"  placeholder="Enter Fees Amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" required>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary d-block">
                                        Submit&nbsp;&nbsp;<span><i class="ti ti-check"></i></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table to display class details -->
                <div class="card mb-4 display-none">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="studentTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Sr&nbsp;No.</th>
                                        <th>Class Name</th>
                                        <th>Yearly Fees</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Table data will be populated by AJAX -->
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <nav>
                                <ul class="pagination" id="pagination">
                                    <!-- Pagination links will be added here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
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
    <!-- JS Includes -->


    <script>
        // Convert input text to title case (first letter of each word capitalized)
        function toTitleCase(str) {
            return str.replace(/\b(\w)/g, function(s) {
                return s.toUpperCase();
            });
        }
	</script>
	
	<script>
        $(document).ready(function() {
            // Handling form submission using AJAX
            $('#add_fee').on('submit', function(e) {
                e.preventDefault(); // Prevent the form from submitting normally

                var className = $('#class_name').val();
                var fees = $('#fees').val();

                if (className == '' || fees == '') {
                    $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text('Please fill all fields.').show();
                    setTimeout(function() {
                        $('#messageDiv').fadeOut();
                    }, 5000); // Shorten timeout
                    return;
                }

                // AJAX request to save data in the database
                $.ajax({
                    url: 'save_class_fees.php',
                    type: 'POST',
                    data: {
                        class_name: className,
                        fees: fees
                    },
                    success: function(response) {
                        if (response == 'success') {
                            $('#messageDiv').removeClass('alert-danger').addClass('alert-success').text('Class added successfully!').show();
                            $('#add_fee')[0].reset();
                            fetchData(1); // Reload the table
                            setTimeout(function() {
                                $('#messageDiv').fadeOut();
                            }, 5000); // Shorten timeout
                        } else {
                            $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text(response).show();
                            setTimeout(function() {
                                $('#messageDiv').fadeOut();
                            }, 5000); // Shorten timeout
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text('An error occurred: ' + error).show();
                        setTimeout(function() {
                            $('#messageDiv').fadeOut();
                        }, 5000); // Shorten timeout
                    }
                });
            });

           
            // Call this function on initial load (you already did this)
			fetchData(1);

			// Function to fetch data with pagination
			function fetchData(page) {
			$.ajax({
				url: 'fetch_classes.php',
				type: 'GET',
				data: {
					page: page
				},				
				success: function(response) {
					const data = JSON.parse(response);

					// Get the limit per page from the data, or set a default value
					const limit = data.limit || 5;  // Default limit is 10 if not provided
					const offset = (page - 1) * limit;  // Calculate offset for pagination

					// Update the table with fetched data
					let tableRows = '';
					let rowNumber = offset + 1; // Initialize row number to start from the correct number
					data.classes.forEach(function(classData) {
						tableRows += `<tr>
							<td>${rowNumber}</td> <!-- Auto-increment row number -->
							<td>${classData.class_name}</td>
							<td>${classData.fees}</td>
							<td><a href="edit-class-and-fees.php?id=${classData.id}" class="btn btn-primary"><i class="ti ti-pencil"></i>&nbsp;&nbsp;Edit</a></td>
						</tr>`;
						rowNumber++; // Increment the row number for each class
					});
					//$('#tableBody').html(tableRows);
					if (tableRows) {
						$('#tableBody').html(tableRows);
						$('.display-none').show();
					}
					
					// Update pagination links
					let paginationLinks = '';
					for (let i = 1; i <= data.totalPages; i++) {
						paginationLinks += `<li class="page-item ${i === data.currentPage ? 'active' : ''}">
							<a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
						</li>`;
					}
					$('#pagination').html(paginationLinks);

					// Attach click event dynamically to pagination links
					$('#pagination .page-link').on('click', function() {
						const page = $(this).data('page');
						fetchData(page);
					});					
				},
				error: function(xhr, status, error) {
					console.error("AJAX error: " + status + " " + error);
				}
			});
		}


        });
    </script>
</body>
</html>
