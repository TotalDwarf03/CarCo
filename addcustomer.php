<?php
    include("Scripts/DBConnect.php");

    // if not logged in or product manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(4, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post when coming from viewcustomers.php

        $message;

        $Name = $_POST['Name'];
        $AddL1 = $_POST['AddressL1'];
        $AddL2 = $_POST['AddressL2'];
        $AddL3 = $_POST['AddressL3'];
        $Postcode = $_POST['Postcode'];
        $Telephone = $_POST['Tel'];



        // 1. Insert Initial Record into the DB
        $sql = "INSERT INTO tblCustomer 
                    (
                        CustomerName, 
                        AddressLine1, 
                        AddressLine2, 
                        AddressLine3,
                        Postcode,
                        Telephone
                    )
                VALUES
                    (
                        '$Name',
                        '$AddL1',
                        '$AddL2',
                        '$AddL3',
                        '$Postcode',
                        '$Telephone'
                    )";
        
        if(mysqli_query($db, $sql)){
            // 2. Once Inserted, Get the CustomerID so Image can be recorded and Uploaded
            $sql = "SELECT c.CustomerID
                    FROM tblCustomer c
                    WHERE c.CustomerName = '$Name'
                        AND c.Postcode = '$Postcode'
                        AND c.Telephone = '$Telephone'
                    LIMIT 1";
            
            $result = mysqli_query($db, $sql);

            if($result != false){
                $CustomerID = mysqli_fetch_assoc($result)['CustomerID'];
                mysqli_free_result($result);

                // 3. Upload Image in File Structure (Images/Product/)
                $UploadOk = true;

                $dir = "Images/Customers/";
                $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);
                $target = $dir.$CustomerID.'.'.$imgFileType;

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
                        $sql = "UPDATE tblCustomer c
                                SET c.Image = '$target'
                                WHERE c.CustomerID = $CustomerID
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

        header("location: viewcustomers.php?UploadStatus=$message&SearchText=$Name");
    }
    else {
        header("location: viewcustomers.php");
    }
?>