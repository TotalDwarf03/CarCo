<?php
    session_start();

    require_once('Scripts/DBConnect.php');
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(2);


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