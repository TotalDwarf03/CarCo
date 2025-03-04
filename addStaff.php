<?php
    session_start();

    require_once('Scripts/DBConnect.php');
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(2);

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $message;
        
        // Get Posted Values
        $Forename = $_POST['Forename'];
        $Surname = $_POST['Surname'];
        $Email = $_POST['Email'];
        $Username = $_POST['Username'];
        $Password = password_hash($_POST['Password'], PASSWORD_BCRYPT);

        // Check if username already exists      
        $sqlUserCheck = "   SELECT
                                s.Username
                            FROM tblstaff s
                            WHERE s.Username = '$Username'";

        $result = mysqli_query($db, $sqlUserCheck);

        if($result->num_rows == 0){
            // If username unique, insert record
            $sqlInsert = "  INSERT INTO tblStaff (Forename, Surname, Email, Username, Password)
                            VALUES (
                                '$Forename',
                                '$Surname',
                                '$Email',
                                '$Username',
                                '$Password'
                                )";

            mysqli_query($db, $sqlInsert);
            $message = "Staff Member Added Successfully.";
        }
        else{
            $message = "Error: Login Username already exists in the database";
        }

        header("location: viewstaff.php?UploadStatus=$message&SearchText=$Username");
    }
    else {
        header("location: index.php");
    }
?>