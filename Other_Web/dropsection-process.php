<?php


require_once 'model/common.php';
require_once 'round2control.php';

$BidDAO = new BidDAO();
$BidHistoryDAO = new BidHistoryDAO();
$Round2DAO = new Round2DAO();
$StudentDAO = new StudentDAO();
$SectionDAO = new SectionDAO();
$UserID = $_SESSION['usertoken'];
$select = $_REQUEST['course_selection'];
$selection = explode(',',$select);
$CourseID = $selection[0];
$SectionID = $selection[1];

// retrieve bid amount
$AmountBidded = $BidHistoryDAO->GetBidHistoryAmountbyUser($UserID, $CourseID, $SectionID);
$CurrentAmount = $StudentDAO->retrieveECredit($UserID);
$NewAmount = number_format($AmountBidded + $CurrentAmount,2);
$StudentDAO->updateBid($UserID, $NewAmount);
$DropResult = $BidHistoryDAO->RemoveBidHistorybyUser($UserID, $CourseID, $SectionID);
$Minimum_Bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);

if ($RoundID == 2) {
    require_once 'round2functions.php';
    R2DropClear($CourseID,$SectionID);
}

$Size = $SectionDAO -> getSize($CourseID, $SectionID);
$Size = $Size + 1;
$UpdateSizeResult = $SectionDAO -> updateSize($CourseID, $SectionID, $Size);

if ($DropResult == FALSE && $UpdateSizeResult == FALSE) {
    $_SESSION['bid-error'] = 'Sorry, there is a problem dropping your section';
    header("Location:bid-home.php");
    exit;
} else {

    $_SESSION['eDollar'] = $NewAmount;
    $_SESSION['successful_msg'] = 'You have succesfully dropped your bid, your eDollar credit is now ' . $NewAmount . '!' ;
    header("Location:bid-home.php");
    exit;
}


?>
