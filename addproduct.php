<?php
    session_start();
    
    require_once("Scripts/DBConnect.php");
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(3);

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post when coming from viewproducts.php

        $message;

        $ProductName = $_POST['ProductName'];
        $Description = $_POST['Description'];
        $Cost = $_POST['Cost'];
        $Status = $_POST['Status'];


        // 1. Insert Initial Record into the DB
        $sql = "INSERT INTO tblSystemProduct 
                    (
                        SystemProductStatusID, 
                        ProductName, 
                        Description, 
                        CostPerUnit
                    )
                VALUES
                    (
                        $Status,
                        '$ProductName',
                        '$Description',
                        $Cost
                    )";
        
        if(mysqli_query($db, $sql)){
            // 2. Once Inserted, Get the SystemProductID so Image can be recorded and Uploaded
            $sql = "SELECT sp.SystemProductID
                    FROM tblSystemProduct sp
                    WHERE sp.ProductName = '$ProductName'
                        AND sp.Description = '$Description'
                        AND sp.CostPerUnit = $Cost
                    LIMIT 1";
            
            $result = mysqli_query($db, $sql);

            if($result != false){
                $ProductID = mysqli_fetch_assoc($result)['SystemProductID'];
                mysqli_free_result($result);

                // 3. Upload Image in File Structure (Images/Product/)
                $UploadOk = true;

                $dir = "Images/Products/";
                $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);
                $target = $dir.$ProductID.'.'.$imgFileType;

                // If Uploaded Image not real image, flag it
                if(getimagesize($_FILES['fileToUpload']['tmp_name']) == false){
                    $UploadOk = false;
                }

                if(!$UploadOk){
                    $message = 'Error Uploading Image, Please Edit the Record and Try Again.';
                }
                else {
                    if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)){
                        // 4. Change Image Value in DB
                        $sql = "UPDATE tblSystemProduct sp
                                SET sp.Image = '$target'
                                WHERE sp.SystemProductID = $ProductID
                                LIMIT 1";

                        $result = mysqli_query($db, $sql);

                        $message = "Upload Complete";
                    }
                    else {
                        $message = "Error Uploading Image, Please Edit the Record and Try Again.";
                    }
                }
            }
            else{
                $message = "Error Getting Product ID";
            }
            
        }
        else {
            $message = "Error Inserting into DB";
        }

        header("location: viewproducts.php?UploadStatus=$message&SearchText=$ProductName");
    }
    else {
        header("location: viewproducts.php");
    }
?>