<?php
//Allow autoloading of classes in each webpage
spl_autoload_register(function ($class_name) {
    if (file_exists("model/" . $class_name . ".php")) {
        require_once "model/" . $class_name . ".php";
    }
    elseif (file_exists($class_name . ".php")) {
        require_once $class_name . ".php";
    }
});

// start the session
session_start();

function Round2PlaceBid($UserID,$CourseID,$Amount,$SectionID){
    $today = date("d/m/y H:i");
    //opening required database connections
    $StudentDAO = new StudentDAO;
    $SectionDAO = new SectionDAO;
    $BidDAO = new BidDAO;
    $BidHistoryDAO = new BidHistoryDAO;
    $CourseDAO = new CourseDAO;
    $Round2DAO = new Round2DAO;
    $current_section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
    $current_aval = $current_section -> getSize();

    $all_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID,$SectionID);
    if (!isset($all_bids)) {
        $BidDAO -> PlaceR2Bid($CourseID, $SectionID,$UserID,$Amount,'1');
        return TRUE;
    }
        //finding all other bids in the Bid table that matches the section+course combo
        if (sizeof($all_bids)+1 < $current_aval) {
            $BidDAO -> PlaceR2Bid($CourseID, $SectionID,$UserID,$Amount,'1');
            return TRUE;
        }
        else {
            // getting all current information to the array
            $all_competing_bids = [];
            foreach ($all_bids as $pending_bid) {
                $pending_userid = $pending_bid -> getUserID();
                $pending_amount = $pending_bid -> getAmount();
                $all_competing_bids[] = [$pending_amount,$pending_userid];
            }
            //add in current bid as well
            $all_competing_bids [] = [$Amount,$UserID];

            //sort the data by highest bid to lowest bid
            rsort($all_competing_bids);
            array_reverse($all_competing_bids);

            // checking where the 2 bids are the same and section size is the same as well
            if (sizeof($all_bids)+1 == $current_aval) {
                $BidDAO -> PlaceR2Bid($CourseID, $SectionID,$UserID,$Amount,'1');
                $clearing_amount = $all_competing_bids[$current_aval-1][0];
                $current_min_bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
                $new_min_bid = $clearing_amount + 1;
                if ($current_min_bid < $new_min_bid) {
                    $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $new_min_bid);
                }
                return TRUE;
            }

            // check for clearing bid price
            $clearing_bid = $all_competing_bids[$current_aval-1];
            $clearing_person = $clearing_bid[1];
            $clearing_amount = $clearing_bid[0];

            // check to see if the next bid is the same as clearing price
            if (!isset($all_competing_bids[$current_aval])) {
                $clearing_bid = $all_competing_bids[$current_aval-2];
            }
            else {
                $clearing_bid = $all_competing_bids[$current_aval];
            }
            $clearing_checkname = $clearing_bid[1];
            $clearing_checkamount = $clearing_bid[0];

            //set R2 clearing bid amount at nth bid +1
            if (isset($all_competing_bids[$current_aval-1])) {
                $set_clearing_amount = $all_competing_bids[$current_aval-1][0];
                $set_clearing_amount ++;
            }
            // if next bid is equal to clearing bid, set new clearing price
            $pricefloor_amount = 0;
            $pricefloor_amount = $clearing_amount;
            if ($clearing_checkamount == $clearing_amount) {
                $stop = $current_aval -1;
                $checksum = true;
                while ($checksum) {
                    $curr_check_amount = $all_competing_bids[$stop][0];
                    if ($curr_check_amount == $clearing_amount) {
                        if ($stop == 0) {
                            $clearing_amount ++;
                            break;
                        }
                        else {
                            $stop --;
                        }
                    }
                    else {
                        // set to exit loop
                        $pricefloor_amount = $curr_check_amount;
                        $checksum = false;
                    }
                }
            }
        }
    //start to process and commit the changes in round2DAO
    foreach ($all_competing_bids as $curr_bid) {
        $curr_name = $curr_bid[1];
        $curr_amount = $curr_bid[0];
        if ($curr_amount >= $pricefloor_amount) {
            $BidStatus = $BidDAO -> SearchBidsbyUserCourseSection($UserID, $CourseID,$SectionID);
            if (empty($BidStatus)) {
                $BidDAO -> PlaceR2Bid($CourseID, $SectionID,$UserID,$Amount,'1');
            }
            else {
                $BidDAO -> UpdateR2Status($curr_name,$curr_amount,$SectionID,$CourseID,'1');
            }
        }
        else {
            $BidDAO -> UpdateR2Status($curr_name,$curr_amount,$SectionID,$CourseID,'0');
        }
    }
    $current_min_bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);

    if ($current_min_bid < $set_clearing_amount) {
        $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $set_clearing_amount);
    }
    return TRUE;
}

