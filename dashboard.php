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


$total_student_query = "SELECT COUNT(id) AS total_total_students FROM students WHERE session = '$session' "; // Replace 'notices' with your actual table
$total_student_result = $conn->query($total_student_query);
$total_student_data = $total_student_result->fetch_assoc();
$total_total_student = $total_student_data['total_total_students'];


$total_class_query = "SELECT COUNT(id) AS total_class FROM class  "; // Replace 'notices' with your actual table
$total_class_result = $conn->query($total_class_query);
$total_class_data = $total_class_result->fetch_assoc();
$total_class = $total_class_data['total_class'];

$total_student_fee_paid_query = "SELECT SUM(amount) AS total_student_fee_paid FROM fee_transactions WHERE session = '$session'";
$total_student_fee_paid_result = $conn->query($total_student_fee_paid_query);
$total_student_fee_paid_data = $total_student_fee_paid_result->fetch_assoc();
$total_student_fee_paid = $total_student_fee_paid_data['total_student_fee_paid'];


$total_staff_expenditure_query = "SELECT SUM(amount) AS total_staff_expenditure FROM staff_expenditure WHERE session = '$session'";
$total_staff_expenditure_result = $conn->query($total_staff_expenditure_query);
$total_staff_expenditure_data = $total_staff_expenditure_result->fetch_assoc();
$total_staff_expenditure = $total_staff_expenditure_data['total_staff_expenditure'];


$total_fee_query = "SELECT SUM(total_fees) AS total_fee_paid FROM students WHERE session = '$session'"; 
$total_fee_result = $conn->query($total_fee_query);
$total_fee_data = $total_fee_result->fetch_assoc();
$total_fee_paid = $total_fee_data['total_fee_paid'];

$total_balance_query = "SELECT SUM(balance) AS total_balance FROM students WHERE session = '$session'";
$total_balance_result = $conn->query($total_balance_query);
$total_balance_data = $total_balance_result->fetch_assoc();
$total_balance = $total_balance_data['total_balance'];

