<?php 
    session_start(); 

    require_once('Scripts/DBConnect.php');
    require_once("Scripts/GeneralScripts.php");

    checkLoginPermissions(4);

    // Search Variables
    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $sqlResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

    // Get List of Customers
    $sqlCustomers = "   SELECT
                            c.CustomerID,
                            c.CustomerName,
                            c.Image,
                            CONCAT(c.AddressLine1, ', ', c.AddressLine2, ', ', c.AddressLine3) AS Address,
                            c.Postcode,
                            c.Telephone
                        FROM tblCustomer c
                        WHERE c.CustomerName LIKE '%$SearchText%'
                        ORDER BY
                            c.CustomerName
                        $sqlResultLimit";

    $Customers = mysqli_query($db, $sqlCustomers);
?>

<script>
    function toggleNewForm(){
        NewCustomerForm = document.getElementById("NewCustomerForm");
        NewCustomerButton = document.getElementById("NewCustomerButton");

        if(NewCustomerForm.hidden){
            NewCustomerForm.hidden = false;
            NewCustomerButton.disabled = true;
        }
        else{
            NewCustomerForm.hidden = true;
            NewCustomerButton.disabled = false;
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

    function editCustomer(CustomerID){
        window.location.replace(`editcustomer.php?CustomerID=${CustomerID}`);
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Customers</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <aside class="ManagerTools">
            <h2>Manager Tools</h2>

            <!-- Error / Success Message -->
            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>

            <button type="button" id="NewCustomerButton" onclick="toggleNewForm()">Add New Customer</button>

            <!-- New Customer Form -->
            <form id="NewCustomerForm" hidden action="addCustomer.php" method="post" enctype="multipart/form-data">
                <fieldset class="inputs">
                    <legend><h3>Add New Customer:</h3></legend>

                    <!-- Customer Name -->
                    <label for="Name">Name: </label>
                    <input type="text" id="Name" name="Name" required maxlength="50">
                    <!-- Address Line 1 -->
                    <label for="AddressL1">Address Line 1: </label>
                    <input type="text" id="AddressL1" name="AddressL1" required maxlength="30">
                    <!-- Address Line 2 -->
                    <label for="AddressL2">Address Line 2: </label>
                    <input type="text" id="AddressL2" name="AddressL2" required maxlength="30">
                    <!-- Address Line 3 -->
                    <label for="AddressL3">Address Line 3: </label>
                    <input type="text" id="AddressL3" name="AddressL3" required maxlength="30">
                    <!-- Postcode -->
                    <label for="Postcode">Postcode: </label>
                    <input type="text" id="Postcode" name="Postcode" required maxlength="8">
                    <!-- Telephone -->
                    <label for="Tel">Telephone: </label>
                    <input type="tel" id="Tel" name="Tel" required maxlength="20">
                    <!-- Image -->
                    <label for="Image">Logo: </label>
                    <input type="file" id="fileToUpload" name="fileToUpload">
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

        <section id="Customers" style="width: 70%;">
            <?php include("Widgets/SearchBar.php") ?>

            <br>

            <table>
                <caption><h2>Customers</h2></caption>

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Logo</th>
                        <th>Address</th>
                        <th>Postcode</th>
                        <th>Telephone</th>
                        <th>Edit</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        if($Customers->num_rows>0){
                            while($row = mysqli_fetch_assoc($Customers)) {
                                $CustomerID = $row['CustomerID'];
                                $Name = $row['CustomerName'];
                                $Image = $row['Image'] ?? '';
                                $Address = $row['Address'];
                                $Postcode = $row['Postcode'];
                                $Telephone = $row['Telephone'];

                                $imgSize = ScaleImage(100, $Image);

                                echo("
                                        <tr>
                                            <td>$Name</td>
                                            <td><img src='$Image' alt='Customer Logo' $imgSize></td>
                                            <td>$Address</td>
                                            <td>$Postcode</td>
                                            <td>$Telephone</td>
                                            <td class='edit'><button type=button onclick='editCustomer($CustomerID)'>&#128393;</button></td>
                                        </tr>
                                    ");
                            }
                        }
                        else {
                            echo("
                                    <tr>
                                        <td colspan='7'>No Results Found.</td>
                                    </tr>
                                ");
                        }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="7"><i><?php echo("$Customers->num_rows Results.") ?></i></td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>