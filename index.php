<?php 
    session_start(); 

    require_once('Scripts/GeneralScripts.php');
?>

<script>
    function ChangePassword(){
        window.location.replace("updatepassword.php");
    }
</script>

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

    <main class="content">
        <!-- User Profile -->
        <section id="UserProfile" style="<?php echo(isset($_SESSION['UserID']) ? '' : 'display:none;'); ?>">
            <header>
                <h2>User Profile</h2>
            </header>

            <img 
                src="<?php echo $_SESSION['Image'] ?>" 
                alt="User Profile Image" 
                <?php echo(isset($_SESSION['Image']) ? ScaleImage(280, $_SESSION['Image']) : ''); ?>
            >
            <br>
            <h3>Welcome <?php echo $_SESSION['Name']; ?>!</h3>

            <!-- Image Upload Form (hidden if not staff) -->
            <form id="UploadStaffImage" action="updatestaffimage.php" method="post" enctype="multipart/form-data" <?php echo($_SESSION['UserType'] != 'Staff' ? 'hidden' : ''); ?>>
                <fieldset>
                    <legend>Update Image:</legend>

                    <input type="file" id="fileToUpload" name="fileToUpload" required>
                    <input type="hidden" id="OldImage" name="OldImage" value="<?php echo($_SESSION['Image']) ?>">
                    <input type="submit" value="Confirm" name="submit">
                </fieldset>
            </form>

            <button type="button" id="ChangePassword" name="ChangePassword" onclick="ChangePassword();">Change Password</button>
            
            <!-- Error / Success Message -->
            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? ''); ?></p>

        </section>

        <!-- Homepage Conent -->
        <section style="<?php echo(isset($_SESSION['UserID']) ? 'width: 70%;' : ''); ?>">
            <header>
                <h2 style="padding-top: 10px;">Welcome to CarCo's Online Portal!</h2>
            </header>

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
        </section>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>