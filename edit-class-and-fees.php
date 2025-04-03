<?php
session_start();

// Include database connection file
$viewFees = "active";
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}


$class_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the class details from the database
if ($class_id) {
    $query = "SELECT * FROM class WHERE id = $class_id";
    $result = mysqli_query($conn, $query);
    $classData = mysqli_fetch_assoc($result);
} else {
    // Redirect to the main page if no ID is passed
    header("Location: index.php");
    exit;
}


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Class and Fees | The Little Legends</title>
    <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="assets/css/styles.min.css" />
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
                <h5 class="card-title fw-semibold mb-4">Edit Class and Fees</h5>


                <!-- Message Div for Success/Error -->
                <div id="messageDiv" class="alert" style="display: none;"></div>

                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="edit_fee">
                            <div class="row">

                                <div class="mb-3 col-xxl-4 col-lg-4 position-relative">
                                    <label for="class_name" class="form-label">Class Name</label>
                                    <input type="text" class="form-control" id="class_name" disabled name="class_name" value="<?php echo $classData['class_name']; ?>" placeholder="Enter Class Name" onkeyup="this.value=toTitleCase(this.value)" autocomplete="off" required >
                                </div>
								
								<div class="mb-3 col-xxl-4 col-lg-4 position-relative">
                                    <label for="fees" class="form-label">Fees</label>
                                    <input type="text" class="form-control" id="fees" name="fees" value="<?php echo $classData['fees']; ?>" step="0.01" placeholder="Enter Fees Amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==10) return false;" inputmode="numeric" autocomplete="off" required >
                                </div>

                                <input type="hidden" name="class_id" value="<?php echo $classData['id']; ?>"> <!-- Hidden input for class ID -->
								
								<div class="mb-3">
									<button type="submit" class="btn btn-primary d-block"> 
										Update Fees&nbsp;&nbsp;<span><i class="ti ti-check"></i></span>
									</button>
								</div>
								
								</div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

 <!-- JS Includes -->
 <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/app.min.js"></script>

    <script>
$(document).ready(function() {
    $('#edit_fee').on('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting normally

        var fees = $('#fees').val(); // Get the value of fees
        var classId = $('input[name="class_id"]').val(); // Get the class ID

        // Check if fees field is empty
        if (fees == '') {
            $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text('Please fill all fields.').show();
            setTimeout(function() {
                $('#messageDiv').fadeOut();
            }, 10000); // Hide message after 10 seconds
            return;
        }

        // Send AJAX request to update data in the database
        $.ajax({
            url: 'update_class_fees.php', // Backend PHP file to handle the update
            type: 'POST',
            data: {
                class_id: classId,
                fees: fees
            },
            success: function(response) {
                if (response == 'success') {
                    // Show success message
                    $('#messageDiv').removeClass('alert-danger').addClass('alert-success').text('Class updated successfully!').show();
                    setTimeout(function() {
                        $('#messageDiv').fadeOut();
                    }, 10000); // Hide message after 10 seconds

                    // Redirect to add-class-and-fees.php after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'add-class-and-fees.php'; // Redirect after success
                    }, 2000); // Adjust delay as needed
                } else {
                    // Show error message if the response is not 'success'
                    $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text(response).show();
                }
            },
            error: function(xhr, status, error) {
                $('#messageDiv').removeClass('alert-success').addClass('alert-danger').text('An error occurred: ' + error).show();
            }
        });
    });
});

    </script>

</body>
</html>