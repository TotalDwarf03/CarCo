<?php 
    // THis might be obsolete
    function ConnectToDB(){

        include("DBCredentials.php");

        $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

        if(mysqli_connect_errno()){
            $msg = "DB Connection Failed: ";
            $msg .= mysqli_connect_error();
            $msg .= " (" . mysqli_connect_errno() . ")";
            exit($msg);
        }
        else {
            return $db;
        }
    }

    // function CloseDB($db){
    //     if(isset($db)){
    //         mysqli_close($db);
    //         // echo("DB Closed");
    //     }
    // }

    // function DropResults($results){
    //     //Drop dataset after table is created
    //     if($results){
    //         mysqli_free_result($results);
    //         // echo("Results Dropped");
    //     }
    // }
?>