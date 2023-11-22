<?php 
    session_start(); 

    include('Scripts/DBConnect.php');

    // if not logged in or user manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(2, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }

    // Search Variables
    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $sqlResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

    $LoggedInStaffID = $_SESSION['UserID'];

    // Get List of Staff (Excl. Logged in user as don't want users to give self permissions)
    $sql = "SELECT
                s.StaffID,
                s.Username,
                s.Forename,
                s.Surname,
                s.Email
            FROM tblStaff s
            WHERE s.Username LIKE '%$SearchText%'
                AND s.StaffID != $LoggedInStaffID
            ORDER BY s.Username
            $sqlResultLimit";

    $Staff = mysqli_query($db, $sql);
?>

<script>
    function toggleNewForm(){
        NewStaffForm = document.getElementById("NewStaffForm")
        NewStaffButton = document.getElementById("NewStaffButton");

        if(NewStaffForm.hidden){
            NewStaffForm.hidden = false;
            NewStaffButton.disabled = true
        }
        else{
            NewStaffForm.hidden = true;
            NewStaffButton.disabled = false;
        }
    }

    function ConfirmNew(){
        document.getElementById("submit").disabled = false;
        document.getElementById("Confirm").disabled = true;
    }

    function UnConfirmNew(){
        document.getElementById("submit").disabled = true;
        document.getElementById("Confirm").disabled = false;
    }

    function editStaff(StaffID){
        window.location.replace(`editstaff.php?StaffID=${StaffID}`);
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Staff</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <aside class="ManagerTools">
            <h2>Manager Tools</h2>

            <!-- Error / Success Message -->
            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>

            <button type="button" id="NewStaffButton" onclick="toggleNewForm()">Add New Staff Member</button>

            <!-- New Staff Form -->
            <form id="NewStaffForm" hidden action="addstaff.php" method="post" enctype="multipart/form-data">
                <fieldset class="inputs">
                    <legend><h3>Add New Staff Member:</h3></legend>

                    <!-- Note: Image not included in form as it's set by the user in index.php -->

                    <!-- Forename -->
                    <label for="Forename">Forename: </label>
                    <input type="text" id="Forename" name="Forename" required maxlength="20">
                    <!-- Surname -->
                    <label for="Surname">Surname: </label>
                    <input type="text" id="Surname" name="Surname" required maxlength="20">
                    <!-- Email -->
                    <label for="Email">Email: </label>
                    <input type="email" id="Email" name="Email" required maxlength="60">
                    <!-- Username -->
                    <label for="Username">Username: </label>
                    <input type="text" id="Username" name="Username" required maxlength="20">
                    <!-- Password -->
                    <label for="Password">Password: </label>
                    <input type="password" id="Password" name="Password" required>
                    
                </fieldset>
                <fieldset>
                    <!-- confirm -->
                    <input type="button" id="Confirm" name="Confirm" value="Confirm" onclick="ConfirmNew()">
                    <!-- Submit -->
                    <input type="submit" id="submit" name="submit" value="Submit" disabled>
                    <!-- reset -->
                    <input type="reset" id="reset" name="reset" value="Cancel" onclick="UnConfirmNew(); toggleNewForm()" style="float: right;">
                </fieldset>
            </form>
        </aside>

        <section id="Staff" style="width: 70%;">
            <?php include("Widgets/SearchBar.php") ?>

            <br>

            <table>
                <caption><h2>Staff</h2></caption>

                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Forename</th>
                        <th>Surname</th>
                        <th>Email</th>
                        <th>Edit</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        if($Staff->num_rows>0){
                            while($row = mysqli_fetch_assoc($Staff)) {
                                $StaffID = $row['StaffID'];
                                $Forename = $row['Forename'];
                                $Surname = $row['Surname'];
                                $Email = $row['Email'];
                                $Username = $row['Username'];

                                echo("
                                        <tr>
                                            <td>$Username</td>
                                            <td>$Forename</td>
                                            <td>$Surname</td>
                                            <td>$Email</td>
                                            <td class='edit'><button type=button onclick='editStaff($StaffID)'>&#128393;</button></td>
                                        </tr>
                                    ");
                            }
                        }
                        else {
                            echo("No Results Found.");
                        }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="5"><i><?php echo("$Staff->num_rows Results.") ?></i></td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>