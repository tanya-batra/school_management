<?php
// Start the session
session_start();
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
  // Redirect to the login page if not logged in
  header("Location: index.php");
  exit;
}

// Include database connection
include('db_connect.php');

// Handle form submission for password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old-password'];
    $new_password = $_POST['password'];

    // Fetch the current user's username from the session
    $username = $_SESSION['email'];

    // Check if the old password is correct
    $sql = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($current_password);
    $stmt->fetch();

    // If the old password matches
    if ($current_password === $old_password) {
        // Update the password in the database
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $username);

        if ($stmt->execute()) {
            $_SESSION['successMessage'] = "Password changed successfully!";
        } else {
            $_SESSION['errorMessage'] = "Error updating the password. Please try again.";
        }
    } else {
        $_SESSION['errorMessage'] = "Old password is incorrect.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password | The Little Legends</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
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
       <?php include('sidebar.php') ?>
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <?php include('header.php') ?>
      <!-- Main Content -->
      <div class="container-fluid">
        <div class="container">
          <h5 class="card-title fw-semibold mb-4">Change Your Password</h5>
          <div class="card">
            <div class="card-body">
              <form method="POST">
                <div class="mb-3 col-xxl-6 col-lg-6">
                  <label for="old-password" class="form-label">Old Password</label>
                  <input type="password" class="form-control" id="old-password" name="old-password" required>
                </div>

                <div class="mb-3 col-xxl-6 col-lg-6">
                  <label for="new-password" class="form-label">New Password</label>
                  <input type="password" class="form-control" id="new-password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Submit&nbsp;
							<span>
							  <i class="ti ti-check"></i>
							</span></button>
              </form>

              <!-- Display success or error message from session -->
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
