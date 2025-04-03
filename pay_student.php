<?php
ob_start();
session_start();

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

include('db_connect.php');

// Check if the student ID is provided in the URL
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Fetch the student details along with the class name
    $stmt = $conn->prepare("SELECT s.*, c.class_name as class_name FROM students s
                            JOIN class c ON s.class_id = c.id
                            WHERE s.id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student data is found
    if ($result->num_rows > 0) {
        // Fetch student details
        $student = $result->fetch_assoc();
    } else {
        echo "Student not found!";
        exit;
    }
} else {
    echo "No student ID provided!";
    exit;
}

// Fetch the active session
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

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get form values
    $fee_change = $_POST['fee']; 
    $payment_mode = $_POST['payment_mode'];
    $comment = $_POST['comment'];
    $student_id = $_POST['student_id']; // Assuming student ID is passed from the form
    $session_id = $_POST['session_id']; // Session ID passed from the form
    $created_by = $_SESSION['email']; // Assuming email is stored in session for the logged-in user
    
    // Get the current total_fees of the student from the student table
    $query = "SELECT total_fees, balance FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($total_fees, $current_balance);
    $stmt->fetch();
    $stmt->close();

    // Calculate the new balance by subtracting the fee change from total_fees
    $new_balance = $total_fees - $fee_change;

    // Prepare the SQL statement to insert the fee transaction record
    $sql = "INSERT INTO fee_transactions (student_id, amount, payment_mode, comment, session_id, created_by) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idssss", $student_id, $fee_change, $payment_mode, $comment, $session_id, $created_by);
    
    // Execute the query
    if ($stmt->execute()) {
        // Update the student's balance after the transaction
        $update_balance_sql = "UPDATE students SET balance = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_balance_sql);
        $update_stmt->bind_param("di", $new_balance, $student_id);
        $update_stmt->execute();
        $update_stmt->close();

        $_SESSION['successMessage'] = "Fee transaction successfully added!";
        
        // Redirect to prevent form re-submission on refresh
        header('Location: pay_student.php?id=' . $student_id);
        exit;
    } else {
        $_SESSION['errorMessage'] = "Error occurred while adding the fee transaction.";
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Student | The Little Legends</title>
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

    table td, table th {
      padding: 10px;
      text-align: left;
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
    
    <div class="container-fluid x-padd" style="max-width: 100%;">
      <div class="row">
        <h5 class="card-title fw-semibold mb-4">Edit Student for the Session <?php echo $session; ?></h5>

        <div class="card mb-4">
          <div class="card-body">
              <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="studentTable">
                      <thead class="thead-dark">
                          <tr>
                              <th>#</th>
                              <td>Admission No.</td>
                              <td>Class Name</td>
                              <td>Student Name</td>
                              <td>Father's Name</td>
                              <td>Mother's Name</td>
                              <td>Aadhar No.</td>
                              <td>Total Fees</td>
                          </tr>
                      </thead>
                    <tbody>
                      <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo $student['admission_no']; ?></td>                     
                        <td><?php echo $student['class_name']; ?></td>    
                        <td><?php echo $student['name']; ?></td> 
                        <td><?php echo $student['father_name']; ?></td>                    
                        <td><?php echo $student['mother_name']; ?></td>                     
                        <td><?php echo $student['adhar_no']; ?></td>                     
                        <td><?php echo $student['total_fees']; ?></td>
                      </tr>
                    </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  </div>

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

  <form id="feeForm" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <!-- Fee Change Amount -->
                <div class="mb-3 col-xxl-6 col-lg-7 position-relative">
                    <label for="fee" class="form-label">Fee Change</label>
                    <input type="number" class="form-control" id="fee" name="fee" placeholder="Enter Fee Change Amount" required>
                </div>

                <!-- Payment Mode -->
                <div class="mb-3 col-xxl-3 col-lg-4">
                    <label for="payment_mode" class="form-label">Payment Mode</label>
                    <select class="form-select" name="payment_mode" id="payment_mode" required>
                        <option value="cash" selected>Cash</option>
                        <option value="online">Online</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>

                <!-- Comments Input -->
                <div class="mb-3 col-xxl-3 col-lg-4">
                    <label for="comment" class="form-label">Comments</label>
                    <input type="text" class="form-control" id="comment" name="comment" placeholder="Enter Comments" required>
                </div>

                <!-- Hidden Fields (Student ID, Session ID) -->
                <input type="hidden" name="student_id" value="<?php echo $studentId; ?>">
                <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">

                <!-- Submit Button -->
                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">Update Fees&nbsp;&nbsp;<span><i class="ti ti-check"></i></span></button>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="assets/libs/jquery/dist/jquery.min.js"></script>
<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sidebarmenu.js"></script>
<script src="assets/js/app.min.js"></script>
<script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="assets/libs/simplebar/dist/simplebar.js"></script>
<script src="assets/js/dashboard.js"></script>

</body>
</html>
