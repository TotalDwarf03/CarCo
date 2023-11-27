<?php 
    session_start(); 

    require_once("Scripts/DBConnect.php");
    require_once('Scripts/GeneralScripts.php');

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

    // If Logged in as customer, only show orders for that customer
    $sqlCustomerLimit = "";

    if($_SESSION['UserType'] == 'Customer'){
        $UserID = $_SESSION['UserID'];

        $sql = "SELECT
                    cl.CustomerID
                FROM tblCustomerLogin cl
                WHERE cl.CustomerLoginID = $UserID
                LIMIT 1";

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);
        $CustomerID = $row['CustomerID'];
        $sqlCustomerLimit = "AND o.CustomerID = $CustomerID";
    }

    // Get List of Orders
    $sql = "SELECT
                o.OrderID,
                o.CreationDate,
                c.CustomerName,
                CONCAT(s.Forename, ' ', s.Surname) as StaffName,
                o.TotalCost,
                o.DeliveryDate
            FROM tblOrder o
            JOIN tblCustomer c
                ON o.CustomerID = c.CustomerID
            JOIN tblStaff s
                ON o.StaffID = s.StaffID
            WHERE o.OrderID LIKE '%$SearchText%'
            $sqlCustomerLimit
            $sqlResultLimit";

    $Orders = mysqli_query($db, $sql);

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

        <table>
            <caption><h2>Orders</h2></caption>
            <thead>
                <tr>
                    <th>OrderID</th>
                    <th>CreationDate</th>
                    <th>Customer Name</th>
                    <th>Staff Name</th>
                    <th>Total Cost</th>
                    <th>Delivery Date</th>
                    <th <?php echo(hideContent(5)); ?>>Show More</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                    if($Orders->num_rows>0){
                        while($row = mysqli_fetch_assoc($Orders)) {
                            $permissionCheck = hideContent(3);

                            echo("  
                                    <tr>
                                        <td>$Product</td>
                                        <td>$Desc</td>
                                        <td><img src='$Img' alt='Product Image' $imgSize></td>
                                        <td>£$Cost</td>
                                        <td $stockHighlight>$Status</td>
                                        <td class='edit' $permissionCheck><button type=button onclick='editProduct($ProductID)'>&#128393;</button></td>
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
                    <td colspan="7"><i><?php echo("$Orders->num_rows Results.") ?></i></td>
                </tr>
            </tfoot>
        </table>
    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>