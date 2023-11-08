<?php
    // Creates a Staff Member and Permissions to Start Interaction
    include('../DBConnect.php');

    $CustomerID = 1;
    $Forename = 'Steve';
    $Surname = 'Daniel';
    $Email = 'Steve@BSS.co.uk';
    $Image = '';
    $Username = 'BSSSteve';
    $Password = password_hash('password', PASSWORD_BCRYPT);

    // $sql = "INSERT INTO tblStaff (Forename, Surname, Email, Image, Username, Password)
    //         VALUES ('$Forename', '$Surname', '$Email', '$Image', '$Username', '$Password')";
    $sql = "INSERT INTO tblCustomerLogin (CustomerID, Forename, Surname, Email, Username, Password)
            VALUES ('$CustomerID', '$Forename', '$Surname', '$Email', '$Username', '$Password')";

    echo $sql;

    $result = mysqli_query($db, $sql);

    if ($result){
        echo "Successfully added $Forename $Surname to Staff!";
    }
    else {
        echo mysqli_errno($db);
    }

    mysqli_close($db);
?>