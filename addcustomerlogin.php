<?php
    session_start();

    require_once('Scripts/DBConnect.php');
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(4);

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $message;
        
        // Get Posted Values
        $CustomerID = $_POST['CustomerID'];
        $Forename = $_POST['Forename'];
        $Surname = $_POST['Surname'];
        $Username = $_POST['Username'];
        $Password = password_hash($_POST['Password'], PASSWORD_BCRYPT);

        // Check if username already exists      
        $sqlUserCheck = "   SELECT
                                cl.Username
                            FROM tblCustomerLogin cl
                            WHERE cl.Username = '$Username'";

        $result = mysqli_query($db, $sqlUserCheck);

        if($result->num_rows == 0){
            // If username unique, insert record
            $sqlInsert = "  INSERT INTO tblCustomerLogin (CustomerID, Forename, Surname, Username, Password)
                            VALUES (
                                $CustomerID,
                                '$Forename',
                                '$Surname',
                                '$Username',
                                '$Password'
                                )";

            mysqli_query($db, $sqlInsert);
            $message = "Login Added Successfully.";
        }
        else{
            $message = "Error: Login Username already exists in the database";
        }

        header("location: editcustomer.php?CustomerID=$CustomerID&UploadStatus=$message");
    }
    else {
        header("location: index.php");
    }
?>