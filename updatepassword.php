<?php
    session_start();

    include('Scripts/DBConnect.php');

    if(isset($_SESSION['UserID'])){
        // If Logged in

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            // If Post

            $message;

            // Get Session and Posted Variables
            $UserID = $_SESSION['UserID'];

            $Password = $_POST['Password'];
            $ConfirmPassword = $_POST['ConfirmPassword'];

            if($Password == $ConfirmPassword){
                // Encrypt Password
                $Password = password_hash($Password, PASSWORD_BCRYPT);

                if($_SESSION['UserType'] == 'Staff'){
                    // If Staff
                    $sql = "UPDATE tblStaff s
                            SET s.Password = '$Password'
                            WHERE s.StaffID = $UserID
                            LIMIT 1";

                    mysqli_query($db, $sql);
                    $message = "Password Updated.";
                }
                else {
                    // If Customer
                    $sql = "UPDATE tblCustomerLogin cl
                            SET cl.Password = '$Password'
                            WHERE cl.CustomerLoginID = $UserID
                            LIMIT 1";

                    mysqli_query($db, $sql);
                    $message = "Password Updated.";
                }

                header("location: index.php?UploadStatus=$message");
            }
            else{
                $message = "Error: Passwords do not match.";
                header("location: updatepassword.php?UploadStatus=$message");
            }
        }
    }
    else {
        header("location: index.php");
    }
?>

<script>
    function ConfirmChanges(){
        document.getElementById("submit").disabled = false;
        document.getElementById("Confirm").disabled = true;
    }

    function UnConfirmChanges(){
        document.getElementById("submit").disabled = true;
        document.getElementById("Confirm").disabled = false;
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Update Password</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <form id="PasswordResetForm" action="updatepassword.php" method="post">
            <fieldset class="inputs">
                <legend><h2>Update Password:</h2></legend>

                <!-- Password -->
                <label for="Password">Password: </label>
                <input type="password" id="Password" name="Password" required>
                <!-- Confirm Password -->
                <label for="ConfirmPassword">Confirm Password: </label>
                <input type="password" id="ConfirmPassword" name="ConfirmPassword" required>

                <!-- Error / Success Message -->
                <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>
            </fieldset>
            <fieldset>
                <!-- confirm -->
                <input type="button" id="Confirm" name="Confirm" value="Confirm" onclick="ConfirmChanges()">
                <!-- Submit -->
                <input type="submit" id="submit" name="submit" value="Submit" disabled>
                <!-- reset -->
                <input type="reset" id="reset" name="reset" value="Reset" onclick="UnConfirmChanges()" style="float: right;">
            </fieldset>
        </form>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>