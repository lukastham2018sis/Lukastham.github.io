<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=1920, initial-scale=1, shrink-to-fit=no">

  <title>BIOS - My Bids</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
  <?php
      require_once 'model/common.php';
      $UserID = $_SESSION['usertoken'];
      $name = $_SESSION['Name'];
      $StudentDAO = new StudentDAO();
      $eDollar = $StudentDAO -> retrieveECredit($UserID);

      //check for round active
      if ($active_round) {
          $round_disabled = "";
      }
      else {
          $round_disabled = 'disabled';
      }

      //admin locking
      $round2_disabled = '';
      if ($_SESSION['usertoken'] == 'admin') {
          $round_disabled = 'disabled';
          $round2_disabled = 'disabled';
          $_SESSION['bid-error'] = [];
          $_SESSION['bid-error'][] = 'Please bid using JSON or from administrator console';
          $eDollar = 'NA';
      }
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
          <?php echo "<span style='color:white'><h5>E-Credit Balance:$eDollar</h5></span>"; ?>
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
          <a class="nav-link" href="course_search.php">
            <i class="fas fa-fw fa-chalkboard"></i>
            <span>Course Search</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="logout-process.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
        </li>
      </ul>

    <div id="content-wrapper">
        <div class="breadcrumb">
            <?php echo "<strong>Welcome $name,      </strong> $round_status"; ?>
        </div>
    <?php
    if (!$active_round) {
        echo "
        <div class='alert alert-warning alert-dismissible fade show ' role='alert' >
          There is no round currently in progress, bidding functions have been disabled.
          <button type='button' class='close' data-dismiss='alert'>
            <span>&times;</span>
          </button>
        </div>
        ";
        unset($_SESSION['bid-error']);
        unset($_SESSION['successful_msg']);
    }
    elseif (isset($_SESSION['bid-error'])) {
        foreach ($_SESSION['bid-error'] as $error) {
            echo "
            <div class='alert alert-danger alert-dismissible fade show ' role='alert' >
              <strong>Error!</strong> $error
              <button type='button' class='close' data-dismiss='alert'>
                <span>&times;</span>
              </button>
            </div>
            ";
        }
        unset($_SESSION['bid-error']);
    }
    else {
      if (isset($_SESSION['successful_msg'])) {
      $msg = $_SESSION['successful_msg'];
      echo "
      <div class='alert alert-success'>
        <strong>Success!</strong> $msg
        <button type='button' class='close' data-dismiss='alert'>
          <span>&times;</span>
        </button>
      </div>
      ";
      unset($_SESSION['successful_msg']);
    }
  }
    ?>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" id="nav-mybids-tab" data-toggle="tab" href="#nav-mybids" role="tab" >MyBids</a>
          <a class="nav-item nav-link" id="nav-placebid-tab" data-toggle="tab" href="#nav-placebid" role="tab" >Place New Bid</a>
          <a class="nav-item nav-link   " id="nav-mybidresult-tab" data-toggle="tab" href="#nav-mybidresult" role="tab" >My Bid Results</a>
          <a class="nav-item nav-link" id="nav-dropclass-tab" data-toggle="tab" href="#nav-dropclass" role="tab" >Drop Successfully Bidded Section</a>
        </div>
        <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade active show" id="nav-mybids" role="tabpanel">
            <div class="card mb-3" style="max-height: 1080px;">
              <div class="card-header"><i class="fas fa-table"></i> My Current Bids</div>
              <div class="card-body">
                <form action="dropcurrentbid-process.php" method="post">
                <input type="hidden" name="Student" value="Student">
                <div class="table-responsive">
                  <table style="max-height:100%;" class="table table-bordered" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Course Code</th>
                          <th>Course Title</th>
                          <th>Section Code</th>
                          <th>Day</th>
                          <th>Start Time</th>
                          <th>Bid Amount</th>
                          <th>Click to drop bid</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $BidDAO = new BidDAO();
                            $StudentBids = $BidDAO -> getBidbyUserID($UserID);
                            $CourseDAO = new CourseDAO();
                            $SectionDAO = new SectionDAO();
                            $Days = ['Monday','Tuesday','Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
                            if (empty($StudentBids)) {
                                echo "<tr>
                                <td colspan='7'>
                                No current bids
                                </td>
                                </tr>";
                            }
                            else {
                                foreach ($StudentBids as $bid) {
                                    $CourseID = $bid -> getCourseID();
                                    $Amount = $bid -> getAmount();
                                    $SectionID = $bid -> getSectionID();
                                    $Course = $CourseDAO -> retrieveCoursebyID($CourseID);
                                    $CourseTitle = $Course -> getTitle();
                                    $Section = $SectionDAO->retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
                                    $SectionDay = $Section->getDay();
                                    $section_day = $Days[$SectionDay-1];
                                    $SectionTime = $Section -> getStartTime();
                                    echo "
                                    <tr>
                                    <td>$CourseID</td>
                                    <td>$CourseTitle</td>
                                    <td>$SectionID</td>
                                    <td>$section_day</td>
                                    <td>$SectionTime</td>
                                    <td>$Amount</td>
                                    <td><button type='submit' name='drop_current_bid' value='$CourseID,$SectionID' $round_disabled/>Drop Bid</button></td>
                                    </tr>
                                    ";
                                }
                            }
                         ?>
                        </form>
                      </tbody>
                  </table>
                </div>
              </div>
              <?php
                  echo "<div class='card-footer small text-muted'>Updated as of $today</div>";
              ?>
            </div>
        </div>

        <div class="tab-pane fade" id="nav-placebid" role="tabpanel">
            <div class="card mb-3" style="max-height: 1080px;">
            <form action="placebid.php" method="post">
            <div class="card-header"><i class="fas fa-cart-plus"></i> Place New Bid</div>
              <div class="card-body">
                <div class="table-responsive">
                  <table style="max-height:100%;" id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Course Code</th>
                        <th>Section</th>
                        <th>Course Name</th>
                        <th>Instructor</th>
                        <th>Vacancies</th>
                        <th>Click to place bid</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php
                        $CourseDAO = new CourseDAO();
                        $SectionDAO = new SectionDAO();
                        $Days = ['Monday','Tuesday','Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
                        $AllSections = $SectionDAO -> retrieveAll();
                        foreach ($AllSections as $Section) {
                            $CourseID = $Section -> getCourseID();
                            $SectionID = $Section -> getSectionID();
                            $Course = $CourseDAO -> retrieveCourseByID($CourseID);
                            $CourseTitle = $Course -> getTitle();
                            $Instructor = $Section -> getInstructor();
                            $Size = $Section -> getSize();
                            echo "
                            <tr>
                                <th>$CourseID</th>
                                <td>$SectionID</td>
                                <td>$CourseTitle</td>
                                <td>$Instructor</td>
                                <td>$Size</td>
                                <td>
                                    <button type='submit' name='course_selection' value='$CourseID,$SectionID' $round_disabled>Place Bid</button>
                                </td>
                            </tr>
                            ";
                        }
                         ?>
                    </tbody>
                    </form>
                  </table>
                </div>
              </div>
            <?php
                            echo "<div class='card-footer small text-muted'>Updated as of $today</div>";
            ?>

            </div>
        </div>
        <div class="tab-pane fade" id="nav-mybidresult" role="tabpanel">

            <div class="card mb-3" style="max-height: 1080px;">
              <div class="card-header">
                <i class="fas fa-table"></i>
                My Bidding Results</div>
              <div class="card-body">
                <div class="table-responsive">
                  <table style="max-height:100%;" class="table table-bordered" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Course Code</th>
                          <th>Course Title</th>
                          <th>Section Code</th>
                          <th>Day</th>
                          <th>Start Time</th>
                          <th>Bid Amount</th>
                          <th>Bid Result</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                            $BidHistoryDAO = new BidHistoryDAO();
                            $StudentBids = $BidHistoryDAO -> retrievebyUserID($UserID);
                            $CourseDAO = new CourseDAO();
                            $SectionDAO = new SectionDAO();
                            $Days = ['Monday','Tuesday','Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
                            $BidDAO = new BidDAO();
                            $StudentBids_Current = $BidDAO -> getBidbyUserID($UserID);
                            if (empty($StudentBids) && empty($StudentBids_Current)) {
                                echo "<tr>
                                <td colspan='7'>
                                No current bids
                                </td>
                                </tr>";
                            }
                            else {
                                foreach ($StudentBids as $bid) {
                                    $Result = $bid -> getBidStatus();
                                    if ($Result == 1) {
                                        $bid_status = "Success";
                                    }
                                    else {
                                        $bid_status = "Fail";
                                    }
                                    $CourseID = $bid -> getCourseID();
                                    $Amount = $bid -> getAmount();
                                    $SectionID = $bid -> getSectionID();
                                    $Course = $CourseDAO -> retrieveCoursebyID($CourseID);
                                    $CourseTitle = $Course -> getTitle();
                                    $Section = $SectionDAO->retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
                                    $SectionDay = $Section->getDay();
                                    $section_day = $Days[$SectionDay-1];
                                    $SectionTime = $Section -> getStartTime();
                                    echo "
                                    <tr>
                                    <td>$CourseID</td>
                                    <td>$CourseTitle</td>
                                    <td>$SectionID</td>
                                    <td>$section_day</td>
                                    <td>$SectionTime</td>
                                    <td>$Amount</td>
                                    <td>$bid_status</td>
                                    </tr>
                                    ";
                                }
                                //show for pending bids
                                if (empty($StudentBids_Current)) {
                                }
                                else {
                                    foreach ($StudentBids_Current as $bid) {
                                        $CourseID = $bid -> getCourseID();
                                        $Amount = $bid -> getAmount();
                                        $SectionID = $bid -> getSectionID();
                                        $Course = $CourseDAO -> retrieveCoursebyID($CourseID);
                                        $CourseTitle = $Course -> getTitle();
                                        $Section = $SectionDAO->retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
                                        $SectionDay = $Section->getDay();
                                        $section_day = $Days[$SectionDay-1];
                                        $SectionTime = $Section -> getStartTime();
                                        if ($RoundID == 2) {
                                          $Round2DAO = new Round2DAO();
                                          $Min_Bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID, $SectionID);
                                          $Size = $Section -> getSize();
                                          $all_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID, $SectionID);
                                          if ($Amount +1 >= $Min_Bid && $Min_Bid != NULL || $Size >= count($all_bids)) {
                                            echo "
                                            <tr>
                                            <td>$CourseID</td>
                                            <td>$CourseTitle</td>
                                            <td>$SectionID</td>
                                            <td>$section_day</td>
                                            <td>$SectionTime</td>
                                            <td>$Amount</td>
                                            <td>Pending</td>
                                            </tr>
                                          ";
                                          }
                                          else {
                                            echo "
                                            <tr>
                                            <td>$CourseID</td>
                                            <td>$CourseTitle</td>
                                            <td>$SectionID</td>
                                            <td>$section_day</td>
                                            <td>$SectionTime</td>
                                            <td>$Amount</td>
                                            <td>Fail</td>
                                            </tr>
                                          ";
                                          }
                                          
                                        }
                                      else {
                                      echo "
                                          <tr>
                                          <td>$CourseID</td>
                                          <td>$CourseTitle</td>
                                          <td>$SectionID</td>
                                          <td>$section_day</td>
                                          <td>$SectionTime</td>
                                          <td>$Amount</td>
                                          <td>Pending</td>
                                          </tr>
                                        "; 
                                      }
                                    }
                                }
                            }
                         ?>

                      </tbody>
                  </table>
                </div>
              </div>
              <?php
                  echo "<div class='card-footer small text-muted'>Updated as of $today</div>";
              ?>
            </div>
        </div>

        <div class="tab-pane fade" id="nav-dropclass" role="tabpanel">

            <div class="card mb-3" style="max-height: 1080px;">
                <?php
                if (($active_round && $RoundID == 1) || !$active_round) {
                    $round2_disabled = 'disabled';
                    echo "
                    <div class='alert alert-warning alert-dismissible fade show ' role='alert' >
                      You are not able to drop any classes until Round 2 has started.
                      <button type='button' class='close' data-dismiss='alert'>
                        <span>&times;</span>
                      </button>
                    </div>
                    ";
                }
                 ?>
            <form action="dropsection-process.php" method="post">
            <input type="hidden" name="Student" value="Student">
              <div class="card-header"><i class="fas fa-edit"></i> Drop Successful Bid</div>
              <div class="card-body">
                <div class="table-responsive">
                  <table style="max-height:100%;" id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Section ID</th>
                        <th>Section Day</th>
                        <th>Section Time</th>
                        <th>Click to drop section</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php
                        $BidHistoryDAO = new BidHistoryDAO();
                        $StudentBids = $BidHistoryDAO -> retrievebyUserIDBidStatus($UserID, '1');
                        $CourseDAO = new CourseDAO();
                        $SectionDAO = new SectionDAO();
                        $Days = ['Monday','Tuesday','Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
                        if (empty($StudentBids)) {
                            echo "<tr>
                            <td colspan='6'>
                            No successful bids
                            </td>
                            </tr>";
                        }
                        else {
                            foreach ($StudentBids as $bid) {
                                $CourseID = $bid -> getCourseID();
                                $Amount = $bid -> getAmount();
                                $SectionID = $bid -> getSectionID();
                                $Course = $CourseDAO -> retrieveCoursebyID($CourseID);
                                $CourseTitle = $Course -> getTitle();
                                $Section = $SectionDAO->retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
                                $SectionDay = $Section->getDay();
                                $section_day = $Days[$SectionDay-1];
                                $SectionTime = $Section -> getStartTime();
                                echo "
                                <tr>
                                <td>$CourseID</td>
                                <td>$CourseTitle</td>
                                <td>$SectionID</td>
                                <td>$section_day</td>
                                <td>$SectionTime</td>
                                <td><button type='submit' name='course_selection' value='$CourseID,$SectionID' $round2_disabled>Drop Section</button></td>
                                </tr>
                                ";
                            }
                        }
                         ?>
                    </tbody>
                    </form>
                  </table>
                </div>
              </div>
            <?php
            echo "<div class='card-footer small text-muted'>Updated as of $today</div>";
            ?>

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

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin.min.js"></script>

  <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>

</body>
