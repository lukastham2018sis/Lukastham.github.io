<!DOCTYPE html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=1920, initial-scale=1, shrink-to-fit=no">

  <title>BIOS - Overall Bidding Results</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">

  <?php
  require_once 'model/common.php';
  $eDollar = $_SESSION['eDollar'];
   ?>

</head>

<body>

    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
      <a class="navbar-brand mr-1" href="index.html">Merlion University: Bidding Online System</a>
    </nav>
    <div id='wrapper'>

      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
        <li class="nav-item px-2 pt-4">
<?php   echo "<span style='color:white'><h5>E-Credit Balance:$eDollar </h5></span>"; ?>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Home</span>
          </a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="bid-home.php">
            <i class="fas fa-fw fa-funnel-dollar"></i>
            <span>My Bids</span>
          </a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="results.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Overall Bidding Results</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="course_search.php">
            <i class="fas fa-fw fa-chalkboard"></i>
            <span>Course Search</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="login.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
        </li>
      </ul>

    <div id="content-wrapper">
        <div class="container-fluid">
            <div class="card mx-auto mt-5">
            <div class="card-header">View Overall Bidding Results</div>
            <div class="card-body">
                <div class="table-responsive">
                  <table style="max-height:100%;" id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Round</th>
                          <th>Course Code</th>
                          <th>Section Code</th>
                          <th>Remaining Vacancies</th>
                          <th>Highest Bid</th>
                          <th>Minimum Successsful Bid</th>
                        </tr>
                      </thead>
                      <tbody>
                <?php
                $BidHistoryDAO = new BidHistoryDAO;
                $overall_results = $BidHistoryDAO -> retrieveOverallResults();
                if (empty($overall_results)) {
                    echo "<tr>
                    <td colspan='6'>
                    No results information avaliable
                    </td>
                    </tr>";
                }
                else {
                    foreach ($overall_results as $result) {
                        $RoundID = $result['RoundID'];
                        $CourseID = $result['CourseID'];
                        $SectionID = $result['SectionID'];
                        $Size = $result['Size'];
                        $Max = $result['Max'];
                        $Min = $result['Min'];
                        echo "
                        <tr>
                        <td>$RoundID</td>
                        <td>$CourseID</td>
                        <td>$SectionID</td>
                        <td>$Size</td>
                        <td>$Max</td>
                        <td>$Min</td>
                        </tr>
                        ";
                    }
                }
                 ?>
                </div>
            </div>
            </div>
         </div>
        </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>


</body>