?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <style>
	.dash-icons { color: #fff; font-size: 2.8rem;}
	.justify-self-start {
		justify-self: start;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
		padding: 0 0 0 1.5rem;
	}
	svg { width: 24px; height: 24px;}
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="dashboard.php" class="text-nowrap logo-img">
            <img src="assets/images/logo.png" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar Include -->
        <?php include('sidebar.php') ?>
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <?php include('header.php') ?>
      <!--  Header End -->
      <div class="container-fluid" style="max-width: 100%;">
        <!--  Row 1 -->
        <div class="row">
          <div class="col-lg-12">
            <h2 class="fw-semibold mt-2 mb-1">Welcome to <i style="color: #EB2690;">The Little Legends</i></h2>
			<h6>A montessori pe-school &nbsp;&nbsp;<?php echo $session; ?></h6>
            <div class="row">
				<div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #2FBF71;">                   
					<a href="add-student.php">
						<div class="d-flex align-items-center justify-content-between">
							<span class="dash-icons">
								  <i class="ti ti-user"></i>
							</span>
							<div class="justify-self-start">
								<h6 class="mb-1 text-white">Add Student</h6>						
								<h2 class="fw-semibold mb-0 text-white">
								<svg fill="#fff" width="800px" height="800px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M5.1 7.93A2.87 2.87 0 0 0 7.87 5 2.88 2.88 0 0 0 5.1 2a2.88 2.88 0 0 0-2.78 3A2.88 2.88 0 0 0 5.1 7.93zm0-4.67A1.62 1.62 0 0 1 6.62 5 1.63 1.63 0 0 1 5.1 6.68 1.63 1.63 0 0 1 3.58 5 1.62 1.62 0 0 1 5.1 3.26zm7.19 5.05a2.39 2.39 0 0 0 2.3-2.46 2.39 2.39 0 0 0-2.3-2.47A2.39 2.39 0 0 0 10 5.85a2.39 2.39 0 0 0 2.29 2.46zm0-3.68a1.15 1.15 0 0 1 1.05 1.22 1.15 1.15 0 0 1-1.05 1.21 1.15 1.15 0 0 1-1.06-1.21 1.15 1.15 0 0 1 1.06-1.22zm-.07 4.93a3.85 3.85 0 0 0-3.07 1.51A5.21 5.21 0 0 0 5.1 9.18 5 5 0 0 0 0 14h1.25a3.72 3.72 0 0 1 3.85-3.57A3.71 3.71 0 0 1 8.94 14h1.25a4.5 4.5 0 0 0-.32-1.69 2.54 2.54 0 0 1 2.35-1.5 2.44 2.44 0 0 1 2.53 2.33V14H16v-.86a3.69 3.69 0 0 0-3.78-3.58z"/></svg>
								<?php echo $total_total_student; ?></h2>
							</div>
						</div>
					</a>
                  </div>
                </div>
              </div>
			  <div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #464655;">                   
					<a href="add-class-and-fees.php">
						<div class="d-flex align-items-center justify-content-between">
							<span class="dash-icons">
								  <i class="ti ti-book"></i>
							</span>
							<div class="justify-self-start">
								<h6 class="mb-1 text-white">Add Class & Fees</h6>						
								<h2 class="fw-semibold mb-0 text-white">
								<svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M19 3.25001H6.75C6.10713 3.23114 5.483 3.4679 5.01439 3.9084C4.54577 4.3489 4.2709 4.9572 4.25 5.60001V18C4.27609 18.7542 4.60027 19.4673 5.15142 19.9829C5.70258 20.4984 6.43571 20.7743 7.19 20.75H19C19.1981 20.7474 19.3874 20.6676 19.5275 20.5275C19.6676 20.3874 19.7474 20.1981 19.75 20V4.00001C19.7474 3.8019 19.6676 3.61264 19.5275 3.47254C19.3874 3.33245 19.1981 3.2526 19 3.25001ZM18.25 19.25H7.19C6.83339 19.2748 6.48151 19.1571 6.21156 18.9227C5.94161 18.6884 5.77562 18.3566 5.75 18C5.77562 17.6435 5.94161 17.3116 6.21156 17.0773C6.48151 16.843 6.83339 16.7253 7.19 16.75H18.25V19.25ZM18.25 15.25H7.19C6.68656 15.2506 6.19135 15.3778 5.75 15.62V5.60001C5.7729 5.3559 5.89028 5.13039 6.0771 4.9716C6.26392 4.8128 6.50538 4.73329 6.75 4.75001H18.25V15.25Z" fill="#fff"/>
								<path d="M8.75 8.75H15.25C15.4489 8.75 15.6397 8.67098 15.7803 8.53033C15.921 8.38968 16 8.19891 16 8C16 7.80109 15.921 7.61032 15.7803 7.46967C15.6397 7.32902 15.4489 7.25 15.25 7.25H8.75C8.55109 7.25 8.36032 7.32902 8.21967 7.46967C8.07902 7.61032 8 7.80109 8 8C8 8.19891 8.07902 8.38968 8.21967 8.53033C8.36032 8.67098 8.55109 8.75 8.75 8.75Z" fill="#fff"/>
								<path d="M8.75 12.25H15.25C15.4489 12.25 15.6397 12.171 15.7803 12.0303C15.921 11.8897 16 11.6989 16 11.5C16 11.3011 15.921 11.1103 15.7803 10.9697C15.6397 10.829 15.4489 10.75 15.25 10.75H8.75C8.55109 10.75 8.36032 10.829 8.21967 10.9697C8.07902 11.1103 8 11.3011 8 11.5C8 11.6989 8.07902 11.8897 8.21967 12.0303C8.36032 12.171 8.55109 12.25 8.75 12.25Z" fill="#fff"/>
								</svg>
								<?php echo $total_class; ?></h2>
							</div>
						</div>
					</a>
                  </div>
                </div>
              </div>
			  <div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #6B717E;">                   
					<a href="pay-fees.php">
						<div class="d-flex align-items-center justify-content-between">
							<span class="dash-icons">
								  <i class="ti ti-download"></i>
							</span>
							<div class="justify-self-start">
								<h6 class="mb-1 text-white">Fees Received</h6>						
								<h2 class="fw-semibold mb-0 text-white">
								<svg width="800px" height="800px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="6" stroke="#fff" fill="none"><path d="M19.48,10.42h5.88c5.75,0,12.09,1.9,12.09,10.65,0,9.49-8.05,13.12-16.59,12.16a.5.5,0,0,0-.41.84L38.61,53.58" stroke-linecap="round"/><line x1="44.38" y1="10.42" x2="24.07" y2="10.42" stroke-linecap="round"/><line x1="19.77" y1="21.37" x2="44.52" y2="21.37" stroke-linecap="round"/></svg>
								<?php if ( isset($total_student_fee_paid)) { echo $total_student_fee_paid; } else { echo "0";} ?></h2>
							</div>
						</div>
					</a>
                  </div>
                </div>
              </div>
			  <div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #5B5F97;">                   
					<a href="expenditure.php">
						<div class="d-flex align-items-center justify-content-between">
							<span class="dash-icons">
								  <i class="ti ti-upload"></i>
							</span>
							<div class="justify-self-start">
								<h6 class="mb-1 text-white">Expenditure</h6>						
								<h2 class="fw-semibold mb-0 text-white">
								<svg width="800px" height="800px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="6" stroke="#fff" fill="none"><path d="M19.48,10.42h5.88c5.75,0,12.09,1.9,12.09,10.65,0,9.49-8.05,13.12-16.59,12.16a.5.5,0,0,0-.41.84L38.61,53.58" stroke-linecap="round"/><line x1="44.38" y1="10.42" x2="24.07" y2="10.42" stroke-linecap="round"/><line x1="19.77" y1="21.37" x2="44.52" y2="21.37" stroke-linecap="round"/></svg>
								<?php if ( isset($total_staff_expenditure)) { echo $total_staff_expenditure; } else { echo "0";} ?></h2>								
							</div>
						</div>
					</a>
                  </div>
                </div>
              </div>
              <div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #D11149;">
					<div class="d-flex align-items-center justify-content-between">
						<span class="dash-icons">
							  <i class="ti ti-check"></i>
						</span>
						<div class="justify-self-start">
							<h6 class="mb-1 text-white">Total Fees to Be Received</h6>						
							<h2 class="fw-semibold mb-0 text-white">
							<svg width="800px" height="800px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="6" stroke="#fff" fill="none"><path d="M19.48,10.42h5.88c5.75,0,12.09,1.9,12.09,10.65,0,9.49-8.05,13.12-16.59,12.16a.5.5,0,0,0-.41.84L38.61,53.58" stroke-linecap="round"/><line x1="44.38" y1="10.42" x2="24.07" y2="10.42" stroke-linecap="round"/><line x1="19.77" y1="21.37" x2="44.52" y2="21.37" stroke-linecap="round"/></svg>
							<?php if ( isset($total_fee_paid)) { echo $total_fee_paid; } else { echo "0";} ?></h2>
						</div>
					</div>
                  </div>
                </div>
              </div>
              <div class="col-xxl-3 col-lg-4">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden mt-3 mb-2 rounded-2">
                  <div class="card-body p-4 rounded-2" style="background-color: #0E7C7B;">                   
					<div class="d-flex align-items-center justify-content-between">
						<span class="dash-icons">
							  <i class="ti ti-help"></i>
						</span>
						<div class="justify-self-start">
							<h6 class="mb-1 text-white">Total Fees Pending</h6>						
							<h2 class="fw-semibold mb-0 text-white">
							<svg width="800px" height="800px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="6" stroke="#fff" fill="none"><path d="M19.48,10.42h5.88c5.75,0,12.09,1.9,12.09,10.65,0,9.49-8.05,13.12-16.59,12.16a.5.5,0,0,0-.41.84L38.61,53.58" stroke-linecap="round"/><line x1="44.38" y1="10.42" x2="24.07" y2="10.42" stroke-linecap="round"/><line x1="19.77" y1="21.37" x2="44.52" y2="21.37" stroke-linecap="round"/></svg>
							<?php if ( isset($total_balance)) { echo $total_balance; } else { echo "0";} ?></h2>
						</div>
					</div>
                  </div>
                </div>
              </div>			  
			  
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
</body>

</html>
