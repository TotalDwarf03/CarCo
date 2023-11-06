<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Home</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <div class="content">
        <h2 style="padding-top: 10px;">Welcome to CarCo's Online Portal!</h2>

        <img src="Images/CarParts.jpg" alt="Image of Car Parts" height="300px">

        <p>
            To get started, please <a href="login.php"><b>login</b></a> to your account. 
            <br>
            If you need to create an account please get in contact with one of our members of staff. 
            <br>
            <br>
            If you're new, why not take a look at our <a href="viewproducts.php"><b>Range of Products</b></a>?
        </p>

        <h2>Need Some Help?</h2>
        <p>
            Please contact us on <a href="tel:01656 123123">01656 123123</a> or email us at <a href="mailto:Help@CarCo.co.uk">Help@CarCo.co.uk</a>.
            <br>
            Our operating hours are 09:00-17:00 Monday-Saturday.
        </p>
        <br>
    </div>

    <?php include("Widgets/footer.php") ?>
</body>
</html>