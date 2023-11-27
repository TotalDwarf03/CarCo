<?php 
    session_start(); 

    require_once("Scripts/DBConnect.php");

    if(!isset($_SESSION['UserID'])){
        // If not logged in, return to index.php
        header("location: index.php");
    }

    // Array Structure (Add Or Edit):
    //
    // $Order = [$Product1, $Product2, ...]
    // $Product1 = [ProductID, QTY, Cost]

    // Search Variables
    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $sqlResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

    // Get List of Orders
    $sql = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Orders</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <?php include("Widgets/SearchBar.php"); ?>

        <br>


    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>