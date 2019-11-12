<?php
//getting all the information from the placebid page
require_once 'model/common.php';
#require_once 'round2control.php';
require_once 'round2functions.php';

$UserID = $_SESSION['usertoken'];
$name = $_SESSION['Name'];
$eDollar = $_SESSION['eDollar'];
$CourseID = $_REQUEST['courseid'];
$SectionID = $_REQUEST['sectionid'];
$Bid_Amount = $_REQUEST['bid_amount'];

//getting access to the databases
$studentDAO = new StudentDAO();
$CourseDAO = new CourseDAO();
$SectionDAO = new SectionDAO();
$BidHistoryDAO = new BidHistoryDAO();
$PrerequisiteDAO = new PrerequisiteDAO();
$BidDAO = new BidDAO();
$Round2DAO = new Round2DAO();
$CourseCompletedDAO = new CourseCompletedDAO();

$current_bid_course = $CourseDAO -> retrieveCourseByID($CourseID);
$current_bid_section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
$Completed_Courses = $CourseCompletedDAO -> retrieveCourseCompletedCourseIDByUserID($UserID);


// if user bid less than 10 eCredits
if ($Bid_Amount < 10) {
    $_SESSION['bid-error'][] = 'invalid amount';
    header('Location:bid-home.php');
    exit;
}

//retrieving all of the user's successful bids for timetable checking
$user_bid = $BidHistoryDAO -> retrievebyUserIDBidStatus($UserID,TRUE);
$user_bid_current = $BidDAO -> getBidbyUserID($UserID);
$userCourses = [];

//getting the bid's class timings and exam timings
$current_bid_classday = $current_bid_section -> getDay();
$current_bid_classStartTime = $current_bid_section -> getStartTime();
$current_bid_classEndTime = $current_bid_section -> getEndTime();
$current_bid_examDate = $current_bid_course -> getExamDate();
$current_bid_examStartTime = $current_bid_course -> getExamStart();
$current_bid_examEndTime = $current_bid_course -> getExamEnd();

//check for class clash in successful bid
foreach ($user_bid as $subject) {
    $class_CourseID = $subject -> getCourseID();
    $class_SectionID = $subject -> getSectionID();
    $section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($class_CourseID, $class_SectionID);
    $class_day = $section -> getDay();
    $class_StartTime = $section -> getStartTime();
    $class_EndTime = $section -> getEndTime();
    $userCourses[] = $class_CourseID;
    $subject_course = $CourseDAO -> retrieveCourseByID($class_CourseID);
    $class_examDate = $subject_course -> getExamDate();
    $class_examStartTime = $subject_course -> getExamStart();
    $class_examEndTime = $subject_course -> getExamEnd();
    if ($current_bid_classday == $class_day) {
        if ($current_bid_classStartTime == $class_StartTime OR $current_bid_classEndTime == $class_EndTime OR (strtotime($current_bid_classStartTime) > strtotime($class_StartTime) && strtotime($current_bid_classStartTime) <= strtotime($class_EndTime)) OR (strtotime($current_bid_classEndTime) < strtotime($class_StartTime) && strtotime($current_bid_classEndTime) >= strtotime($class_EndTime))) {
            $_SESSION['bid-error'][] = 'Clash in class timetable';
            header('Location:bid-home.php');
            exit;
        }
    }
    if ($current_bid_examDate == $class_examDate) {
        if ($current_bid_examStartTime == $class_examStartTime OR $current_bid_examEndTime == $class_examEndTime OR (strtotime($current_bid_examStartTime) > strtotime($class_examStartTime) && strtotime($current_bid_examStartTime) <= strtotime($class_examEndTime)) OR (strtotime($current_bid_examEndTime) < strtotime($class_examStartTime) && strtotime($current_bid_examEndTime) >= strtotime($class_examEndTime))) {
            $_SESSION['bid-error'][] = 'Clash in exam timetable';
            header('Location:bid-home.php');
            exit;
        }
    }
}

