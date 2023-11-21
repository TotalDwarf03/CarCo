<?php 
    session_start(); 

    include('Scripts/DBConnect.php');
    include('Scripts/GeneralScripts.php');

    // if not logged in or customer manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(4, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }


    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post once edit form submitted

        $message;

        // Get Posted Values
        $CustomerID = $_POST['CustomerID'];
        $Name = $_POST['Name'];
        $AddressL1 = $_POST['AddressL1'];
        $AddressL2 = $_POST['AddressL2'];
        $AddressL3 = $_POST['AddressL3'];
        $Postcode = $_POST['Postcode'];
        $Telephone = $_POST['Tel'];
        $Image = $_POST['Image'];
        
        // 1. Update Database (Excl. Image)
        $sqlUpdate = "  UPDATE tblCustomer c
                        SET
                            c.CustomerName = '$Name',
                            c.AddressLine1 = '$AddressL1',
                            c.AddressLine2 = '$AddressL2',
                            c.AddressLine3 = '$AddressL3',
                            c.Postcode = '$Postcode',
                            c.Telephone = '$Telephone'
                        WHERE c.CustomerID = $CustomerID
                        LIMIT 1";
        
        if(mysqli_query($db, $sqlUpdate)){
            // 2. Check if new image uploaded

            if($_FILES['fileToUpload']['name'] != ''){
                // 3. Upload Image in File Structure (Images/Product/)
                $UploadOk = true;

                $dir = "Images/Customers/";
                $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);
                $target = $dir.$CustomerID.'.'.$imgFileType;

                // If Uploaded Image not real image, flag it
                if(getimagesize($_FILES['fileToUpload']['tmp_name']) == false){
                    $UploadOk = false;
                }

                if(!$UploadOk){
                    $message = 'Error Uploading Image, Please Edit the Record and Try Again.';
                }
                else {
                    if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)){
                        // 4. Remove Old Image and Change Image Value in DB
                        unlink($Image);

                        $sql = "UPDATE tblCustomer c
                                SET c.Image = '$target'
                                WHERE c.CustomerID = $CustomerID
                                LIMIT 1";

                        $result = mysqli_query($db, $sql);

                        $message = "Record Updated";
                    }
                    else {
                        $message = "Error Uploading Image, Please Edit the Record and Try Again.";
                    }
                }
            }
            else {
                $message = "Record Updated";
            }
        }
        else {
            $message = "Error Updating Database";
        }
        
        header("location: viewcustomers.php?UploadStatus=$message&SearchText=$ProductName");
    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET"){
        // Get when coming from viewcustomers.php

        $CustomerID = $_GET['CustomerID'];

        if(isset($_GET['DeleteID'])){
            $deleteID = $_GET['DeleteID'];

            $sqlDelete = "  DELETE FROM tblCustomerLogin
                            WHERE CustomerLoginID = $deleteID
                            LIMIT 1";
            
            mysqli_query($db, $sqlDelete);
        }

        // Get Customer Info
        $sqlCustomerInfo = "SELECT
                                c.CustomerName,
                                c.Image,
                                c.AddressLine1,
                                c.AddressLine2,
                                c.AddressLine3,
                                c.Postcode,
                                c.Telephone
                            FROM tblCustomer c
                            WHERE c.CustomerID = $CustomerID
                            LIMIT 1";
        
        $result = mysqli_query($db, $sqlCustomerInfo);
        $CustomerInfo = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $Name = $CustomerInfo['CustomerName'];
        $Image = $CustomerInfo['Image'];
        $AddressL1 = $CustomerInfo['AddressLine1'];
        $AddressL2 = $CustomerInfo['AddressLine2'];
        $AddressL3 = $CustomerInfo['AddressLine3'];
        $Postcode = $CustomerInfo['Postcode'];
        $Telephone = $CustomerInfo['Telephone'];

        // Get Customer Logins
        $sqlLogins = "  SELECT
                            cl.CustomerLoginID,
                            cl.forename,
                            cl.surname,
                            cl.username,
                            cl.password
                        FROM tblCustomerLogin cl
                        WHERE cl.CustomerID = $CustomerID";

        $CustomerLogins = mysqli_query($db, $sqlLogins);

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

    function deleteCustomerLogin(CustomerID, CustomerLoginID) {
        window.location.replace(`editcustomer.php?CustomerID=${CustomerID}&DeleteID=${CustomerLoginID}`);
    }

    function ToggleNewLogin(){
        newLoginTable = document.getElementById("NewCustomerLoginTable");
        newLoginButton = document.getElementById("AddLogin");

        if(newLoginTable.hidden == true){
            newLoginTable.hidden = false;
            newLoginButton.disabled = true;
        }
        else{
            newLoginTable.hidden = true;
            newLoginButton.disabled = false;
        }
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Edit Customer</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <!-- Edit Customer Section -->
        <section id="editCustomer" style="width: 20%; float:left;">
            <form id="EditCustomerForm" action="editcustomer.php" method="post" enctype="multipart/form-data">
                <fieldset class="inputs">
                    <legend><h3>Edit Customer:</h3></legend>
    
                    <!-- Customer ID (Hidden) -->
                    <input type="hidden" id="CustomerID" name="CustomerID" value="<?php echo($CustomerID); ?>">
                    <!-- Customer Name -->
                    <label for="Name">Name: </label>
                    <input type="text" id="Name" name="Name" required maxlength="50" value="<?php echo($Name); ?>">
                    <!-- Address Line 1 -->
                    <label for="AddressL1">Address Line 1: </label>
                    <input type="text" id="AddressL1" name="AddressL1" required maxlength="30" value="<?php echo($AddressL1); ?>">
                    <!-- Address Line 2 -->
                    <label for="AddressL2">Address Line 2: </label>
                    <input type="text" id="AddressL2" name="AddressL2" required maxlength="30" value="<?php echo($AddressL2); ?>">
                    <!-- Address Line 3 -->
                    <label for="AddressL3">Address Line 3: </label>
                    <input type="text" id="AddressL3" name="AddressL3" required maxlength="30" value="<?php echo($AddressL3); ?>">
                    <!-- Postcode -->
                    <label for="Postcode">Postcode: </label>
                    <input type="text" id="Postcode" name="Postcode" required maxlength="8" value="<?php echo($Postcode); ?>">
                    <!-- Telephone -->
                    <label for="Tel">Telephone: </label>
                    <input type="tel" id="Tel" name="Tel" required maxlength="20" value="<?php echo($Telephone); ?>">
                    <!-- Image -->
                    <label for="Image">Logo: </label>
                    <input type="file" id="fileToUpload" name="fileToUpload">
                    <!-- Preview of Current Image and Hidden Input with old path -->
                    <h4>Current Image:</h4>
                    <img src="<?php echo($Image ?? ''); ?>" alt="No Image Found." <?php echo(ScaleImage(150, $Image ?? '')); ?>>
                    <input type="hidden" id="Image" name="Image" value="<?php echo($Image); ?>">
                </fieldset>
                <fieldset>
                    <!-- confirm -->
                    <input type="button" id="Confirm" name="Confirm" value="Confirm" onclick="ConfirmChanges()">
                    <!-- Submit -->
                    <input type="submit" id="submit" name="submit" value="Submit" disabled>
                    <!-- reset -->
                    <input type="reset" id="reset" name="reset" value="Clear" onclick="UnConfirmChanges()" style="float: right;">
                </fieldset>
            </form>
        </section>

        <!-- Customer Login Section -->
        <section id="CustomerLogins" style="width: 80%; float:right;">
            <h3>Customer Logins</h3>

            <table>
                <thead>
                    <tr>
                        <th>Forename</th>
                        <th>Surname</th>
                        <th>Username</th>
                        <th>Delete</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        if($CustomerLogins->num_rows>0){
                            while($row = mysqli_fetch_assoc($CustomerLogins)) {
                                $CustomerLoginID = $row['CustomerLoginID'];
                                $Forename = $row['forename'];
                                $Surname = $row['surname'];
                                $Username = $row['username'];

                                echo("
                                        <tr>
                                            <td>$Forename</td>
                                            <td>$Surname</td>
                                            <td>$Username</td>
                                            <td class='delete'><button type=button onclick='deleteCustomerLogin($CustomerID, $CustomerLoginID)'>&#128465;</button></td>
                                        </tr>
                                    ");
                            }
                        }
                        else {
                            echo("
                                    <tr>
                                        <td colspan='4'>No Results Found.</td>
                                    </tr>
                                ");
                        }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4"><i><?php echo($CustomerLogins->num_rows>0 ? "$CustomerLogins->num_rows Results." : "") ?></i></td>
                    </tr>
                </tfoot>
            </table>
            
            <br>

            <button type="button" id="AddLogin" onclick="ToggleNewLogin()">Add Login</button>

            <table id="NewCustomerLoginTable" hidden>
                <form id="NewCustomerLogin" action="addcustomerlogin.php" method="post">
                    
                    <input type="hidden" id="CustomerID" name="CustomerID" value="<?php echo($CustomerID); ?>">

                    <thead>
                        <tr>
                            <th>Forename</th>
                            <th>Surname</th>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
    
                    <thead>
                        <tr>
                            <td><input type="text" id="Forename" name="Forename" required maxlength="20"></td>
                            <td><input type="text" id="Surname" name="Surname" required maxlength="20"></td>
                            <td><input type="text" id="Username" name="Username" required maxlength="20"></td>
                            <td><input type="password" id="Password" name="Password" required></td>
                        </tr>
                    </thead>
    
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <input type="submit" id="submit" name="submit" value="Add">
                                <input type="reset" id="reset" name="reset" value="Cancel" onclick="ToggleNewLogin()">
                            </td>
                        </tr>
                    </tfoot>
                </form>
            </table>
        </section>
    </main>
    
    <?php include("Widgets/footer.php"); ?>
</body>
</html>