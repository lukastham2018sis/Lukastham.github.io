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

<body>
<div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-courses-tab" data-toggle="tab" role="tab" >Courses Available for Bidding</a>
</div>

<div class="tab-pane fade active show" role="tabpanel">
    <div class="card mb-3" style="max-height: 1080px;">
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
                      </tr>
                    </thead>
                    <tbody>
                        <?php
                        $CourseDAO = new CourseDAO();
                        $SectionDAO = new SectionDAO();
                        $PrerequisiteDAO = new PrerequisiteDAO();
                        $CourseCompletedDAO = new CourseCompletedDAO();
                        
                        $AvailSections = [];
                        $errors = 0;
                        $Days = ['Monday','Tuesday','Wednesday','Thursday','Friday', 'Saturday', 'Sunday'];
                        $AllSections = $SectionDAO -> retrieveAll();
                        foreach($AllSections as $eachsection){
                          $CourseID = $eachsection -> getCourseID();
                          $SectionID = $eachsection -> getSectionID();
                          $Course = $CourseDAO -> retrieveCourseByID($CourseID);
                          $CourseTitle = $Course -> getTitle();
                          $Instructor = $eachsection -> getInstructor();
                          $Size = $eachsection -> getSize();
                          $userschl = $StudentDAO -> retrieveSchoolByUserID($UserID);
                          $coursesch = $CourseDAO -> retrieveSchoolByCourseID($CourseID);
                          
                          //Display courses from user's own school if round = 1
                          if($RoundID == 1){
                            if($userschl != $coursesch){
                              $errors += 1;
                            }
                          }

                          //Display Courses for which the user has 
                          //completed the prerequisites
                          $prerequisitecourse = $PrerequisiteDAO -> retrieveCoursePrerequisiteByCourse($CourseID);
                          $completedcourses = $CourseCompletedDAO -> retrieveCourseCompletedCourseIDByUserID($UserID);
                          if(!empty($prerequisitecourse)){
                            if(!in_array($prerequisitecourse, $completedcourses) ){
                              $errors += 1;
                            }
                          }

                          //Display courses that the user has not completed before
                          if(in_array($CourseID, $completedcourses)){
                            $errors += 1;
                          }

                          if($errors == 0){
                            $AvailSections[] = [$CourseID, $SectionID, $CourseTitle, $Instructor, $Size];
                          }
                          $errors = 0;

                        }

                        foreach ($AvailSections as $sectioninfo) {
                            $Courseid = $sectioninfo[0];
                            $Sectionid = $sectioninfo[1];
                            $Coursetitle = $sectioninfo[2];
                            $instructor = $sectioninfo[3];
                            $size = $sectioninfo[4];
                            echo "
                            <tr>
                                <th>$Courseid</th>
                                <td>$Sectionid</td>
                                <td>$Coursetitle</td>
                                <td>$instructor</td>
                                <td>$size</td>
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