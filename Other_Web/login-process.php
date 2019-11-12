<?php
    //start session
    session_start();

    //autoload of required classes
    spl_autoload_register(function ($class_name) {
        if (file_exists("model/" . $class_name . ".php")) {
            require_once "model/" . $class_name . ".php";
        }
        elseif (file_exists($class_name . ".php")) {
            require_once $class_name . ".php";
        }
    });
    //admin login details
    require_once 'model/admin-master.php';


    //check for no user token
    if (!isset($_REQUEST)) {
        $_SESSION['error'] = [];
        array_push($_SESSION['error'],'Please login!');
        header("Location: login.php");
        exit;
    }
    else {
        //retrieve data from given form
        $userid = $_REQUEST['userid'];
        $password = $_REQUEST['password'];
        //retrieve the student object from the database
        $studentDAO = new StudentDAO();
        $student = $studentDAO -> get($userid);
        //if the student object is not found, redirect to login
        if ($student == false) {
            if ($userid == 'admin') {
                if ($password == $admin_password) {
                    $_SESSION['usertoken'] = 'admin';
                    $_SESSION['eDollar'] = 'NA';
                    $_SESSION['Name'] = 'Administrator';
                    header("Location: admin.php");
                    exit;
                }
                else {
                    $_SESSION['error'] = [];
                    array_push($_SESSION['error'],'Administrator Password is incorrect!');
                    header("Location: login.php");
                    exit;
                }
            }
            $_SESSION['error'] = [];
            array_push($_SESSION['error'],'Username not found!');
            header("Location: login.php");
            exit;
        }
        else {
            //check the input password
            $validate = $student -> authenticate($password);
            // password matches, give user token
            if ($validate) {
                $_SESSION['usertoken'] = $student-> getUserID();
                $_SESSION['Name'] = $student -> getName();
                $_SESSION['eDollar'] = $student -> getEcredits();
                header("Location: index.php");
                exit;
            }
            //else, redirect user to main page
            else {
                $_SESSION['error'] = [];
                array_push($_SESSION['error'],'Incorrect Password!');
                header("Location: login.php");
                exit;
            }
        }
    }
 ?>
