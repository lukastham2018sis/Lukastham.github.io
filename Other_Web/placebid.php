<!DOCTYPE html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=1920, initial-scale=1, shrink-to-fit=no">

  <title>BIOS - Home</title>

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
        <?php
        if (!isset($_POST['course_selection'])) {
          $_SESSION['bid-error'][] = 'Please enter a course!';
          header('Location:bid-home.php');
          exit;
        }
        $day = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday', 'Sunday'];
        $select = $_REQUEST['course_selection'];
        $selection = explode(',',$select);
        $CourseID = $selection[0];
        $SectionID = $selection[1];
        $CourseDAO = new CourseDAO();
        $SectionDAO = new SectionDAO();
        $Course = $CourseDAO -> retrieveCourseByID($CourseID);
        $Section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
        $day_num = $Section -> getDay();
        $section_day = $day[$day_num-1];
         ?>
        <div class="container-fluid">
          <div class="card mx-auto mt-5">
            <div class="card-header">Place a new Bid</div>
            <div class="card-body">
              <form class="" action="bid-process.php" method="post">
                  <div class="container">
                      <div class="row">
                      <div class="col-lg-6">
                          <div class="card">
                          <div class="card-header">Class Details</div>
                          <?php
                          $Course_Name = $Course->getTitle();
                          $Instructor = $Section->getInstructor();
                          $Start_Time = $Section->getStartTime();
                          $End_Time = $Section->getEndTime();
                          $Exam_Date = $Course->getExamDate();
                          $Exam_Time = $Course->getExamStart();
                          echo "
                          You are placing a bid for: <br> <br>
                          Course ID: $CourseID <br>
                          Section: $SectionID <br>
                          Course Name: $Course_Name <br>
                          Instructor: $Instructor <br>
                          Class Day: $section_day <br>
                          Class Time: $Start_Time - $End_Time <br>
                          Exam Date: $Exam_Date <br>
                          Exam Time: $Exam_Time <br><br>
                          ";
                           ?>
                         </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                            <div class="card-header">Current Bids</div>
                            <br>
                            <table class="table table-bordered" cellspacing="0">
                                <tr>
                                    <th>Current Lowest Bid</th>
                                    <th>Availablility</th>
                                </tr>
                                <tr>
                                    <?php
                                    $BidDAO = new BidDAO();
                                    $Round2DAO = new Round2DAO();
                                    $Number_of_Dropped_Bids = $Round2DAO -> GetSize($CourseID, $SectionID);
                                    $Size = $SectionDAO -> getSize($CourseID,$SectionID);  
                                    $all_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID,$SectionID);
                                    $lowest_bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
                                    $bids = [];
                                    if ($Number_of_Dropped_Bids == 0 && $Size == 0 || $Number_of_Dropped_Bids > count($all_bids) || $Size > count($all_bids)) {
                                      if ($lowest_bid == null) {
                                          $lowest_bid = 'NA';
                                      }  
                                    }
                                    elseif ($Size <= count($all_bids) && $lowest_bid != NULL) {
                                      //get lowest bid from min table.
                                      $lowest_bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
                                    } 
                                    if ($RoundID == 2) {
                                      echo "
                                      <td>$lowest_bid</td>
                                      <td>$Size</td>
                                      <input type='hidden' name='courseid' value='$CourseID'/>
                                      <input type='hidden' name='sectionid' value='$SectionID'/>
                                      <input type='hidden' name='size' value='$lowest_bid'/>
                                      ";
                                  }
                                    else {
                                        echo "
                                        <td colspan = '2'>Information not avaliable in Round 1</td>
                                        <td>$Size</td>
                                        <input type='hidden' name='courseid' value='$CourseID'/>
                                        <input type='hidden' name='sectionid' value='$SectionID'/>
                                        ";
                                    }
                                     ?>

                                </tr>

                            </table>
                           </div>
                          </div>
                      </div>
                  </div>
                  </div>
                  <br>
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="number" name="bid_amount" id="bid_amount" min="10" data-bind="value:bid_amount" class="form-control" placeholder="Bid Amount" required="required" step="0.01" <?php echo "max='$eDollar'"; ?>>
                    <label for="bid_amount">Bid Amount</label>
                  </div>
                </div>
                <input class="btn btn-primary btn-block" type="submit" name="bidnow" value="Place Bid">
              </form>
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
