<?php
    session_start();

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
            // case "editproduct.php":
            //     $dir = "Images/Products/";
            //     break;
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
            $message = move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target) ? 'File Uploaded' : 'Error Uploading File';
        }

    }
    else{
        header("location: index.php");
    }
?>