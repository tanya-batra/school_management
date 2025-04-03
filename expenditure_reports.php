<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$monthWise ="active";
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

// Include database connection
include('db_connect.php');

// Default values for date inputs
$from_date = '';
$to_date = '';
$expenditure_records = [];

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the date range from the form
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Query the database to get expenditures within the date range
    $query = "SELECT * FROM staff_expenditure WHERE date BETWEEN '$from_date' AND '$to_date' AND session = '2025-26'";
    $result = $conn->query($query);

    // Fetch all results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expenditure_records[] = $row;
        }
    } else {
        $_SESSION['errorMessage'] = 'No records found for the selected date range.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Expenditure Reports | The Little Legends</title>
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
        <h5 class="card-title fw-semibold mb-4">Expenditure Reports</h5>
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

        <form id="studentForm" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="from_date" class="form-label">From</label>
                      <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="to_date" class="form-label">To</label>
                      <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] :  date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-3 col-xxl-3 col-lg-3">
                      <label for="status" class="form-label">&nbsp;</label>
                      <button type="submit" name="submit" onclick="submitForm()" class="btn btn-primary d-block">
                        <span><i class="ti ti-search"></i></span>&nbsp;&nbsp;Search Record
                      </button>
                    </div>

                    <br><br>
					<div>
						<a href="export_expendture_report.php?action=export_pdf&from_date=<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d'); ?>&to_date=<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d'); ?>" class="btn btn-danger">
							<i class="ti ti-download"></i>&nbsp;&nbsp;Export to PDF
						</a>&nbsp;

						<a href="export_expendture_report.php?action=export_excel&from_date=<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d'); ?>&to_date=<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d'); ?>" class="btn btn-primary">
							<i class="ti ti-download"></i>&nbsp;&nbsp;Export to Excel (CSV)
						</a>

				  
					</div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>

        <!-- Table to display expenditure records -->
        <?php if (!empty($expenditure_records)) : ?>
        <div class="card mb-4"  >
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover" id="studentTable">
                <thead class="thead-dark">
                  <tr>
                    <th>Sr&nbsp;No.</th>
                    <th>Date</th>
                    <th>Comments</th>
                    <th>Expenditure&nbsp;Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                 
                      $sr_no = 1;
                      foreach ($expenditure_records as $record) {
                          echo '<tr>';
                          echo '<td>' . $sr_no . '</td>';
                          echo '<td>' . date('d-m-Y', strtotime($record['date'])) . '</td>';
                          echo '<td>' . htmlspecialchars($record['comment']) . '</td>';
                          echo '<td>' . number_format($record['amount']) . '</td>';
                          echo '</tr>';
                          $sr_no++;
                      }
                  
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
 
</body>

</html>
