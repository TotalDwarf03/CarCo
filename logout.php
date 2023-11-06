<?php
    session_start();

    // If Logout is confirmed, unset the session variables and redirect to homepage
    if(isset($_GET['logout'])){
        session_unset();
        header("location: index.php");
        exit();
    }
?>

<script>
    function confirmLogout(){
        // Redirect to logout.php with $_GET
        window.location.replace("logout.php?logout=True");

        // using replace over href as I dont want the user to be able to go back to the page
    }

    function cancelLogout(){
        // Redirect to homepage
        window.location.replace("index.php");
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Logout</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <div class="content">
        <p>Are you sure you want to logout?</p>
        <button onclick="confirmLogout()">Confirm</button>
        <button onclick="cancelLogout()">Cancel</button>
    </div>

    <?php include("Widgets/footer.php") ?>
</body>
</html>