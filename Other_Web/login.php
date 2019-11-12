<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=1920,initial-scale=1">

    <title>BIOS - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <?php
    // start session
    session_start();

    //show any login errors
    if (isset($_SESSION['error'])) {
        foreach ($_SESSION['error'] as $error) {
            echo "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
              <strong>Error!</strong> $error
              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
              </button>
            </div>
            ";
        }
        unset($_SESSION['error']);
    }
     ?>
</head>

<body class="bg-dark">
    <div class="container-fluid">
        <div class="card card-login mx-auto mt-5">
          <div class="card-header">Bidding Online System - Login </div>
          <div class="card-body">
            <form class="" action="login-process.php" method="post">
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="input_userID" name="userid" class="form-control" placeholder="Email address" required="required" autofocus="autofocus">
                  <label for="input_userID">UserID</label>
                </div>
              </div>
              <div class="form-group">
                <div class="form-label-group">
                  <input type="password" name="password" id="input_Password" class="form-control" placeholder="Password" required="required">
                  <label for="input_Password">Password</label>
                </div>
              </div>
              <input class="btn btn-primary btn-block" type="submit" name="" value="Login">
            </form>
        </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
