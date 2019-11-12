<?php
require_once 'model/common.php';
$BidHistoryDAO = new BidHistoryDAO();
$output = $BidHistoryDAO -> getBidByCourseSection('IS110','S1');
$result = $output;
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
 ?>
