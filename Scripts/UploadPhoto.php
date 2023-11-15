<?php
    // Script to upload a new staff or customer image based on UploadForm.php
    // Product Images are handled in a separate file.

    session_start();
    
    function cleanUp(string $target){
        // This function:
        //      1. Gets the old image path from the DB and deletes the file
        //      2. Updates the DB with the new image path

        include('DBConnect.php');

        $UserID = $_SESSION['UserID'];

        switch($_POST['PageName']){
            case "index.php":
                $sql = "SELECT s.Image
                        FROM tblstaff s
                        WHERE s.StaffID = '$UserID'
                        LIMIT 1";
                break;
            case "editcustomer.php":
                $sql = "SELECT c.Image
                        FROM tblcustomerLogin cl
                        JOIN tblcustomer c
                            ON cl.CustomerID = c.CustomerID
                        WHERE cl.CustomerLoginID = '$UserID'
                        LIMIT 1";
                break;
        }

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);
        $oldPath = '../'.$row['Image'];

        mysqli_free_result($result);

        if($row['Image'] != $target){
            // If New Path Different to the Old Path

            if(unlink($oldPath)){
                echo("File Removed <br>");
    
                switch($_POST['PageName']){
                    case "index.php":
                        $sql = "UPDATE tblstaff s
                                SET s.Image = '$target'
                                WHERE s.StaffID = '$UserID'
                                LIMIT 1";
                        break;
                    case "editcustomer.php":
                        $sql = "UPDATE c
                                SET c.Image = '$target'
                                FROM tblcustomerLogin cl
                                JOIN tblcustomer c
                                    ON cl.CustomerID = c.CustomerID
                                WHERE cl.CustomerLoginID = '$UserID'
                                LIMIT 1";
                        break;
                }
    
                if(mysqli_query($db, $sql)){
                    echo("Database Updated <br>");

                    $_SESSION['Image'] = $target;
                }
                else {
                    echo("Can't update Database <br>");
                }
            }
            else{
                echo("Can't remove file <br>");
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        $UploadOk = true;

        $dir;
        $RecordID;
        $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);

        switch($_POST['PageName']){
            case "index.php":
                $dir = "../Images/Staff/";
                $RecordID = $_SESSION['UserID'];
                break;
            case "editcustomer.php":
                $dir = "../Images/Customers/";
                $RecordID = $_SESSION['UserID'];
                break;
        }

        $target = $dir.$RecordID.'.'.$imgFileType;

        // If Uploaded Image not real image, flag it
        if(getimagesize($_FILES['fileToUpload']['tmp_name']) == false){
            $UploadOk = false;
        }

        if(!$UploadOk){
            $message = 'Error Uploading File';
        }
        else {
            if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)){
                cleanUp(str_replace("../", "", $target));
                $message = 'Upload Complete';
            }
            else {
                $message = 'Error Uploading File';
            }
        }

        header("location: ../index.php?UploadStatus=$message");

    }
    else{
        header("location: ../index.php");
    }
?>