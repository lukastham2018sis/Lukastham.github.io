<?php

require_once 'model/common.php';

function round2control($CourseID, $SectionID, $Bid_Amount) {
    $Round2DAO = new Round2DAO();
    $SectionDAO = new SectionDAO();
    $BidDAO = new BidDAO();


    $Minimum_Bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
    $all_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID, $SectionID);
    $Size = $SectionDAO -> getSize($CourseID,$SectionID);
    $Does_Course_Exist = $Round2DAO -> retrieve($CourseID, $SectionID);
    $Number_Of_Dropped_Bids = $Round2DAO -> getSize($CourseID,$SectionID);
    $number_of_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID, $SectionID);

    if ($Size > len($number_of_bids)) {
        
    }

































    if ($Size == count($all_bids) && $Minimum_Bid == NULL) {
        $Bids_Processing = [];
        foreach ($all_bids as $curr_bids) {
            $Bids_Processing[] = $curr_bids -> getAmount();
        }
        rsort($Bids_Processing);
        $tmp = 0;
        if (count($all_bids) > 1) {
            if ($tmp == 0) {
                $tmp = $Bids_Processing[count($Bids_Processing) - 1];
                if ($tmp != $Bids_Processing[count($Bids_Processing) - 2]) {
                    $Minimum_Bid = $tmp;
                } 
                else {
                    for ($i = count($Bids_Processing) - 3; $i >= 0; $i--) {
                        if ($tmp != $Bids_Processing[$i]) {
                            $Minimum_Bid = $Bids_Processing[$i];
                        }
                    }
                }  
            } 
        }
        if ($Does_Course_Exist == FALSE) {
            $Round2DAO -> AddRound2Information($CourseID, $SectionID, $Minimum_Bid + 1, $Size);
        }
        elseif(sizeof($number_of_bids) > 0) {
            $Minimum_Bid = max($Bids_Processing);
            $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $Minimum_Bid + 1);
        }
    }

    elseif ($Size < count($all_bids) && $Minimum_Bid == NULL && $Does_Course_Exist == TRUE) {
        $Bids_Processing = [];
        foreach ($all_bids as $curr_bids) {
            $Bids_Processing[] = $curr_bids -> getAmount();
        }
        if(sizeof($number_of_bids) > 0) {
            $Minimum_Bid = max($Bids_Processing);
            $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $Minimum_Bid+1);
        }
        else {
            $Minimum_Bid = min($Bids_Processing);
            $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $Minimum_Bid + 1);
        }
        
    }
    elseif ($Minimum_Bid != NULL && $Size < count($all_bids) && $Does_Course_Exist == TRUE) {
        if ($Bid_Amount > $Minimum_Bid) {
            $Minimum_Bid = $Bid_Amount + 1;
        }
        else {
            $Minimum_Bid = $Minimum_Bid + 1;
        }
        $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $Minimum_Bid);
    }


}



?>