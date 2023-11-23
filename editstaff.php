<?php
    session_start(); 

    require_once('Scripts/DBConnect.php');
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(2);


    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post once edit form submitted

        $message;

        // Get Posted Values
        $StaffID = $_POST['StaffID'];
        $Forename = $_POST['Forename'];
        $Surname = $_POST['Surname'];
        $Email = $_POST['Email'];
        $Username = $_POST['Username'];
        
        $sql = "UPDATE tblStaff s
                SET
                    s.Forename = '$Forename',
                    s.Surname = '$Surname',
                    s.Email = '$Email',
                    s.Username = '$Username'
                WHERE s.StaffID = $StaffID
                LIMIT 1";

        if(mysqli_query($db, $sql)){
            $message = "Record Updated";
        }
        else {
            $message = "Error Updating Database";
        }
        
        header("location: viewstaff.php?UploadStatus=$message&SearchText=$Username");
    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET"){
        // Get when coming from viewstaff.php
        $StaffID = $_GET['StaffID'];

        // Get Customer Info
        $sql = "SELECT
                    s.Forename,
                    s.Surname,
                    s.Email,
                    s.Username
                FROM tblStaff s
                WHERE s.StaffID = $StaffID
                LIMIT 1";
        
        $result = mysqli_query($db, $sql);
        $StaffInfo = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $Forename = $StaffInfo['Forename'];
        $Surname = $StaffInfo['Surname'];
        $Email = $StaffInfo['Email'];
        $Username = $StaffInfo['Username'];

        if(isset($_GET['ResetPassword'])){
            $Password = password_hash('password', PASSWORD_BCRYPT);

            $sql = "UPDATE tblStaff
                    SET Password = '$Password'
                    WHERE StaffID = $StaffID
                    LIMIT 1";

            mysqli_query($db, $sql);

            // Send email to user
            $subject = "CarCo Password Reset";

            $msg = "Dear $Forename $Surname,\nYour CarCo password has been reset to 'password'.\nPlease Change your password when you login next.\nThanks,\nCarCo Online Portal.";
            
            mail($Email, $subject, $msg);

            // Reload Page with Success/Error Message
            header("location: editstaff.php?StaffID=$StaffID&UploadStatus=Password Reset Successful. User has been notified via Email.");
        }

        // Get All Permissions
        $sql = "SELECT
                    p.PermissionID,
                    p.PermissionName,
                    p.Description
                FROM tblPermissions p
                ORDER BY p.PermissionName";

        $Permissions = mysqli_query($db, $sql);

        // Get User's Current Permissions and puts them in an array
        // (Used when setting default checked checkboxes)
        $sql = "SELECT
                    sp.PermissionID
                FROM tblStaffPermissions sp
                WHERE sp.StaffID = $StaffID";

        $result = mysqli_query($db, $sql);
        
        $UserPermissions = array();

        if($result->num_rows>0){
            while($row = mysqli_fetch_assoc($result)){
                array_push($UserPermissions, $row['PermissionID']);
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

    function ResetPassword(StaffID){
        window.location.replace(`editstaff.php?StaffID=${StaffID}&ResetPassword=1`);
    }

    function EnablePermissionSubmit(){
        document.getElementById("submitPerms").disabled = false;
    }
    
    function DisablePermissionSubmit(){
        document.getElementById("submitPerms").disabled = true;
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
        <section id="editStaff" style="width: 20%; float:left;">
            <form id="EditStaffForm" action="editstaff.php" method="post">
                <fieldset class="inputs">
                    <legend><h3>Edit Staff:</h3></legend>
    
                    <!-- Staff ID (Hidden) -->
                    <input type="hidden" id="StaffID" name="StaffID" value="<?php echo($StaffID); ?>">
                    <!-- Forename -->
                    <label for="Forename">Forename: </label>
                    <input type="text" id="Forename" name="Forename" required maxlength="20" value="<?php echo($Forename); ?>">
                    <!-- Surname -->
                    <label for="Surname">Surname: </label>
                    <input type="text" id="Surname" name="Surname" required maxlength="20" value="<?php echo($Surname); ?>">
                    <!-- Email -->
                    <label for="Email">Email: </label>
                    <input type="email" id="Email" name="Email" required maxlength="60" value="<?php echo($Email); ?>">
                    <!-- Username -->
                    <label for="Username">Username: </label>
                    <input type="text" id="Username" name="Username" required maxlength="20" value="<?php echo($Username); ?>">
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

            <button type="button" onclick="ResetPassword(<?php echo($StaffID); ?>);">Reset Password</button>
        </section>

        <!-- Staff Permission Section -->
        <section id="StaffPerms" style="width: 80%; float:right;">
            <h3>Staff Permissions</h3>

            <!-- Error / Success Message -->
            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>

            <table id="Permissions">
                <form id="PermissionsForm" action="updatepermissions.php" method="post">
                    <!-- Staff ID (Hidden)  -->
                    <input type="hidden" id="StaffID" name="StaffID" value="<?php echo($StaffID); ?>">

                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Description</th>
                            <th>Enabled</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                            if($Permissions->num_rows>0){
                                while($row = mysqli_fetch_assoc($Permissions)) {
                                    $PermissionID = $row['PermissionID'];
                                    $PermissionName = $row['PermissionName'];
                                    $Description = $row['Description'];

                                    $selected = in_array($PermissionID, $UserPermissions) ? 'checked' : '';

                                    echo("
                                            <tr>
                                                <td>$PermissionName</td>
                                                <td>$Description</td>
                                                <td><input type='checkbox' id='$PermissionName' name='$PermissionName' value='$PermissionID' onclick='EnablePermissionSubmit();' $selected></td>
                                            </tr>
                                        ");
                                }
                            }
                            else {
                                echo("
                                        <tr>
                                            <td colspan='3'>No Results Found.</td>
                                        </tr>
                                    ");
                            }
                        ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <input type="submit" id="submitPerms" name="submit" value="Update" disabled>
                                <input type="reset" id="reset" name="reset" value="Reset" onclick="DisablePermissionSubmit();">
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