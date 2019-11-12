<?php

require_once 'model/common.php';


$bidDAO = new BidDAO();
$studentDAO = new StudentDAO();
$UserID = $_SESSION['usertoken'];
$select = $_REQUEST['drop_current_bid'];
$selection = explode(',',$select);
$CourseID = $selection[0];
$SectionID = $selection[1];

// retrieve bid amount
$AmountBidded = $bidDAO->getBidAmount($UserID, $CourseID, $SectionID);

$dropResult = $bidDAO->DeleteBid($UserID, $CourseID, $SectionID);

if ($RoundID == 2) {
    require_once 'round2functions.php';
    R2DropClear($CourseID,$SectionID);
}

if ($dropResult == FALSE) {
    $_SESSION['bid-error'] = 'Sorry, there is a problem deleting your bid';
    header("Location:bid-home.php");
    exit;
} else {
    $currentAmount = $studentDAO->retrieveECredit($UserID);
    $newAmount = number_format($AmountBidded + $currentAmount,2);
    $studentDAO->updateBid($UserID, $newAmount);
    $_SESSION['eDollar'] = $newAmount;
    $_SESSION['successful_msg'] = 'You have succesfully dropped your bid, your eDollar credit is now ' . $newAmount . '!' ;
    header("Location:bid-home.php");
    exit;
}


?>
