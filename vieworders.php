<?php 
    session_start(); 

    require_once("Scripts/DBConnect.php");
    require_once('Scripts/GeneralScripts.php');

    // If logged in as staff, must have permissions
    // If logged in as customer fine
    if(isset($_SESSION['UserID'])){
        if($_SESSION['UserType'] == "Staff"){
            // If Staff, Check Perms
            checkLoginPermissions(5);
        }
    }
    else{
        // If not logged in, redirect to index.php
        header("location: index.php");
    }

    // Search Variables
    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $sqlResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if($_SESSION['UserType'] == "Staff"){
            // Delete Order if DeleteID Given
            if(isset($_GET['DeleteID'])){
                $DeleteID = $_GET['DeleteID'];
            
                $sqlDelete = "  DELETE FROM tblOrderProducts
                                WHERE OrderID = $DeleteID";

                mysqli_query($db, $sqlDelete);

                $sqlDelete = "  DELETE FROM tblOrder
                                WHERE OrderID = $DeleteID
                                Limit 1";
                
                mysqli_query($db, $sqlDelete);
            }
        }
    }

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
    $sqlOrders = "  SELECT
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
                    ORDER BY o.CreationDate DESC
                    $sqlResultLimit";

    $Orders = mysqli_query($db, $sqlOrders);

    // Get List of Customers
    $sqlCustomers = "   SELECT
                            c.CustomerID,
                            c.CustomerName
                        FROM tblCustomer c";
    
    $Customers = mysqli_query($db, $sqlCustomers);
?>

<script>
    function showSubTable(OrderID){
        SubTable = document.getElementById(`SubTable-${OrderID}`);

        if(SubTable.hidden){
            SubTable.hidden = false;
        }
        else{
            SubTable.hidden = true;
        }
    }

    function deleteOrder(OrderID){
        window.location.replace(`vieworders.php?DeleteID=${OrderID}`);
    }

    function addOrder(){
        window.location.replace("addorder.php");
    }
</script>

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
        <!-- Orders Section -->
        <section id="Orders">
            <?php include("Widgets/SearchBar.php"); ?>

            <br>

            <table>
                <caption><h2>Orders</h2></caption>
                <thead>
                    <tr>
                        <th>OrderID</th>
                        <th>CreationDate</th>
                        <th>Customer</th>
                        <th>Created By</th>
                        <th>Delivery Date</th>
                        <th>Total Cost</th>
                        <th>Show More</th>
                        <th <?php echo(hideContent(5)); ?>>Edit</th>
                        <th <?php echo(hideContent(5)); ?>>Delete</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                        if($Orders->num_rows>0){
                            while($row = mysqli_fetch_assoc($Orders)) {
                                $OrderID = $row['OrderID'];
                                $CreationDate = $row['CreationDate'];
                                $CustomerName = $row['CustomerName'];
                                $StaffName = $row['StaffName'];
                                $DeliveryDate = $row['DeliveryDate'];
                                $TotalCost = $row['TotalCost'];

                                $PermissionCheck = hideContent(5);

                                echo("  
                                        <tr>
                                            <td>$OrderID</td>
                                            <td>$CreationDate</td>
                                            <td>$CustomerName</td>
                                            <td>$StaffName</td>
                                            <td>$DeliveryDate</td>
                                            <td>£$TotalCost</td>
                                            <td class='info'><button type='button' onclick='showSubTable($OrderID)'>&#9432;</button></td>
                                            <td class='edit' $PermissionCheck><button type=button onclick='editOrder($OrderID)'>&#128393;</button></td>
                                            <td class='delete' $PermissionCheck><button type=button onclick='deleteOrder($OrderID)'>&#128465;</button></td>
                                        </tr>
                                    ");

                                // Get Order Products
                                $sqlOrderProducts = "   SELECT
                                                            sp.ProductName,
                                                            op.Quantity,
                                                            sp.CostPerUnit,
                                                            op.Subtotal
                                                        FROM tblOrderProducts op
                                                        JOIN tblSystemProduct sp
                                                            ON op.SystemProductID = sp.SystemProductID
                                                        WHERE op.OrderID = $OrderID";

                                $OrderProducts = mysqli_query($db, $sqlOrderProducts);

                                echo("
                                        <tr id='SubTable-$OrderID' hidden>
                                            <td colspan='9'>
                                                <table class='SubTable'>
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Quantity</th>
                                                            <th>Cost Per Unit</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                    ");

                                while($row2 = mysqli_fetch_assoc($OrderProducts)){
                                    $ProductName = $row2['ProductName'];
                                    $Quantity = $row2['Quantity'];
                                    $CostPerUnit = $row2['CostPerUnit'];
                                    $Subtotal = $row2['Subtotal'];

                                    echo("
                                            <tr>
                                                <td>$ProductName</td>
                                                <td>$Quantity</td>
                                                <td>£$CostPerUnit</td>
                                                <td>£$Subtotal</td>
                                            </tr>
                                        ");                                                    
                                }

                                echo("
                                                    </tbody>

                                                    <tfoot>
                                                        <td colspan=3></td>
                                                        <td><b>£$TotalCost</b></td>
                                                    <tfood>
                                                </table>
                                            </td>
                                        </tr>
                                    ");
                            }
                        }
                        else {
                            echo("
                                    <tr>
                                        <td colspan='9'>No Results Found.</td>
                                    </tr>
                                ");
                        }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="9"><i><?php echo("$Orders->num_rows Results.") ?></i></td>
                    </tr>
                </tfoot>
            </table>

            <br>

            <button type="button" id="NewOrderButton" onclick="addOrder()">Add New Order</button>
        </section>
    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>