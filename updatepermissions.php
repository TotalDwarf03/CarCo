<?php
    session_start();

    include('Scripts/DBConnect.php');

    // if not logged in or user manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(2, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }


    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Get Posted Values
        $StaffID = $_POST['StaffID'];
        $UserManager = $_POST['User_Manager'] ?? '';
        $ProductManager = $_POST['Product_Manager'] ?? '';
        $CustomerManager = $_POST['Customer_Manager'] ?? '';
        $OrderManager = $_POST['Order_Manager'] ?? '';

        // Put Permission into an Array
        $Permissions = [$UserManager, $ProductManager, $CustomerManager, $OrderManager];

        // Remove all old permission for user
        $sql = "DELETE FROM tblStaffPermissions
                WHERE StaffID = $StaffID";

        mysqli_query($db, $sql);

        for($i = 0; $i < count($Permissions); $i++) {
            if($Permissions[$i] != ''){
                $sql = "INSERT INTO tblStaffPermissions (StaffID, PermissionID)
                        VALUES ($StaffID, $Permissions[$i])";

                mysqli_query($db, $sql);
            }
        }

        $message = "Permissions Updated.";

        header("location: editstaff.php?UploadStatus=$message&StaffID=$StaffID");
    }
?>