function R2DropClear($CourseID,$SectionID){
    $today = date("d/m/y H:i");
    //opening required database connections
    $StudentDAO = new StudentDAO;
    $SectionDAO = new SectionDAO;
    $BidDAO = new BidDAO;
    $BidHistoryDAO = new BidHistoryDAO;
    $CourseDAO = new CourseDAO;
    $Round2DAO = new Round2DAO;
    $current_section = $SectionDAO -> retrieveSectionDetailsBySectionCourse($CourseID, $SectionID);
    $current_aval = $current_section -> getSize();


    $all_bids = $BidDAO -> SearchBidsbyCourseSection($CourseID,$SectionID);
        //finding all other bids in the Bid table that matches the section+course combo
        if (isset($all_bids)) {
            if (sizeof($all_bids) <= $current_aval) {
                foreach ($all_bids as $bid) {
                    $BidDAO -> UpdateR2Status($bid -> getUserID(),$bid -> getAmount(),$bid -> getSectionID(),$bid -> getCourseID(),'1');
                }
                return TRUE;
            }
            else {
                // getting all current information to the array
                $all_competing_bids = [];
                foreach ($all_bids as $pending_bid) {
                    $pending_userid = $pending_bid -> getUserID();
                    $pending_amount = $pending_bid -> getAmount();
                    $all_competing_bids[] = [$pending_amount,$pending_userid];
            }
            //sort the data by highest bid to lowest bid
            rsort($all_competing_bids);
            array_reverse($all_competing_bids);

            // check for clearing bid price
            $clearing_bid = $all_competing_bids[$current_aval-1];
            $clearing_person = $clearing_bid[1];
            $clearing_amount = $clearing_bid[0];
            // check to see if the next bid is the same as clearing price
            if (!isset($all_competing_bids[$current_aval])) {
                $clearing_bid = $all_competing_bids[$current_aval-2];
            }
            else {
                $clearing_bid = $all_competing_bids[$current_aval];
            }
            $clearing_checkname = $clearing_bid[1];
            $clearing_checkamount = $clearing_bid[0];

            // if next bid is equal to clearing bid, set new clearing price
            if ($clearing_checkamount == $clearing_amount) {
                $stop = $current_aval -1;
                $checksum = true;
                while ($checksum) {
                    $curr_check_amount = $all_competing_bids[$stop][0];
                    if ($curr_check_amount == $clearing_amount) {
                        if ($stop == 0) {
                            $clearing_amount ++;
                            break;
                        }
                        else {
                            $stop --;
                        }
                    }
                    else {
                        // set to exit loop
                        $clearing_amount = $curr_check_amount;
                        $checksum = false;
                    }
                }
            }
        }
        //start to process and commit the changes in round2DAO
        if (isset($all_competing_bids)) {
            foreach ($all_competing_bids as $curr_bid) {
                $curr_name = $curr_bid[1];
                $curr_amount = $curr_bid[0];
                if ($curr_amount >= $clearing_amount) {
                    $BidDAO -> UpdateR2Status($curr_name,$curr_amount,$SectionID,$CourseID,'1');
                }
                else {
                    $BidDAO -> UpdateR2Status($curr_name,$curr_amount,$SectionID,$CourseID,'0');
                }
            }
        }
        /* not required to update min-bid in R2 dropping bid/section as the current min-bid will always be higher
        if (isset($clearing_amount)) {
            $current_min_bid = $Round2DAO -> GetMinBidSizefromCourseSection($CourseID,$SectionID);
            $new_min_bid = $clearing_amount + 1;
            if ($current_min_bid < $new_min_bid) {
                $Round2DAO -> UpdateMin_Bid($CourseID, $SectionID, $new_min_bid);
            }
        }
        */
    }


}
 ?>
