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

if (isset($_POST['submit'])) {
    // Retrieve form data
    $amount = $_POST['amount'];
    $comment = $_POST['comment'];
    $email = $_SESSION['email'];  // Email from the session
    $date = $_POST['date'];       // Date from the form

    // Validate input data
    if (empty($amount) || empty($comment) || empty($date)) {
        $_SESSION['errorMessage'] = 'Please fill all fields';
        header("Location: expenditure.php"); // Redirect to the form page
        exit;
    }

    // Check if the date is empty or invalid
    if (empty($date)) {
        $_SESSION['errorMessage'] = 'Date is required';
        header("Location: expenditure.php");
        exit;
    }

    // Log the date for debugging purposes (check PHP logs if necessary)
    error_log("Date received: " . $date);

    // Prepare SQL query to insert the data into the staff_expenditure table
    $stmt = $conn->prepare("INSERT INTO staff_expenditure (date, amount, comment,session, created_by, created_at) 
                            VALUES (?, ?, ?,?, ?, NOW())");

    // Bind the parameters to the query
    $stmt->bind_param("sssss", $date, $amount, $comment,$session, $email);  // 'd' for date, 's' for string (amount, comment, email)

    // Execute the query
    if ($stmt->execute()) {
      $_SESSION['successMessage'] = 'Expenditure added successfully';
      header("Location: expenditure.php"); // Redirect back to the form page
      exit;
  } else {
      $_SESSION['errorMessage'] = 'Error occurred while adding expenditure: ' . $stmt->error;
      header("Location: expenditure.php");
      exit;
  }


    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>





<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Expenditure | The Little Legends</title>
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
        <h5 class="card-title fw-semibold mb-4">Expenditure Details</h5>
        <div class="col-lg-12">
          <!-- Display success/error messages -->
          <?php
			  // Displaying session messages if they exist
			  if (isset($_SESSION['successMessage'])) {
				  echo '<div id="sessionMessage" class="alert alert-success">' . $_SESSION['successMessage'] . '</div>';
				  unset($_SESSION['successMessage']);  // Clear the message after displaying it
			  } elseif (isset($_SESSION['errorMessage'])) {
				  echo '<div id="sessionMessage" class="alert alert-danger">' . $_SESSION['errorMessage'] . '</div>';
				  unset($_SESSION['errorMessage']);  // Clear the message after displaying it
			  }
			?>

        </div>


        <form id="studentForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    
					<div class="mb-3 col-xxl-3 col-lg-4">
						<label for="date" class="form-label">Expenditure Date</label>
						<input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>"  required>
                    </div>
					
					<div class="mb-3 col-xxl-3 col-lg-4">
                      <label for="amount" class="form-label">Expenditure Amount</label>
                      <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="if(this.value.length==12) return false;" inputmode="numeric" autocomplete="off" autofocus required >
                    </div>					
					
					<div class="mb-3 col-xxl-3 col-lg-4 position-relative">
						<label for="comment" class="form-label">Comments</label>
						<input type="text" class="form-control" id="comment" name="comment" placeholder="Enter Comments" onkeyup="this.value=toTitleCase(this.value)" autocomplete="off" required >
						<div id="nameSuggestions" class="suggestions-box"></div> 
					</div>
					
					<div class="mb-3">
						<button type="submit" name="submit" class="btn btn-primary d-block">
						  <span><i class="ti ti-plus"></i></span>&nbsp;&nbsp;Add Expenditure						  
						</button>
					</div>
					
                  </div>
                </div>
              </div>
			  
			  <!-- Table to display student details 
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="studentTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Sr&nbsp;No.</th>
                                        <th>Date</th>
										<th>Amount</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic student records will be inserted here by AJAX 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->
				
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
  // Check if session message is displayed
  if (document.getElementById("sessionMessage")) {
      // Set a timeout to hide the message after 5 seconds
      setTimeout(function() {
          document.getElementById("sessionMessage").style.display = "none";
      }, 5000);  // 5000 milliseconds = 5 seconds
  }
  function toTitleCase(str) {
      return str.replace(/\b(\w)/g, function (char) {
        return char.toUpperCase();
      });
    }
</script>
  
</body>

</html>