//check for class clash in placed bids
foreach ($user_bid_current as $subject) {
    $class_CourseID = $subject -> getCourseID();
    $class_SectionID = $subject -> getSectionID();
    $section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($class_CourseID, $class_SectionID);
    $class_day = $section -> getDay();
    $class_StartTime = $section -> getStartTime();
    $class_EndTime = $section -> getEndTime();
    $subject_course = $CourseDAO -> retrieveCourseByID($class_CourseID);
    $class_examDate = $subject_course -> getExamDate();
    $class_examStartTime = $subject_course -> getExamStart();
    $class_examEndTime = $subject_course -> getExamEnd();
    if ($current_bid_classday == $class_day && $class_CourseID != $current_bid_course && $class_SectionID != $current_bid_section) {
        if ($current_bid_classStartTime == $class_StartTime OR $current_bid_classEndTime == $class_EndTime OR (strtotime($current_bid_classStartTime) > strtotime($class_StartTime) && strtotime($current_bid_classStartTime) <= strtotime($class_EndTime)) OR (strtotime($current_bid_classEndTime) < strtotime($class_StartTime) && strtotime($current_bid_classEndTime) >= strtotime($class_EndTime))) {
            $_SESSION['bid-error'][] = 'class timetable clash';
            header('Location:bid-home.php');
            exit;
        }
    }
    if ($current_bid_examDate == $class_examDate) {
        if ($current_bid_examStartTime == $class_examStartTime OR $current_bid_examEndTime == $class_examEndTime OR (strtotime($current_bid_examStartTime) > strtotime($class_examStartTime) && strtotime($current_bid_examStartTime) <= strtotime($class_examEndTime)) OR (strtotime($current_bid_examEndTime) < strtotime($class_examStartTime) && strtotime($current_bid_examEndTime) >= strtotime($class_examEndTime))) {
            $_SESSION['bid-error'][] = 'exam timetable clash';
            header('Location:bid-home.php');
            exit;
        }
    }
}
//check for available eCredits
if ($Bid_Amount > $eDollar) {
    $_SESSION['bid-error'][] = 'not enough e-dollar';
    header('Location:bid-home.php');
    exit;
}

//check for PreReq Course
$allPrerequisiteCourses = $PrerequisiteDAO->retrievePrerequisitebyCourse($CourseID);
$preReq = [];
if ($allPrerequisiteCourses != NULL) {
    foreach ($allPrerequisiteCourses as $courses) {
        $preReq[] = $courses -> getPreReq();
    }
}

foreach ($Completed_Courses as $completed_course) {
    $userCourses[] = $completed_course;
}

//check PreReq Courses with Courses the user has taken
if ($preReq != []) {
    foreach ($preReq as $courseNeedtoClear) {
        if (!in_array($courseNeedtoClear, $userCourses)) {
            $_SESSION['bid-error'][] = 'incomplete prerequisites';
            header('Location:bid-home.php');
            exit;
        }
    }
}

//Check for more than 5 bids
$UserBids = $BidDAO->getBidbyUserID($UserID);

if (sizeof($user_bid_current) >= 5 || sizeof($user_bid_current) + sizeof($user_bid) >= 5) {
    $_SESSION['bid-error'][] = 'section limit reached';
    header('Location:bid-home.php');
    exit;
}

//Check for more than one bid for the same course
// retrieve from bid, check whether he/she has bidded for the course
$allUserCourses = [];
foreach ($UserBids as $bids) {
    $allUserCourses[] = $bids->getCourseID();
}

$CompletedCourses = [];
    foreach ($user_bid as $completed_bids) {
        if ($CourseID == $completed_bids -> getCourseID()) {
            $_SESSION['bid-error'][] = 'You have already bidded for this course';
            header('Location:bid-home.php');
            exit;
        }
    }
