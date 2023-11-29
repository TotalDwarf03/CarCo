<?php 
    session_start(); 

    require_once("Scripts/DBConnect.php");
    require_once('Scripts/GeneralScripts.php');

    checkLoginPermissions(5);

    function GetBasketContents(){
        if(file_exists("basket.txt")){
            // Open basket.txt
            $basket = fopen("basket.txt", "r");

            if(filesize("basket.txt") > 0){
                // Get contents of basket.txt as a string
                $basketContents = fread($basket, filesize("basket.txt"));

                // Split contents into an array (i.e [Product1, Product2, Product3])
                $basketContents = explode(";", $basketContents);
                
                // Split each element into an array of [ProductID, QTY]
                foreach($basketContents as &$value){
                    $value = explode(",", $value);
                }

                // Remove last element in array due to final basket item ending with a ;
                array_pop($basketContents);

                // Final array structure is:
                //      [[ProductID1, QTY1]], [[ProductID2, QTY2]], [[ProductID3, QTY3]]

                // Close Text File
                fclose($basket);
            }
            else{
                $basketContents = false;
            }
        }
        else{
            $basketContents = false;
        }

        return($basketContents);
    }

    function GetProductInfo($db, $ProductID){
        // Get Product Name and Cost Per Unit
        $sql = "SELECT
                    sp.ProductName,
                    sp.CostPerUnit
                FROM tblSystemProduct sp
                WHERE sp.SystemProductID = $ProductID
                LIMIT 1";

        $result = mysqli_query($db, $sql);
        $ProductInfo = mysqli_fetch_assoc($result);

        return $ProductInfo;
    }

    if($_SERVER['REQUEST_METHOD'] == "GET"){
        // Get when adding to basket

        // Textfile called basket.txt contains ProductID's and QTY's
        // Each entity is in the format: 
        //      <ProductID>,<QTY>;

        if(isset($_GET['ProductID'])){
            // Get Inputed Product and QTY
            $ProductID = $_GET['ProductID'];
            $qty = $_GET['qty'];

            // Open basket.txt
            $basket = fopen("basket.txt", "a");

            // Add new Product and QTY to basket.txt
            fwrite($basket, "$ProductID,$qty;");

            // Close basket.txt
            fclose($basket);

            $basketContents = GetBasketContents();
        }
        elseif(isset($_GET['DeleteIndex'])){
            $basketContents = GetBasketContents();

            if($basketContents != false){
                $DeleteIndex = $_GET['DeleteIndex'];

                // Unset DeletedIndex
                unset($basketContents[$DeleteIndex]);

                // Reindex array
                $basketContents = array_values($basketContents);

                // Rewrite Array to basket.txt
                $basket = fopen("basket.txt", "w");
                foreach($basketContents as &$value){
                    fwrite($basket, "$value[0],$value[1];");
                }
                fclose($basket);

                // If basketContents Array is empty after changes, Unset it
                if(count($basketContents) == 0){
                    unset($basketContents);
                }
            }
            else{
                unset($basketContents);
            }
        }
        elseif(isset($_GET['UploadStatus'])){
            $basketContents = GetBasketContents();

            if($basketContents == false){
                unset($basketContents);
            }

        }
        else{
            // If not passed any values, new order so remove basket.txt to empty it
            if(file_exists("basket.txt")){
                unlink("basket.txt");
            }
        }

    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post when submitting order to DB

        // If Customer not set, no customer selected
        // Therefore redirect back to addorder.php
        if(!isset($_POST['Customer'])){
            $message = "Error: Please select a customer.";
            header("location: addorder.php?UploadStatus=$message");
        }
        
        // Get Posted Values
        $CustomerID = $_POST['Customer'];
        $DeliveryDate = $_POST['DeliveryDate'];

        // Get StaffID
        $StaffID = $_SESSION['UserID'];

        // Create Order Record in DB
        $sql = "INSERT INTO tblOrder (CustomerID, StaffID, DeliveryDate)
                VALUES ($CustomerID, $StaffID, '$DeliveryDate')";

        mysqli_query($db, $sql);

        // Get OrderID of the inserted order
        // (Most recent order with the same params and a TotalCost of 0) 
        $sql = "SELECT
                    o.OrderID
                FROM tblOrder o
                WHERE o.CustomerID = $CustomerID
                    AND o.StaffID = $StaffID
                    AND o.DeliveryDate = '$DeliveryDate'
                    AND o.TotalCost = 0
                ORDER BY o.CreationDate DESC
                LIMIT 1";

        $result = mysqli_query($db, $sql);
        $OrderID = mysqli_fetch_assoc($result)['OrderID'];

        // Create Order Products
        $basketContents = GetBasketContents();

        if($basketContents != false){
            $TotalCost = 0;

            // Create an Order Products Record for each Product in the Basket
            foreach($basketContents as &$value){
                $ProductID = $value[0];
                $qty = $value[1];

                $ProductInfo = GetProductInfo($db, $ProductID);

                $Subtotal = $qty * $ProductInfo['CostPerUnit'];
                $TotalCost += $Subtotal;

                $sql = "INSERT INTO tblOrderProducts (OrderID, SystemProductID, Quantity, Subtotal)
                        VALUES ($OrderID, $ProductID, $qty, $Subtotal)";

                mysqli_query($db, $sql);
            }

            // Update TotalCost on order
            $sql = "UPDATE tblOrder o
                    SET o.TotalCost = $TotalCost
                    WHERE o.OrderID = $OrderID
                    LIMIT 1";

            mysqli_query($db, $sql);

            // Return to vieworders.php
            $message = "Order has been placed.";
            header("location: vieworders.php?UploadStatus=$message&SearchText=$OrderID");
        }
        else{
            // Remove the Order Record from the DB
            $sql = "DELETE FROM tblOrder
                    WHERE OrderID = $OrderID
                    LIMIT 1";

            mysqli_query($db, $sql);

            // Return to addorder.php with error message
            $message = "Error: Basket is empty.";
            header("location: addorder.php?UploadStatus=$message");
        }

    }
    else{
        header("location: index.php");
    }

    // Get List of Customers
    $sqlCustomers = "   SELECT
                            c.CustomerID,
                            c.CustomerName
                        FROM tblCustomer c";
    
    $Customers = mysqli_query($db, $sqlCustomers);

    // Get List of Products (Excl. Out of Stock - ID = 2)
    $sqlProducts = "SELECT
                        sp.SystemProductID,
                        sp.ProductName
                    FROM tblSystemProduct sp
                    WHERE sp.SystemProductStatusID != 2";
    
    $Products = mysqli_query($db, $sqlProducts);
