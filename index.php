<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>The Little Legends School | Login</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <style>
	
	a.logo-img > img{ width:250px;}
	.card {	--bs-card-spacer-x: 20px; /*border: 2px solid rgba(255,255,255,.45);
		background: linear-gradient(rgba(255,255,255,1), rgba(255,255,255,.2));
		backdrop-filter: blur(2px);*/
	}
	.page-wrapper {
		background: radial-gradient(rgba(17,33,54,0), rgba(17,33,54,1)),url('assets/images/cover-desat.jpg');
		background-size: cover;
		background-position: top;
		background-blend-mode: screen;
	}
	
	
    @media(min-width:1370px){
		.col-xxl-3 { max-width: 22%;}
	}
	
	@media(max-width:1370px){
		.col-md-3 { max-width: 28%;}
		a.logo-img > img { width: 220px;}
	}
	
	@media(max-width:1160px){
		.col-md-3 { max-width: 45%; width: 45%;}
	}
	
	@media(max-width:767px){
		.col-md-3 { max-width: 90%; width: 90%;}
	}
	
  </style>
</head>

<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-3 col-lg-4 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="index.php" class="text-nowrap logo-img text-center d-block py-3 w-100 logo-modified">
                  <img src="assets/images/logo.png" style=" margin: 0 0 .3rem;" alt="">
                </a>

                <!-- Error message container -->
                <div id="error-message"></div>

                <!-- Login form -->
                <form id="login-form">
                  <div class="mb-2 position-relative">
                    <span class="position-absolute" style="font-size: 1.3em; left: 10px; top: 15px; padding-right: 8px; color: #129AD6; line-height: 20px;">
                      <i class="ti ti-user"></i>
                    </span>
                    <input type="email" class="form-control rounded-2" id="username" name="username" placeholder="Enter username" style="padding-left: 37px; background-color: #fff;" autofocus required>
                  </div>
                  <div class="mb-2 position-relative">
                    <span class="position-absolute" style="font-size: 1.3em; left: 10px; top: 15px; padding-right: 8px; color: #129AD6; line-height: 20px;">
                      <i class="ti ti-lock"></i>
                    </span>
                    <input type="password" class="form-control rounded-2" id="password" name="password" placeholder="Enter password" style="padding-left: 37px; background-color: #fff;" required>
                  </div>
                  <button type="submit" class="btn btn-primary py-1 fs-4 mb-4 rounded-2 d-flex justify-content-center align-items-center">
                    Sign In <span style="font-size: 1.4em; left: 5px; top: 0; position: relative;">
                      <i class="ti ti-arrow-right"></i>
                    </span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>

$(document).ready(function () {
    $("#login-form").submit(function (e) {
        e.preventDefault(); // Prevent form submission

        var username = $("#username").val();
        var password = $("#password").val();

        // Log data to check if values are being taken correctly
        console.log("Username: " + username);
        console.log("Password: " + password);

        // Send the AJAX request
        $.ajax({
    url: 'login_process.php', // PHP script to process login
    type: 'POST',
    data: {
        username: username,
        password: password
    },
    success: function(response) {
        console.log("Response from server:", response); // Log server response for debugging

        if (response.success && response.status == 1) {
            // Redirect based on role
            if (response.role === 'accountant') {
                window.location.href = 'dashboard.php';
            } else if (response.role === 'teacher') {
                window.location.href = 'teacher_dashboard.php';
            } else if (response.role === 'student') {
                window.location.href = 'student_dashboard.php';
            } else if (response.role === 'principal') {
                window.location.href = 'principal_dashboard.php';
            }
        } else {
            $("#error-message").html('<div class="alert alert-danger">' + response.message + '</div>');
        }
    },
    error: function(xhr, status, error) {
        console.error("AJAX Error: ", error);
        $("#error-message").html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
    }
});

    });
});

  </script>

</body>

</html>
