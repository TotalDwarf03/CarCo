<?php
    // Creates a Staff Member and Permissions to Start Interaction
    include('DBConnect.php');

    $Forename = 'Kieran';
    $Surname = 'Pritchard';
    $Email = 'Kieran@CarCo.com';
    $Image = '';
    $Username = 'Admin';
    $Password = password_hash('admin', PASSWORD_BCRYPT);

    $sql = "INSERT INTO tblStaff (Forename, Surname, Email, Image, Username, Password)
            VALUES ('$Forename', '$Surname', '$Email', '$Image', '$Username', '$Password')";

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