<?php 
    session_start(); 

    // if not logged in or product manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(3, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }


    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post once edit form submitted

    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET"){
        // Get when coming from viewproducts.php

    }
    else {
        header("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Edit Product</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <form id="EditProductForm" method="post" action="editproduct.php">
            <fieldset>
                
            </fieldset>
        </form>
    </main>
    
    <?php include("Widgets/footer.php"); ?>
</body>
</html>