<?php
    // Creates a Staff Member and Permissions to Start Interaction
    include('DBConnect.php');

    $Forename = 'Bob';
    $Surname = 'Smiles';
    $Email = 'Bob@CarCo.com';
    $Image = 'Images/Staff/5-UserManager.png';
    $Username = 'UserManager';
    $Password = password_hash('password', PASSWORD_BCRYPT);

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