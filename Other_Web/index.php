<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=1920. initial-scale=1">

    <title>BIOS - Home</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">

    <?php
        require_once 'model/common.php';
        $currentuser = $_SESSION['usertoken'];
        $name = $_SESSION['Name'];
        $eDollar = $_SESSION['eDollar'];
        //admin locking
        if ($_SESSION['usertoken'] == 'admin') {
            $round_disabled = 'disabled';
            $eDollar = 'NA';
        }
     ?>
</head>

<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
        <a class="navbar-brand mr-1" href="index.html">Merlion University: Bidding Online System</a>
    </nav>

    <div id='wrapper'>
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
                    <span>Logout</span>
                </a>
                </li>
        </ul>
        <div id="content-wrapper">
            <!-- <ol class="breadcrumb">
                <form action="index.php" method="post">
                    <button class="btn btn-primary " type="submit" name="view" value="school_view">My School Timetable</button>
                    <button class="btn btn-primary " type="submit" name="view" value="exam_view">My Exam Timetable</button>
                </form>
            </ol> -->
            <div class="container-fluid">
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <?php
                echo "<strong>Welcome, $name!</strong> $round_status"; ?>
                  <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                  </button>
                </div>
            </div>
            <?php
            //echo errors due to no admin access or other related errors
            if (isset($_SESSION['errors'])) {
                foreach ($_SESSION['errors'] as $error_msg) {
                    echo"
                    <div class='container-fluid'>
                        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                            <strong>$error_msg</strong>
                            <button type='button' class='close' data-dismiss='alert'>
                              <span>&times;</span>
                            </button>
                        </div>
                    </div>
                    ";
                }
                unset($_SESSION['errors']);
            }
             ?>

<div class="nav nav-tabs" id="nav-tab" role="tablist">
<a class="nav-item nav-link active" id="nav-classtimetable-tab" data-toggle="tab" href="#nav-classtimetable" role="tab" >Class Timetable</a>
<a class="nav-item nav-link" id="nav-examtimetable-tab" data-toggle="tab" href="#nav-examtimetable" role="tab" >Exam Timetable</a>
</div>
</nav>

<div class="tab-content" id="nav-tabContent">

<div class="tab-pane fade active show" id="nav-classtimetable" role="tabpanel">
<div class="card mb-3" style="max-height: 1080px;">
  <div class="card-header">
    <i class="fas fa-table"></i>
    Class Timetable</div>
  <div class="card-body">
    <div class="table-responsive">
      <table style="max-height:100%;" class="table table-bordered" width="100%" cellspacing="0">
          <thead>
          <tr>
          <th>Course ID</th>
          <th>Course Title</th>
          <th>Section</th>
          <th>Day</th>
          <th>Time</th>
          </tr>
      </thead>
          <tbody>
            <?php

              $connMgr = new ConnectionManager();
              $conn = $connMgr->getConnection();

              $bidhistoryDAO = new BidHistoryDAO;
              $userbidhistory = $bidhistoryDAO -> retrievebyUserIDBidStatus($currentuser,'1');
              $courseDAO = new CourseDAO();
              $sectionDAO = new SectionDAO();

              $classtime = array();
              foreach($userbidhistory as $successfulcourse){
                $return = array();
                $course = $successfulcourse -> getCourseID();
                $coursedetails = $courseDAO -> retrieveCourseByID($course);
                $coursetitle = $coursedetails-> getTitle();
                $sectionid = $successfulcourse -> getSectionID();
                $section_details = $sectionDAO -> retrieveSectionDetailsBySectionCourse($course, $sectionid);
                $day = $section_details -> getDay();
                $time = $section_details -> getStartTime();
                
                $return['courseid'] = $course;
                $return['coursename'] = $coursetitle;
                $return['sectionid'] = $sectionid;
                $return['day'] = $day;
                $return['time'] = $time;

                $classtime[] = $return;
              }
              $day = array_column($classtime, 'day');
              array_multisort($day, SORT_ASC, $classtime);
              $Days = ['Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
              foreach($classtime as $class){
                $courseID = $class['courseid'];
                $courseName = $class['coursename'];
                $sectionID = $class['sectionid'];
                $nday = (($class['day']) - 1);
                $Day = $Days[$nday];
                $Time = $class['time'];

                echo"
                <tr>
                <td>$courseID</td>
                <td>$courseName</td>
                <td>$sectionID</td>
                <td>$Day</td>
                <td>$Time</td>
                </tr>
                ";
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

<div class="tab-pane fade" id="nav-examtimetable" role="tabpanel">

<div class="card mb-3" style="max-height: 1080px;">
  <div class="card-header">
    <i class="fas fa-table"></i>
    Exam Timetable</div>
  <div class="card-body">
    <div class="table-responsive">
      <table style="max-height:100%;" class="table table-bordered" width="100%" cellspacing="0">
      <thead>
      <tr>
      <th>Course ID</th>
      <th>Course Title</th>
      <th>Exam Date</th>
      <th>Start Time</th>
      <th>End Time</th>
      </tr>
      </thead>
      <tbody>
        <?php
        $bidhistoryDAO = new BidHistoryDAO;
        $userbidhistory = $bidhistoryDAO -> retrievebyUserIDBidStatus($currentuser,'1');
        $courseDAO = new CourseDAO();
        foreach($userbidhistory as $successfulcourse){
          $course = $successfulcourse -> getCourseID();
          $coursedetails = $courseDAO -> retrieveCourseByID($course);
          $coursetitle = $coursedetails-> getTitle();
          $examdate = $coursedetails -> getExamDate();
          $examstart = $coursedetails-> getExamStart();
          $examend = $coursedetails -> getExamEnd();

          echo "<tr>
          <td>$course</td>
          <td>$coursetitle</td>
          <td>$examdate</td>
          <td>$examstart</td>
          <td>$examend</td>
          </tr>";
                                
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

    <!-- Demo scripts for this page-->
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
</body>