//check if you have completed the course

    foreach ($Completed_Courses as $completed_course) {
        if ($CourseID == $completed_course) {
            $_SESSION['bid-error'][] = 'course completed';
            header('Location:bid-home.php');
            exit;
        }
    }

//check if student bids with more than 2 d.p.
if (is_float($Bid_Amount)) {
    $CheckProcessedAmount = explode(".", $Bid_Amount);
    if (strlen($CheckProcessedAmount[1]) > 2) {
    $_SESSION['bid-error'][] = 'enter a valid amount';
    header('Location:bid-home.php');
    exit;
    }
}

if (!is_numeric($Bid_Amount)) {
    $_SESSION['bid-error'][] = 'enter a valid amount';
    header('Location:bid-home.php');
    exit;
}


// check if student is from that particular school in round 1
if ($RoundID == 1) {
    $courseSchool = $CourseDAO -> retrieveSchoolByCourseID($CourseID);
    $userSchool = $studentDAO -> retrieveSchoolByUserID($UserID);

    if ($courseSchool != $userSchool) {
        $_SESSION['bid-error'][] = 'not own school course';
        header('Location:bid-home.php');
        exit;
    }
}

if ($RoundID == 2) {
    $Minimum_Bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
    if ($Bid_Amount < $Minimum_Bid) {
        $_SESSION['bid-error'][] = 'bid amount is too low';
        header('Location:bid-home.php');
        exit;
    }
}

$Is_Same_Course = FALSE;
//check if you are bidding for the course
foreach ($allUserCourses as $coursesBiddedFor) {
    if ($CourseID == $coursesBiddedFor) {
        $Is_Same_Course = TRUE;
    }
}

if ($Is_Same_Course == TRUE) {
    $user_curr_amount = $studentDAO -> retrieveECredit($UserID);
    //get the original bid amount.
    $curr_bid_amount = $BidDAO -> getBidAmount($UserID, $CourseID, $SectionID);
    //check whether new bid amount is bigger or smaller
    if ($Bid_Amount > $curr_bid_amount) {
        $amount_to_deduct = number_format($Bid_Amount - $curr_bid_amount, 2);
        $amount_to_store = number_format($user_curr_amount - $amount_to_deduct, 2);
        $deductResult = $studentDAO->updateBid($UserID, $amount_to_store);
        if ($deductResult) {
            $result = $BidDAO->UpdateBid($UserID, $CourseID, $SectionID, $Bid_Amount);
        }
        else {
            $_SESSION['bid-error'][] = 'Error in eDollar deduction';
            header('Location:bid-home.php');
            exit;
        }
    }
    else {
        $extra_amount = number_format($curr_bid_amount - $Bid_Amount, 2);
        $amount_to_store = number_format($user_curr_amount + $extra_amount, 2);
        $deductResult = $studentDAO->updateBid($UserID, $amount_to_store);
        if ($deductResult) {
            $result = $BidDAO->UpdateBid($UserID, $CourseID, $SectionID, $Bid_Amount);
        }
        else {
            $_SESSION['bid-error'][] = 'Error in eDollar deduction';
            header('Location:bid-home.php');
            exit;
        }
    }

}
else {
    $amount_left = number_format($eDollar - $Bid_Amount,2);
    $deductResult = $studentDAO->updateBid($UserID, $amount_left);
    if ($deductResult) {
        if ($RoundID == 2) {
            $result = Round2PlaceBid($UserID,$CourseID,$Bid_Amount,$SectionID);
        }
        else {
            $result = $BidDAO->PlaceBid($UserID, $CourseID, $Bid_Amount, $SectionID);
        }

    }
}

if ($result) {

    $_SESSION['eDollar'] = $amount_left;
    $_SESSION['successful_msg'] = 'Bid successfully placed, Your eDollar is now ' . $amount_left . ' !';
}
else {
    $_SESSION['bid-error'][] = 'Error in bidding';
}



header('Location:bid-home.php');


exit;


 ?>
