<?php
    include("DBCredentials.php");

    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if(mysqli_connect_errno()){
        $msg = "DB Connection Failed: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }
    // else{
    //     echo("DB Connection Successful.");
    // }
?>