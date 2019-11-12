<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=1920, initial-scale=1, shrink-to-fit=no">

  <title>BIOS - Admin Dashboard</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">

  <?php
    require_once 'model/common.php';
    require_once 'round1clear.php';
    require_once 'round2clear.php';
    //invoke admin protections
    if ($_SESSION['usertoken']!='admin') {
        $_SESSION['errors'][] = 'You do not have access to the administrator module';
        header('Location:index.php');
        exit;
    }

    // checking for the next round
    $RoundDAO = new RoundDAO();
    $upcoming_rounds = $RoundDAO -> NextRound();
    $bootstrap_lock = '';
    if (!empty($upcoming_rounds)) {
        $next_round = $upcoming_rounds[0] -> getRoundID();
        if ($upcoming_rounds[0]->getRoundStart() != null) {
            $next_round = $upcoming_rounds[1] -> getRoundID();
            if ($next_round == 1) {
                $bootstrap_lock = '';
            }
            else {
                $bootstrap_lock = 'disabled';
            }
        }
        if ($next_round == 0) {
            $next_round =  $RoundID.' bid processing';
            $bootstrap_lock = 'disabled';
        }
        if (sizeof($upcoming_rounds) == 2) {
            if ($upcoming_rounds[1] -> getRoundEnd() != null) {
                if ($active_round) {
                    $next_round = $RoundID . ' bid processing';
                }
                else {
                    $next_round = ' is currently not scheduled past this current round, please reset system';
                }

            }
        }
    }
    else {
        $next_round = ' is currently not scheduled past this current round, please reset system';
    }

    if ($active_round) {
        $newround_disable = 'disabled';
        $stopround_disable = '';
        $reset_round = 'disabled';

    }
    else {
        if (is_numeric($next_round)) {
            $stopround_disable = 'disabled';
            $newround_disable = '';
            $reset_round = '';
        }
        else {
            $stopround_disable = 'disabled';
            $newround_disable = 'disabled';
            $reset_round = '';
        }

    }

    //executing form input if any
    $user_token = $_SESSION['usertoken'];
    if (!empty($_POST)) {
        $RoundDAO = new RoundDAO();
        $action = $_POST['action'];
        if ($action == 'end_round') {
            if($RoundID == 1){
              round1clear($RoundID);
            }
            elseif($RoundID == 2) {
              round2clear($RoundID);
            }
            $status = $RoundDAO -> EndRound($RoundID,$today);
            header('Location:admin.php');
            unset($_POST);
            exit;
        }
        elseif ($action == 'start_round') {
            $status = $RoundDAO -> StartRound($next_round,$today);
            header('Location:admin.php');
            unset($_POST);
            exit;
        }
        elseif ($action == 'reset_round') {
            $status = $RoundDAO -> AdminReset();
            header('Location:admin.php');
            unset($_POST);
            exit;
        }
    }

   ?>
</head>

<body>

  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
    <a class="navbar-brand mr-1" href="admin.php">Merlion University: Bidding Online System Administrator Console</a>
  </nav>

  <div id='wrapper'>

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="admin.php">
          <i class="fas fa-fw fa-home"></i>
          <span>Home</span>
        </a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="logout-process.php">
          <i class="fas fa-fw fa-sign-out-alt"></i>
          <span>Logout</span></a>
      </li>
    </ul>

    <div id="content-wrapper">
        <a name="page-top" ></a>
      <div class="container-fluid">
        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            Round Control</div>
          <div class="card-body">
            Current Round:
            <?php
            if ($active_round) {
                echo "$RoundID";
            }
            else {
                echo "No round is currently in progress";
            }
             ?>
            <br>
            Next Round: Round <?php echo "$next_round"; ?>
            <br> <br>
            <form action="admin.php" method="post">
                <input type="hidden" name="usertoken" value="<?php echo "$user_token"; ?>">
                <button type="submit" class="btn btn-primary" name="action" value="end_round" <?php echo "$stopround_disable"; ?>>End Current Round</button> <br> <br>
                <button type="submit" class="btn btn-primary" name="action" value="start_round" <?php echo "$newround_disable"; ?>>Start Next Round</button> <br> <br>
                <button type="submit" class="btn btn-primary" name="action" value="reset_round" <?php echo "$reset_round"; ?> >Reset all rounds to default</button> <br>
            </form>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-header">
              Bootstrap Control</div>
            <div class="card-body">
                <?php
                // locking bootstrap after round 1 is over
                if ($bootstrap_lock ==  'disabled') {
                    echo "Warning! Bootstrap is only avaliable for use before Round 1 has started <br> <br>";
                }
                else {
                    echo "Warning! Bootstrapping will start Round 1 automatically! <br> <br>";
                }
                 ?>
              <form id='bootstrap-form' action="bootstrap.php" method="post" enctype="multipart/form-data">
              	Bootstrap file:
              	<input type="file" name="bootstrap-file" id='bootstrap-file'></br>
              	<input type="submit" class="btn btn-primary" name="submit" value="Load">
            </form>
              </div>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='card mb-3'>
                  <div class='card-header'>Bootstrap Results</div>
                  <div class='card-body'>";
                $status = $_SESSION['message']['status'];
                if ($status == 'success') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Bootstrap Successful!</strong>
                            <button type="button" class="close" data-dismiss="alert">
                            <span >&times;</span>
                            </button>
                            </div>';
                    $all_messages = $_SESSION['message']['num-record-loaded'];
                    foreach ($all_messages as $item) {
                        foreach ($item as $source => $records_loaded) {
                            echo "$records_loaded records were loaded from $source<br/>";
                        }
                    }
                }
                else {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Bootstrap Error!</strong>
                            <button type="button" class="close" data-dismiss="alert">
                            <span >&times;</span>
                            </button>
                            </div>';
                            $all_messages = $_SESSION['message']['num-record-loaded'];
                            foreach ($all_messages as $item) {
                                foreach ($item as $source => $records_loaded) {
                                    echo "$records_loaded records were loaded from $source<br/>";
                                }
                            }
                            echo "<table class='table table-bordered'>
                            <tr><th colspan='3'>Errors found in bootstrap data</th><tr>
                            <tr>
                            <th>Source File</th>
                            <th>Line</th>
                            <th>Message</th>
                            </tr>";
                            foreach ($_SESSION['message']['error'] as $error) {
                                $source = $error['file'] ;
                                $line = $error['line'];
                                $error_message = $error['message'];
                                foreach ($error_message as $failure_point) {
                                    echo "
                                    <tr>
                                    <td>$source</td>
                                    <td>$line</td>
                                    <td>$failure_point</td>
                                    </tr>";

                                }
                            }
                            echo "</table>";
                }
                echo "
                </div>
              </div>";
              unset($_SESSION['message']);
            }
             ?>

      </div>
      <!-- /.container-fluid -->

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
  <script src="vendor/chart.js/Chart.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin.min.js"></script>

</body>

</html>
