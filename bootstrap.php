<?php
# edit the file included below. the bootstrap logic is there
require_once 'model/bootstrap-process.php';
$message = doBootstrap();
$_SESSION['message'] = $message;
header('Location:admin.php');
?>
