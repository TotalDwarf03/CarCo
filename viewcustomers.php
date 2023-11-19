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

    // Search Variables
    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $sqlResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

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

            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>

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
                        <th>Delete</th>
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
                                            <td class='delete'><button type=button onclick='deleteCustomer($CustomerID)'>&#128465;</button></td>
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
                        <td colspan="7"><i><?php echo("$Customers->num_rows Results.") ?></i></td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>