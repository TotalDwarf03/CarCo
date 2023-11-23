<?php
    session_start();

    require_once('Scripts/DBConnect.php');

    if(isset($_SESSION['UserID'])){
        // If Logged in

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            // If Post

            $message;

            // 1. Get StaffID from session variables and posted values
            $StaffID = $_SESSION['UserID'];
            $OldImage = $_POST['OldImage'];

            // 1. Upload Image in File Structure (Images/Staff/)
            $UploadOk = true;

            $dir = "Images/Staff/";
            $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);
            $target = $dir.$StaffID.'.'.$imgFileType;

            // If Uploaded Image not real image, flag it
            if(getimagesize($_FILES['fileToUpload']['tmp_name']) == false){
                $UploadOk = false;
            }

            if(!$UploadOk){
                $message = 'Error Uploading Image, Please Edit the Record and Try Again.';
            }
            else {
                if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)){
                    // 2. Remove Old Image and Change Image Value in DB
                    if($OldImage != $target){
                        unlink($OldImage);
                    }

                    $sql = "UPDATE tblStaff s
                            SET s.Image = '$target'
                            WHERE s.StaffID = $StaffID
                            LIMIT 1";

                    $result = mysqli_query($db, $sql);

                    // 3. Update Session Variable
                    $_SESSION['Image'] = $target;

                    $message = "Record Updated";
                }
                else {
                    $message = "Error Uploading Image, Please Try Again.";
                }
            }

            header("location: index.php?UploadStatus=$message");
        }
        else {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }
?>