?>

<script>
    function ConfirmNew(){
        document.getElementById("submit").disabled = false;
        document.getElementById("Confirm").disabled = true;
    }

    function AddToBasket(){
        if(document.getElementById("Product").value != -1 && document.getElementById("qty").value != ''){
            ProductID = document.getElementById("Product").value;
            qty = document.getElementById("qty").value;

            document.location.replace(`addorder.php?ProductID=${ProductID}&qty=${qty}`);
        }
        else{
            message = "Error: Please select a product to add."
            document.location.replace(`addorder.php?UploadStatus=${message}`);
        }
    }

    function RemoveFromBasket(Index){
        document.location.replace(`addorder.php?DeleteIndex=${Index}`);
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Add Order</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <section id="NewOrder" style="width:50%; float:left;">
            <h2>Add New Order:</h2>

            <!-- Error / Success Message -->
            <p <?php echo(str_contains($_GET['UploadStatus'] ?? "", "Error") ? "class='error'" : "class='message'"); ?>><?php echo($_GET['UploadStatus'] ?? '') ?></p>

            <!-- New Order Form -->
            <form id="NewOrderForm" action="addorder.php" method="post">
                <fieldset class="inputs">
                    <legend><h3>Order Information:</h3></legend>

                    <!-- Customer Drop Down -->
                    <label for="Customer">Customer:</label>
                    <select id="Customer" name="Customer">
                        <optgroup label="Customer">
                            <option value=999 disabled selected hidden>-- Select a Customer --</option>

                            <?php
                                while($row = mysqli_fetch_assoc($Customers)){
                                    $CustomerID = $row['CustomerID'];
                                    $CustomerName = $row['CustomerName'];

                                    echo("
                                            <option value='$CustomerID'>$CustomerName</option>
                                        ");
                                }
                            ?>
                        </optgroup>
                    </select>

                    <!-- Delivery Date -->
                    <label for="DeliveryDate">Delivery Date:</label>
                    <input type="date" id="DeliveryDate" name="DeliveryDate" min="<?php echo date('Y-m-d'); ?>" required>
                </fieldset>

                <fieldset class="inputs">
                    <legend><h3>Add Product to Order:</h3></legend>

                    <!-- Product Drop Down -->
                    <label for="Product">Product:</label>
                    <select id="Product" name="Product" onchange="document.getElementById('qty').value = 1;">
                        <optgroup label="Product">
                            <option value="-1" disabled selected hidden>-- Select a Product --</option>

                            <?php
                                while($row = mysqli_fetch_assoc($Products)){
                                    $ProductID = $row['SystemProductID'];
                                    $ProductName = $row['ProductName'];

                                    echo("
                                            <option value='$ProductID'>$ProductName</option>
                                        ");
                                }
                            ?>
                        </optgroup>
                    </select>

                    <!-- Quantity -->
                    <label for="qty">Quantity:</label>
                    <input type="number" id="qty" name="qty" min=1>

                <br>

                <!-- Add to Basket -->
                <input type="button" id="addToBasket" name="addToBasket" value="Add to Basket" onclick="AddToBasket();">
                </fieldset>

                <fieldset>
                    <!-- confirm -->
                    <input type="button" id="Confirm" name="Confirm" value="Confirm" onclick="ConfirmNew()">
                    <!-- Submit -->
                    <input type="submit" id="submit" name="submit" value="Submit" disabled>
                    <!-- reset -->
                    <input type="button" id="cancel" name="cancel" value="Cancel" onclick="window.location.replace('vieworders.php');" style="float: right;">
                </fieldset>
            </form>
        </section>

        <section id="basket" style="width:50%; float:right;">
            <table>
                <caption><h2>Basket</h2></caption>

                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                        if(isset($basketContents)){
                            $index = 0;
                            $TotalCost = 0;
                            foreach($basketContents as &$value){
                                $ProductID = $value[0];
                                $qty = $value[1];

                                $ProductInfo = GetProductInfo($db, $ProductID);

                                $ProductName = $ProductInfo['ProductName'];
                                $Subtotal = $qty * $ProductInfo['CostPerUnit'];

                                $TotalCost += $Subtotal;
                                $Subtotal = "£" . number_format($Subtotal, 2);

                                echo("
                                        <tr>
                                            <td>$ProductName</td>
                                            <td>$qty</td>
                                            <td>$Subtotal</td>
                                            <td><button type='button' onclick='RemoveFromBasket($index)'>&#10006;</button></td>
                                        </tr>
                                    ");

                                $index++;
                            }

                            $TotalCost = number_format($TotalCost, 2);
                        }
                        else{
                            echo("  <tr>
                                        <td colspan='4'>Basket Empty</td>
                                    </tr>
                                ");
                        }
                    ?>
                </tbody>

                <!-- Hide tfoot if basket empty -->
                <tfoot <?php echo(isset($basketContents) ? "" : "style='display:none;'") ?>>
                    <tr>
                        <td colspan="2" style="text-align:right;"><b>Total:</b></td>
                        <td><b><?php echo("£$TotalCost"); ?></b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>