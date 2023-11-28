<?php 
    session_start(); 

    require_once('Scripts/DBConnect.php');
    require_once('Scripts/GeneralScripts.php');

    checkLoginPermissions(5);

    if($_SERVER['REQUEST_METHOD'] == "GET"){
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Manage Order</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>