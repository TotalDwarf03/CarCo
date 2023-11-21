<?php
    session_start();

    include('Scripts/DBConnect.php');

    if(isset($_SESSION['UserID'])){
        if(!in_array(4, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Get Posted Values
        $CustomerID = $_POST['CustomerID'];
        $Forename = $_POST['Forename'];
        $Surname = $_POST['Surname'];
        $Username = $_POST['Username'];
        $Password = password_hash($_POST['Password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO tblCustomerLogin (CustomerID, Forename, Surname, Username, Password)
                VALUES (
                    $CustomerID,
                    '$Forename',
                    '$Surname',
                    '$Username',
                    '$Password'
                    )";

        mysqli_query($db, $sql);

        header("location: editcustomer.php?CustomerID=$CustomerID");
    }
    else {
        header("location: index.php");
    }
?>